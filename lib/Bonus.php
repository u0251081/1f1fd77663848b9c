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

    public function CheckBonus($threshold = 0)
    {
        $SQL = 'select m_name, member_no, ReMonth,Amount,record_member.bonus from member left join record_member using(member_no) where parent_no = :member_no;';
        $Para['member_no'] = $this->MemberNO;
        $rst = $this->PDOOperator($SQL, $Para);
        foreach ($rst as $key => $value) {
            $rst[$key]['m_name'] = $this->MaskSecret($value['m_name']);
            $rst[$key]['ReMonth'] = substr($value['ReMonth'], 0, 7);
            $rst[$key]['member_no'] = $this->MaskSecret($value['member_no']);
        }
        $result['List'] = $rst;
        $SQL = 'select count(*) as Count, sum(Amount) as Amount, sum(record_member.bonus) as Bonus from member left join record_member using(member_no) where parent_no = :member_no and Amount >= :threshold;';
        $Para['threshold'] = $threshold;
        $rst = $this->PDOOperator($SQL, $Para);
        $result['Count'] = isset($rst[0]['Count']) ? $rst[0]['Count'] : '';
        $result['Amount'] = isset($rst[0]['Amount']) ? $rst[0]['Amount'] : '';
        $result['Bonus'] = isset($rst[0]['Bonus']) ? $rst[0]['Bonus'] : '';
        return $result;
    }

}