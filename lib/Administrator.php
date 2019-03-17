<?php
/**
 * Created by PhpStorm.
 * User: andychen
 * Date: 8/20/18
 * Time: 2:08 AM
 */

namespace Base17Mai;

class Administrator extends Base17mai
{

    private $adminData;

    public function __construct()
    {
        parent::__construct();
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['AdminData'])) {
            $adminData = array(
                'ID' => '',
                'name' => '',
                'identity' => '',
                'image' => ''
            );
            $_SESSION['AdminData'] = $adminData;
        }
        $this->adminData = &$_SESSION['AdminData'];
    }

    public function GetSysConfig()
    {
        $SQL = 'select * from sysconfig;';
        $rst = $this->PDOOperator($SQL);
        $result = array();
        foreach ($rst as $item) {
            $result[$item['ParameterName']] = $item['ParameterValue'];
        }
        return $result;
    }

    public function ajaxResetSys($get, $post)
    {
        $configs = $this->GetSysConfig();
        $javascript = '';
        foreach ($configs as $key => $item) {
            $javascript .= '$(\'input[name="' . $key . '"]\').val(\'' . $item . '\');';
        }
        $this->PAE(['javascript' => $javascript]);
    }

    public function ajaxSaveSys($get, $post)
    {
        $setting = ['threshold', 'angelValue', 'storeFee'];
        $javascript = '';
        foreach ($setting as $key => $value) {
            $check = $this->checkExistsDataInTable(['ParameterName' => $value], 'sysconfig');
            $Para = array(
                'ParameterName' => $value,
                'ParameterValue' => $post[$value],
            );
            if ($check) {
                $SQL = 'update sysconfig set ParameterValue = :ParameterValue where ParameterName = :ParameterName';
                $rst = $this->PDOOperator($SQL, $Para, Base17mai::DO_UPDATE);
            } else {
                $SQL = 'insert into sysconfig set ParameterName = :ParameterName, ParameterValue = :ParameterValue';
                $rst = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
            }
            if ($rst) $javascript .= 'showMessage(\'' . $value . '儲存成功\');';
            else $javascript .= 'showMessage(\'' . $value . '沒有變更\');';
        }
        if (true) $this->PAE(['javascript' => $javascript]);
    }

    private function getAllIdentity()
    {
        $result = array(
            'admin' => '管理員',
            'supplier' => '供應商',
            'store' => '行鏛經理'
        );
        return $result;
    }

    private function Login($account, $password)
    {
        $column = ['id', 'name', 'identity', 'img', 's_id'];
        $SQLcol = implode(',', $column);
        $SQL = "select {$SQLcol} from admin where account = :account and password = unhex(md5(:password));";
        $Para = ['account' => $account, 'password' => $password];
        $admin_arr = $this->PDOOperator($SQL, $Para, Base17mai::DO_SELECT);
        $result = isset($admin_arr[0]['id']);
        if ($result) $this->setupAdminData($admin_arr[0]);
        return $result;
    }

    private function Logout()
    {
        unset($_SESSION['AdminData']);
        // old setting
        unset($_SESSION['id']);
        unset($_SESSION['name']);
        unset($_SESSION['identity']);
        $this->PAE(array('javascript' => 'alert("登出成功");location.href="home.php?url=Login";'));
    }

    private function setupAdminData($adminData = array())
    {
        $this->adminData = array(
            'ID' => (isset($adminData['id'])) ? $adminData['id'] : '',
            'name' => (isset($adminData['name'])) ? $adminData['name'] : '',
            'identity' => (isset($adminData['identity'])) ? $adminData['identity'] : '',
            'image' => (isset($adminData['img'])) ? $adminData['img'] : '',
            'StaffID' => (isset($adminData['s_id'])) ? $adminData['s_id'] : ''
        );
        // old setting
        $_SESSION['AdminData'] = $this->adminData;
        $_SESSION['id'] = $adminData['id'];
        $_SESSION['name'] = $adminData['name'];
        $_SESSION['identity'] = $adminData['identity'];
    }

    public static function GetAdminID()
    {
        return isset($_SESSION['AdminData']['ID']) ? $_SESSION['AdminData']['ID'] : '';
    }

    public static function GetAdminName()
    {
        return isset($_SESSION['AdminData']['name']) ? $_SESSION['AdminData']['name'] : '';
    }

    public function checkLogin()
    {
        if (!empty($this->adminData['ID'])) {
            return true;
        } else {
            return false;
        }
    }

    public function getName()
    {
        $result = $this->adminData['name'];
        return $result;
    }

    public function getImage()
    {
        $result = $this->adminData['image'];
        return $result;
    }

    public function getIdentity()
    {
        $result = $this->adminData['identity'];
        return $result;
    }

    public function getIdentityStr()
    {
        $sample = $this->getAllIdentity();
        $index = $this->adminData['identity'];
        if ($index === '') return false;
        $result = $sample[$index];
        return $result;
    }

    public function ajaxLogin($get, $post)
    {
        $account = addslashes(POST('account', ''));
        $password = addslashes(POST('password', ''));
        $result = $this->Login($account, $password);
        if ($result) {
            $javascript = 'alert("登入成功");';
            $javascript .= 'window.location.href = "home.php";';
        } else {
            $javascript = 'alert("帳號或密碼錯誤");';
            $javascript .= '$("input[name!=\"btn\"]").val("");';
        }
        $output = ['javascript' => $javascript];
        $this->PAE($output);
    }

    public function ajaxLogout($get, $post)
    {
        $this->Logout();
        print_r($_SESSION);
    }

    public function GetAdminInformation($Columns = false, $Condition = false, $operator = 'and')
    {
        $columns = $Columns === false ? '*' : '';
        $columns = is_string($Columns) ? $Columns : $columns;
        $columns = $this->Check1DArray($Columns) ? implode(', ', $Columns) : $columns;
        $condition = $Condition === false ? 'true' : '';
        $condition = is_string($Condition) ? $Condition : $condition;
        $Para = array();
        if ($this->Check1DArray($Condition)) {
            $conditions = [];
            foreach ($Condition as $key => $value) {
                $Para[$key] = $value;
                $conditions[] = $key;
            }
            $condition = count($conditions) > 0 ? implode(' ' . $operator . ' ', $conditions) : $condition;
        }
        $SQL = 'select ' . $columns . ' from Admin where ' . $condition . ';';
        $rst = $this->PDOOperator($SQL, $Para);
        return $rst;
    }

}

?>