<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2019/3/13
 * Time: 下午 09:46
 */

namespace Base17Mai;

class Bonus extends Base17mai
{

    private $ManagerNO;
    private $threshold;  // 消費額低於這個數字的會員不列入計算
    private $angelValue; // 如果自身消費額超過這個數字，貢獻比率直接為 100%
    private $storeFee;   // 店家回饋 百分比(%)
    private $BonusDetail;

    public function __construct()
    {
        parent::__construct();
        $this->ManagerNO = Manager::GetManagerNO();
        if ($this->ManagerNO !== false) {
            $setting = $this->GetSetting();
            $this->threshold = $setting['threshold'];
            $this->angelValue = $setting['angelValue'];
            $this->storeFee = $setting['angelValue'];
        }
    }

    public function GetSetting()
    {
        $SQL = 'select * from sysconfig;';
        $rst = $this->PDOOperator($SQL);
        $result = array();
        foreach ($rst as $item) {
            $result[$item['ParameterName']] = $item['ParameterValue'];
        }
        return $result;
        # $result['angelValue'] = 20000; // 如果超過這個數字，消費額不增加
        # $result['threshold'] = 500;    // 如果低語着個數字，消費者不計算
        # $result['storeFee'] = 1;       // 店家要直接將營業額乘以這個數字處以100作為家長獎金
    }

    public function ValidMember($NOList = array())
    {
        if ($this->Check1DArray($NOList) === false) return $NOList;
        $NOString = '\'' . implode('\', \'', $NOList) . '\'';
        $SQL = 'select member_no from record_member where member_no in (' . $NOString . ') and Amount >= :threshold and ReMonth >= date_format(now(),\'%Y-%m-00\');';
        $Para = array(
            'threshold' => $this->threshold
        );
        $rst = $this->PDOOperator($SQL, $Para);
        $result = [];
        foreach ($rst as $item) {
            $result[] = isset($item['member_no']) ? $item['member_no'] : false;
        }
        return $result;
    }

    public function CalculateBonus($Amount, $NOList = array())
    {
        if ($this->Check1DArray($NOList) === false) return false;

        // 特別在明細上加入會員明細
        $CrewDetail = [];
        foreach ($NOList as $item) {
            $SQL = 'select Amount, bonus from record_member where member_no = :member_no and ReMonth >= date_format(now(), \'%Y-%m-0\');';
            $Para['member_no'] = $item;
            $rst = $this->PDOOperator($SQL, $Para);
            $CrewDetail[] = array(
                'member_no' => $item,
                'ReMonth' => date('Y-m-00'),
                'Amount' => isset($rst[0]) ? $rst[0]['Amount'] : 0,
                'bonus' => isset($rst[0]) ? $rst[0]['bonus'] : 0,
                'Valid' => isset($rst[0])
            );
        }

        $NOList = $this->ValidMember($NOList);
        $TotalAmount = $this->SumAllAmount($NOList);
        $TotalBonus = $this->SumAllBonus($NOList);
        $average = (count($NOList) === 0) ? 0 : $TotalAmount / count($NOList);
        $Ratio = ($average === 0) ? 0 : ($Amount / $average > 1 || $Amount > $this->angelValue) ? 1 : $Amount / $average;
        $result = $TotalBonus * $Ratio;
        $this->BonusDetail = array(
            'TotalAmount' => $TotalAmount,
            'TotalBonus' => $TotalBonus,
            'average' => $average,
            'ValidBonus' => $result,
            'COM' => count($NOList), // Count Of Member
            'Rate' => $Ratio
        );
        $this->BonusDetail['Detail'] = $CrewDetail;
        return $result;

    }

    public function SumModifyBonus($ManagerNO)
    {
        if (!is_string($ManagerNO) || !is_numeric($ManagerNO)) return false;
        else {
            $ManagerNO = addslashes($ManagerNO);
            $SQL = 'select sum(bonus) as modifyBonus from bonus_modify where ManagerNO = :manager_no and ModMonth = date_format( now(), \'%Y-%m-00\');';
            $rst = $this->PDOOperator($SQL, ['manager_no' => $ManagerNO]);
            $result = isset($rst[0]['modifyBonus']) ? (float)$rst[0]['modifyBonus'] : 0;
            return $result;
        }
    }

    public function SumAllAmount($NOList)
    {
        if ($this->Check1DArray($NOList) === false) return 0;
        $NOString = '\'' . implode('\', \'', $NOList) . '\'';
        $SQL = 'select sum(Amount) as totalAmount from record_member where member_no in (' . $NOString . ') and ReMonth >= date_format( now(), \'%Y-%m-00\');';
        $rst = $this->PDOOperator($SQL);
        $result = isset($rst[0]['totalAmount']) ? (float)$rst[0]['totalAmount'] : 0;
        return $result;
    }

    public function SumAllBonus($NOList)
    {
        if ($this->Check1DArray($NOList) === false) return 0;
        $NOString = '\'' . implode('\', \'', $NOList) . '\'';
        $SQL = 'select sum(Bonus) as totalBonus from record_member where member_no in (' . $NOString . ') and ReMonth >= date_format( now(), \'%Y-%m-00\');';
        $rst = $this->PDOOperator($SQL);
        $result = isset($rst[0]['totalBonus']) ? (float)$rst[0]['totalBonus'] : 0;
        return $result;
    }

    public function GetDetail()
    {
        return $this->BonusDetail;
    }

