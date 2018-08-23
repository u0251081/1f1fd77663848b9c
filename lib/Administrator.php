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
        if (!isset($_SESSION['adminData'])) {
            $adminData = array(
                'ID' => '',
                'name' => '',
                'identity' => '',
                'image' => ''
            );
            $_SESSION['adminData'] = $adminData;
        }
        $this->adminData = &$_SESSION['adminData'];
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
        $column = ['id', 'name', 'identity', 'img'];
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
        unset($_SESSION['adminData']);
    }

    private function setupAdminData($adminData = array())
    {
        $this->adminData = array(
            'ID' => (isset($adminData['id'])) ? $adminData['id'] : '',
            'name' => (isset($adminData['name'])) ? $adminData['name'] : '',
            'identity' => (isset($adminData['identity'])) ? $adminData['identity'] : '',
            'image' => (isset($adminData['img'])) ? $adminData['img'] : ''
        );
        // old setting
        $_SESSION['id'] = $adminData['id'];
        $_SESSION['name'] = $adminData['name'];
        $_SESSION['identity'] = $adminData['identity'];
    }

    public function checkLogin() {
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
            $javascript .= '$("input").val("");';
        }
        $output = ['javascript' => $javascript];
        $this->PAE($output);
    }

    public function ajaxLogout($get, $post)
    {
        $this->Logout();
        print_r($_SESSION);
    }

}

?>