    public function ListBonusModify($manager_no = false)
    {
        $condition = $manager_no === false ? 'true' : 'ManagerNO = :manager_no';
        $SQL = 'select * from bonus_modify where ' . $condition . ';';
        $Para = $manager_no === false ? array() : ['manager_no' => $manager_no];
        $rst = $this->PDOOperator($SQL, $Para);
        $result = $rst;
        return $result;
    }

    private function BonusModify($Parameter)
    {
        $month = $Parameter['ModMonth'] . '-00';
        unset($Parameter['ModMonth']);
        $SQL = 'insert into bonus_modify set StaffID = :StaffID, ModMonth=\'' . $month . '\', ManagerNO = :ManagerNO, bonus = :bonus, reason = :reason;';
        $Para = $Parameter;
        $rst = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
        return $rst;
    }

    private function BonusModifyV($id)
    {
        if (!is_string($id)) return false;
        $SQL =
            'select a.id, ModMonth, a.bonus, reason, logtime, b.name as Admin, d.email as Manager from bonus_modify as a ' .
            'left join admin as b on b.s_id = a.StaffID ' .
            'left join seller_manager as c on c.manager_no = a.ManagerNO ' .
            'left join member as d on d.member_no = c.member_id ' .
            'where a.id = :id;';
        $Para = ['id' => $id];
        $rst = $this->PDOOperator($SQL, $Para);
        if (isset($rst[0])) $rst[0]['ModMonth'] = substr($rst[0]['ModMonth'], 0, -3);
        $rst = isset($rst[0]) ? $rst[0] : $rst;
        return $rst;
    }

    public function ajaxBonusModify($get, $post)
    {
        $javascript = '';
        // input check

        if ($this->BonusModifyInputCheck($post)) {
            $Parameter = array(
                'StaffID' => $_SESSION['AdminData']['StaffID'],
                'ModMonth' => $post['recordMonth'],
                'ManagerNO' => (int)$post['Manager'],
                'bonus' => (int)$post['bonus'],
                'reason' => $post['reason']
            );
            $result = (bool)$this->Bonusmodify($Parameter);
            $javascript .= 'showMessage("' . ($result ? '修改成功' : '修改失敗') . date('Y-m-d') . '");';
        }
        $string = print_r($post, true);
        /*
         * 如果要在 json 格式中放入含有換行字元等的資料
         * 要先 json_encode 後再去掉引號(")
         */
        $result = $this->BonusModifyInputCheck($post) ? 'true' : 'false';
        $string = json_encode($string);
        $string = substr($string, 1, -1);
        // $javascript .= 'showMessage("' . $result . $string . '");';
        $javascript .= 'console.log(data);';
        $this->PAE(array('javascript' => $javascript));
    }

    public function ajaxBonusModifyV($get, $post)
    {
        $javascript = '';
        /*
         * 如果要在 json 格式中放入含有換行字元等的資料
         * 要先 json_encode 後再去掉引號(")
         */
        $Modal = file_get_contents('admin/template/BonusModifyDetail.html');
        $string = json_encode($Modal);
        $string = substr($string, 1, -1);

        $id = addslashes($post['id']);
        $Data = $this->BonusModifyV($id);
        $string2 = json_encode(print_r($Data, true));
        $string2 = substr($string2, 1, -1);

        $javascript .= '$(\'body\').append("' . $string . '");';
        $javascript .= 'console.log("' . $string2 . '");';
        $javascript .= '$(\'#BonusModifyDetail\').find(\'#ModalTitle\').html(\'獎金調整 #' . $Data['id'] . '\');';
        $javascript .= '$(\'#BonusModifyDetail\').find(\'#recorder\').val(\'' . $Data['Admin'] . '\');';
        $javascript .= '$(\'#BonusModifyDetail\').find(\'#recordMonth\').val(\'' . $Data['ModMonth'] . '\');';
        $javascript .= '$(\'#BonusModifyDetail\').find(\'#Manager\').val(\'' . $Data['Manager'] . '\');';
        $javascript .= '$(\'#BonusModifyDetail\').find(\'#bonus\').val(\'' . $Data['bonus'] . '\');';
        $javascript .= '$(\'#BonusModifyDetail\').find(\'#reason\').val(\'' . $Data['reason'] . '\');';
        $javascript .= '$(\'#BonusModifyDetail\').find(\'#footerContent\').html(\'登錄時間： ' . $Data['logtime'] . '\');';
        // $javascript .= '$(\'#BonusModifyDetail\').find(\'#recorder\').val(\'獎金調整 #' . $Data['admin'] . '\');';
        $javascript .= '$(\'#BonusModifyDetail\').on(\'hidden.bs.modal\', function (e) { $(this).remove(); });';
        $javascript .= '$(\'#BonusModifyDetail\').modal();';
        $javascript .= 'console.log(data);';
        $this->PAE(array('javascript' => $javascript));
    }

    private function BonusModifyInputCheck($input)
    {
        $StaffIDStatus = isset($_SESSION['AdminData']['StaffID']);
        $dateStatus = isset($input['recordMonth']) && (bool)preg_match('/\d{4}-\d{2}/', $input['recordMonth']);
        $ManagerNOStatus = isset($input['Manager']);
        $bonusStatus = isset($input['bonus']) && (int)$input['bonus'] !== 0;
        $reasonStatus = isset($input['reason']);
        $result = $StaffIDStatus && $dateStatus && $ManagerNOStatus && $bonusStatus && $reasonStatus;
        return $result;
    }

}