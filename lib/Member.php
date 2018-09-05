<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2018/8/13
 * Time: 上午 12:48
 */

namespace Base17Mai;

require_once 'toolFunc.php';

use Base17Mai\Bank;
use function Base17Mai\take;

class Member extends Base17mai
{
    private $memberNO;

    public function __construct()
    {
        parent::__construct();
        if (isset($_SESSION['member_no'])) $this->memberNO = &$_SESSION['member_no'];
    }

    public function CreateNewMember($account = '', $password = '')
    {
        $result = false;
        $emailRule = '/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/';
        $passwordRule = '/(?=.{6,})(?=.*\d.*)(?=.*[A-z].*).*/';
        preg_match($emailRule, $account, $chkEmail);
        preg_match($passwordRule, $password, $chkPassword);
        $emailDuplicate = $this->checkEmailDuplicate($account);
        if (!empty($chkEmail) && !empty($chkPassword) && !$emailDuplicate) {
            $memberNO = $this->generateMemberNumber();
            $SQL = 'insert into member set email = :account, password = unhex(md5(:password)), member_no = :member_no,';
            $SQL .= ' registration_time = :createDate, identity = :identity, status = :status;';
            $Para = array(
                'account' => $account,
                'password' => $password,
                'member_no' => $memberNO,
                'createDate' => date('Y-m-d H:i:s'),
                'identity' => 'member',
                'status' => array(
                    'PARAM_TYPE' => Base17mai::PDO_PARSE_INT,
                    'VALUE' => 1
                )
            );
            $result = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
        } else {
            if (empty($chkEmail)) $result = 001;
            if ($emailDuplicate) $result = 002;
            if (empty($chkPassword)) $result = 003;
        }
        return $result;
    }

    private function generateMemberNumber()
    {
        $member_no = '';
        do {
            for ($i = 1; $i <= 10; $i++) {
                $num = rand(0, 9);
                $member_no .= $num;
            }
        } while ($member_no[0] === '0' || !$this->checkMemberNoRepeat($member_no));
        return $member_no;
    }

    private function checkMemberNoRepeat($member_no)
    {
        $Table = 'member';
        $Parameter = ['member_no' => $member_no];
        $result = $this->checkExistsDataInTable($Parameter, $Table);
        return !$result;
    }

    private function checkFavorite($member_no, $productID)
    {
        $para = ['member_no' => $member_no, 'productID' => $productID];
        $table = 'producttrack';
        $rst = $this->checkExistsDataInTable($para, $table);
        return $rst;
    }

    private function removeFromTrack($member_no, $productID)
    {
        $SQL = "delete from producttrack where member_no = '{$member_no}' and productID = '{$productID}';";
        $result = $this->PDOOperator($SQL, [], Base17mai::DO_DELETE);
        return $result;
    }

    private function addToTrack($member_no, $productID)
    {
        $SQL = "insert into producttrack set member_no = :member_no, productID = :productID;";
        $para = ['member_no' => $member_no, 'productID' => $productID];
        $result = $this->PDOOperator($SQL, $para, Base17mai::DO_INSERT_NORMAL);
        return $result;
    }

    public function ListMember($column = ['Member_no', 'M_name'], $condition = ['status' => '1'])
    {
        $column_sql = strtolower(implode(',', $column));
        $condition_sql = ' true';
        $para = Array();
        if (is_array($condition)) {
            foreach ($condition as $k => $v) {
                $condition_sql .= ' and ' . $k . ' = :' . $k;
                $para[$k] = $v;
            }
        }
        $sql = "select {$column_sql} from member where {$condition_sql};";
        $result = $this->PDOOperator($sql, $para);
        return $result;
    }

    public function getMemberProfile($MemberNO = false)
    {
        // 預設
        $result = array(
            'email' => '',
            'm_name' => '',
            'born' => '',
            'id_card' => '',
            'bank_id' => '',
            'bank_no' => '',
            'parent_no' => '',
            'parent_name' => '',
            'gender' => '',
            'city_id' => '',
            'area_id' => '',
            'address' => '',
            'cellphone' => ''
        );
        if ($MemberNO !== false) {
            $fetchColumn = array(
                'email',
                'm_name',
                'born',
                'id_card',
                'bank_id',
                'bank_no',
                'parent_no',
                'gender',
                'city_id',
                'area_id',
                'address',
                'cellphone'
            );
            $SQLColumn = implode(',', $fetchColumn);
            $SQL = "select  'secret' as password,{$SQLColumn} from member where member_no = :memberNO;";
            $Para = ['memberNO' => $MemberNO];
            $arr = $this->PDOOperator($SQL, $Para, Base17mai::DO_SELECT);
            $result = (!empty($arr[0])) ? $arr[0] : $result;
            if (!empty($result['parent_no'])) $result['parent_name'] = $this->getMemberName($result['parent_no']);
        } else {
        }
        return $result;
    }

    public function getAllGender()
    {
        $result = array(
            '0' => '法人',
            '1' => '先生',
            '2' => '女士',
            // '3' => '非二元性別'
        );
        return $result;
    }

    public function getAllCity()
    {
        $SQL = "select * from city";
        $result = $this->PDOOperator($SQL);
        return $result;
    }

    public function getSpecifyArea($targetID = false)
    {
        if ($targetID === false) return false;
        $SQL = "select * from area where city_id='{$targetID}';";
        $result = $this->PDOOperator($SQL);
        return $result;
    }

    public function ajaxChangeCity($get, $post)
    {
        $targetID = $post['targetID'];
        $html = '';
        $areaList = $this->getSpecifyArea($targetID);
        $html .= "<option value=\"NaN\" selected>請選擇地區</option>";
        foreach ($areaList as $key => $value) {
            $selected = ($value['id'] === '') ? 'selected' : '';
            $html .= "<option value=\"{$value['id']}\" {$selected}>{$value['area']}</option>";
        }
        $javascript = "$('#area_id').html('{$html}');";
        $this->PAE(array('javascript' => $javascript));
    }

    private function getMemberName($MemberNO = false)
    {
        if ($MemberNO === false) $result = 'no this member';
        else {
            $sql = "select m_name from member where member_no = '{$MemberNO}';";
            $result = $this->PDOOperator($sql);
            $result = isset($result[0]['m_name']) ? $result[0]['m_name'] : 'no this member';
        }
        return $result;
    }

    private function getManagerName($MemberNO = false)
    {
        if ($MemberNO === false) $result = '沒有家長是這個編號';
        else {
            $sql = "select m_name from member left join seller_manager on member_no = member_id where member_no = '{$MemberNO}' and apply_status = '1';";
            $result = $this->PDOOperator($sql);
            $result = isset($result[0]['m_name']) ? $result[0]['m_name'] : '沒有家長是這個編號';
        }
        return $result;
    }

    private function checkInput(&$receive = false)
    {
        if ($receive === false) {
            return false;
        } else {
            foreach ($receive as $key => $value) {
                $receive[$key] = addslashes($value);
            }

            // check born input
            if ($this->checkNoneFilled('born')) {
                if (empty($receive['born'])) {
                    return 300;
                } else {
                    preg_match('/^\d{4}-\d{2}-\d{2}$/', $receive['born'], $result);
                    if (empty($result)) return 301;
                }
            }

            // check id_card input
            if ($this->checkNoneFilled('id_card')) {
                if (empty($receive['id_card'])) {
                    // it could be empty
                } else {
                    $idCode = $receive['id_card'];
                    $natural = $this->checkIDNumber($idCode);
                    $juristic = $this->checkGUINumber($idCode);
                    $repeat = $this->checkExistsDataInTable(['id_card' => $receive['id_card']], 'member');
                    if (!$natural && !$juristic) return 401;
                    if ($repeat) return 402;
                    if ($natural) $receive['mType'] = '1';
                    if ($juristic) $receive['mType'] = '0';
                }
            }
            // check parent Exists
            if ($this->checkNoneFilled('parent_number')) {
                if (empty($receive['parent_number'])) {
                    // doing nothing cause it could be empty
                } else {
                    $parentNO = $receive['parent_number'];
                    $memberExists = $this->checkManagerExists($parentNO);
                    if (!$memberExists) return 601;
                    if ($receive['parent_number'] === $this->memberNO) return 602;
                }
            }

            // check gender Valid
            if ($this->checkNoneFilled('gender')) {
                if (strlen($receive['gender']) < 1 or $receive['gender'] === 'NaN') {
                    unset($receive['gender']);
                    // return 900; it could be null
                } else {
                    if ($receive['gender'] < 0 or $receive['gender'] > 3) {
                        return 901;
                    }
                    if (!isset($receive['mType'])) return 902;
                    if ($receive['mType'] === '1' && $receive['gender'] !== $receive['id_card'][1]) return 903;
                    if ($receive['mType'] === '0' && $receive['gender'] !== '0') return 903;
                }
            }

            // check password format
            if (!empty($receive['password'])) {
                $password = $receive['password'];
                if (strlen($password) < 6) {
                    return 100;
                }
                preg_match('/\d+/', $password, $digit);
                preg_match('/\D+/', $password, $alpha);
                if (empty($digit) || empty($alpha)) {
                    return 101;
                }
            }

            // check name format
            if (empty($receive['m_name'])) {
                return 200;
            } else {
                if (strlen($receive['m_name']) < 2) return 201;
            }

            // check BankID and BankNO
            if (isset($receive['bank_id']) and isset($receive['bank_no'])) {
                if (empty($receive['bank_id']) or empty($receive['bank_no']) or $receive['bank_id'] === 'NaN') {
                    // doing no thing cause bank information could be empty for mobile user
                } else {
                    $bankID = $receive['bank_id'];
                    $bankNO = $receive['bank_no'];
                    $bankExists = $this->checkBankIDExists($bankID);
                    $bankNOValid = $this->checkBankNO($bankNO);
                    if (!$bankExists or !$bankNOValid) return 501;
                }
            }

            // check phone format
            if (empty($receive['cellphone'])) {
                return 700;
            } else {
                $receive['cellphone'] = preg_replace('/\W/', '', $receive['cellphone']);
                preg_match('/^\d{9,10}$/', $receive['cellphone'], $chk);
                if (empty($chk)) {
                    return 701;
                }
            }

            // check address
            if ($receive['city'] === 'NaN' || $receive['area'] === 'NaN' || empty($receive['address'])) {
                return 800;
            } else {
                preg_match('/\S{8,}[路街巷弄].*號.*/', $receive['address'], $chk);
                if (empty($chk)) {
                    return 801;
                }
            }
        }
        return true;
    }

    public function ajaxUpdateProfile($get, $post)
    {
        $javascript = '';
        $checkInput = $this->checkInput($post);
        if ($checkInput === true) {
            $updateStatus = $this->UpdateMember($post);
            if ($updateStatus === true) {
                $javascript .= 'alert("更新成功");';
            } else {
                $javascript .= 'alert("沒有更新");';
            }
        } else {
            switch ($checkInput) {
                case 200:
                    $message = '姓名為必填';
                    break;
                case 201:
                    $message = '姓名必須兩個字以上';
                    break;
                case 402:
                    $message = '身份證字號已申請過';
                    break;
                case 500:
                    $message = '銀行代碼必須選';
                    break;
                case 501:
                    $message = '銀行帳號格式錯誤';
                    break;
                case 902:
                    $message = '請提供統一編號以驗證性別';
                    break;
                case 903:
                    $message = '統一編號與性別不符，請再次確認';
                    break;
                default:
                    $message = '有地方出錯了' . $checkInput;
                    break;
            }
            $javascript .= 'showMessage("' . $message . '");';
        }
        $javascript .= 'console.log(data);';
        $this->PAE(array('javascript' => $javascript, 'checkInput' => $checkInput));
    }

    private function checkNoneFilled($column = false)
    {
        if ($column !== false) {
            switch ($column) {
                case 'parent_number':
                    $column = 'parent_no';
                    break;
                default:
                    break;
            }
            $SQL = "select {$column} from member where member_no = '{$this->memberNO}'";
            $result = $this->PDOOperator($SQL);
            $check = (strlen($result[0][$column]) < 1);
            return $check;
        }
    }

    private function checkIDNumber($idCode)
    {
        $tab = "ABCDEFGHJKLMNPQRSTUVXYWZIO";
        $A1 = Array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3);
        $A2 = Array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3, 4, 5);
        $Mx = Array(9, 8, 7, 6, 5, 4, 3, 2, 1, 1);
        $sum = 0;

        if (strlen($idCode) !== 10) return false;
        $i = strpos($tab, $idCode[0]);
        if ($i === false) return false;
        $sum += $A1[$i] + $A2[$i] * 9;

        for ($i = 1; $i < 10; $i++) {
            preg_match('/\d/', $idCode[$i], $chk);
            if (empty($chk)) return false;
            $c = (int)$chk[0];
            $sum += $c * $Mx[$i];
        }
        if ($sum % 10 != 0) return false;
        return true;
    }

    private function checkGUINumber($idCode)
    {
        $invalidList = "00000000,11111111";
        preg_match('/^(?!0{8})(?!1{8})(\d{8})$/', $idCode, $chk);
        if (empty($chk)) return false;
        $validateOperator = [1, 2, 1, 2, 1, 2, 4, 1];
        $sum = 0;
        function calculate($product)
        {
            // 個位數 + 十位數
            $ones = $product % 10;
            $tens = ($product - $ones) / 10;
            return $ones + $tens;
        }

        for ($i = 0; $i < count($validateOperator); $i++)
            $sum += calculate($idCode[$i] * $validateOperator[$i]);
        if ($sum % 10 === 0) return true;
        if ($idCode[6] === '7' && ($sum + 1) % 10 === 0) return true;
        return false;
    }

    private function checkBankIDExists($BankID)
    {
        $bank = new Bank();
        $result = $bank->checkBankIDExists($BankID);
        return $result;
    }

    private function checkBankNO($BankNO)
    {
        $processed = preg_replace('/\W/', '', $BankNO);
        preg_match('/\d{12,14}/', $processed, $chk);
        if (empty($chk)) return false;
        // check bank number no repeat
        return true;
    }

    private function checkBankNumberNoDuplicate($BankNO)
    {
    }

    private function checkManagerExists($MemberNO)
    {
        $para = ['member_id' => $MemberNO, 'apply_status' => '1'];
        $table = 'seller_manager';
        $result = $this->checkExistsDataInTable($para, $table);
        return $result;
    }

    private function checkEmailDuplicate($targetEmail = false)
    {
        if ($targetEmail === false) {
            return false;
        } else {
            $targetEmail = addslashes($targetEmail);
            $Parameter = ['email' => $targetEmail];
            $Table = 'member';
            $result = $this->checkExistsDataInTable($Parameter, $Table);
            return $result;
        }
    }

    private function Login($account = false, $password = false)
    {
        $sql = "select member_no from member where email = :account and password = unhex(md5(:password)) and status = '1';";
        $para = ['account' => $account, 'password' => $password];
        $member_arr = $this->PDOOperator($sql, $para, Base17mai::DO_SELECT);
        $member_no = isset($member_arr[0]['member_no']) ? $member_arr[0]['member_no'] : null;
        if (!is_null($member_no)) {
            $this->setupMemberInformation($member_no);
            $this->setupManagerInformation($member_no);
            return true;
        } else {
            return false;
        }
    }

    private function Logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            $notUnset = ['device'];
            foreach ($_SESSION as $key => $value) {
                if (!in_array($key, $notUnset)) unset($_SESSION[$key]);
            }
        }
        return true;
    }

    private function setupMemberInformation($MemberNO = false)
    {
        if ($MemberNO === false) return $MemberNO;
        $sql = "select id, member_no from member where member_no = '{$MemberNO}';";
        $para = [];
        $member_arr = $this->PDOOperator($sql, $para, Base17mai::DO_SELECT);
        $member_info = isset($member_arr[0]) ? $member_arr[0] : null;
        if (!is_null($member_info)) {
            $_SESSION['front_id'] = $member_info['id']; //會員表的id
            $_SESSION['front_identity'] = 'member'; //身分
            $_SESSION["member_no"] = $member_info['member_no']; //會員編號
            // $_SESSION['device'] = 'desktop'; //判斷登入的裝置
        }
        return true;
    }

    private function setupManagerInformation($MemberNO = false)
    {
        $sql = "select manager_no from seller_manager where member_id = :member_id and manager_status = '1';";
        $para = ['member_id' => $MemberNO];
        $manager_arr = $this->PDOOperator($sql, $para, Base17mai::DO_SELECT);
        $manager_info = isset($manager_arr[0]) ? $manager_arr[0] : null;
        $_SESSION['manager_no'] = isset($manager_info['manager_no']) ? $manager_info['manager_no'] : ''; //如果是行銷經理會有行銷經理編號
    }

    public function DeleteMember($MemberNO)
    {

    }

    private function UpdateMember($config = Array())
    {
        $SQLPara = array();
        $normalColumns = ['password', 'm_name', 'bank_id', 'bank_no', 'city', 'area', 'address', 'cellphone'];
        $onceColumns = ['born', 'id_card', 'parent_number', 'gender', 'mType'];
        $specialPara = array('password', 'bank_id', 'bank_no', 'mType', 'gender', 'parent_number');
        foreach ($onceColumns as $key => $value) {
            if ($this->checkNoneFilled($value) && isset($config[$value]) && strlen($config[$value]) > 0) {
                if (!in_array($value, $specialPara)) $SQLPara[$value] = $config[$value];
                if ($value === 'mType' || $value === 'gender') {
                    $SQLPara[$value]['PARAM_TYPE'] = Base17mai::PDO_PARSE_INT;
                    $SQLPara[$value]['VALUE'] = (int)$config[$value];
                }
                if ($value === 'parent_number') $SQLPara['parent_no'] = $config[$value];
            }
        }
        foreach ($normalColumns as $key => $value) {
            $isSpecial = in_array($value, $specialPara);
            // if ($value === 'password' && strlen($config[$value]) > 0) $SQLPara[$value] = $config[$value];
            if ($isSpecial && isset($config[$value]) && strlen($config[$value]) > 0) $SQLPara[$value] = $config[$value];
            if (!$isSpecial) $SQLPara[$value] = $config[$value];
        }
        $tmpColumns = array();
        foreach ($SQLPara as $key => $value) {
            if ($key !== 'city' && $key !== 'area' && $key !== 'password')
                $tmpColumns[] = $key . ' = :' . $key;
            if ($key === 'city' || $key === 'area')
                $tmpColumns[] = $key . '_id = :' . $key;
            if ($key === 'password')
                $tmpColumns[] = $key . ' = unhex(md5(:' . $key . '))';
        }
        $setupSQL = implode(', ', $tmpColumns);
        unset($tmpColumns);
        $SQL = "update member set {$setupSQL} where member_no = :member_no;";
        $SQLPara['member_no'] = $this->memberNO;
        $result = $this->PDOOperator($SQL, $SQLPara, Base17mai::DO_UPDATE);
        return $result;
    }

    private function UpdateIMEI($imei = '', $regid = '')
    {
        preg_match('/^\d{15}$/', $imei, $chkIMEI);
        $imeiNoneSet = $this->checkNoneFilled('imei');
        /*
         * imei 欄位用作 APP 判斷是否有登入
         * 如手機 imei 不同則自動判定為登出
         * 避免在新手機登入，舊手機也處於登入狀態
         */
        $imeiNoneSet = true;
        if (!empty($chkIMEI) && $imeiNoneSet || $imei === '') {
            $SQL = 'update member set imei = :imei, reg_id = :reg_id where member_no = :member_no;';
            $Para = array(
                'imei' => $imei,
                'reg_id' => $regid,
                'member_no' => $this->memberNO
            );
            $result = $this->PDOOperator($SQL, $Para, Base17mai::DO_UPDATE);
        } else {
            $result = false;
        }
        return $result;
    }

    private function AddToCart($member_id, $productID, $Quantity)
    {
        $status = $this->checkCartStatus($mid, $pid);
        if ($status) {
            $SQL = 'update shoppingcart set Quantity = Quantity + :Quantity where member_no = :member_id and productID = :productID;';
            $para = ['member_id' => $mid, 'productID' => $pid, 'Quantity' => ['PARAM_TYPE' => Base17mai::PDO_PARSE_INT, 'VALUE' => $Quantity]];
            $result = $this->PDOOperator($SQL, $para, Base17mai::DO_UPDATE);
        } else {
            $SQL = 'insert into shoppingcart set member_no = :member_id, productID = :productID, Quantity = :Quantity;';
            $para = ['member_id' => $mid, 'productID' => $pid, 'Quantity' => ['PARAM_TYPE' => Base17mai::PDO_PARSE_INT, 'VALUE' => $Quantity]];
            $result = $this->PDOOperator($SQL, $para, Base17mai::DO_INSERT_NORMAL);
        }
    }

    public function ajaxCreateAccount($get, $post)
    {
        $account = addslashes($post['account']);
        $password = addslashes($post['password']);
        $javascript = '';
        $result = $this->CreateNewMember($account, $password);
        if (($result === true) && isset($post['imei']) && strlen($post['imei']) === 15)
            $this->UpdateIMEI($post['imei'], $post['regid']);
        if ($result === true) {
            $javascript .= 'showMessage("註冊成功");';
            $javascript .= 'ajax17mai("Member","Login",{},{account:"' . $account . '",password:"' . $password . '"});';
        } else {
            switch ($result) {
                case 001:
                    $javascript .= 'showMessage("E-mail 格式不符!!!");';
                    break;
                case 002:
                    $javascript .= 'showMessage("E-mail 已經被申請!!!");';
                    break;
                case 003:
                    $javascript .= 'showMessage("密碼格式不符!!!");';
                    break;
                default:
                    $javascript .= 'showMessage("有東西出錯了!!!");';
                    break;
            }
        }
        $this->PAE(['javascript' => $javascript]);
    }

    public function ajaxLogin($get, $post)
    {
        $javascript = '';
        $account = addslashes($post['account']);
        $password = addslashes($post['password']);
        $result = $this->Login($account, $password);
        if ($result && isset($post['imei']) && strlen($post['imei']) === 15)
            $this->UpdateIMEI($post['imei'], $post['regid']);
        if ($result) {
            $javascript .= '$(\'#MemberHint\').hide(1000);';
            $javascript .= 'if (typeof $(\'#login-modal\').modal !== \'undefined\') $(\'#login-modal\').modal(\'hide\');';
            $javascript .= 'showMessage(\'登入成功\');';
            $javascript .= 'location.href = \'index.php\';';
        } else {
            $javascript .= '$(\'#MemberHint\').show(1000);';
            $javascript .= 'showMessage(\'帳號或密碼錯誤\');';
        }
        $output = array('javascript' => $javascript);
        $this->PAE($output);
    }

    public function ajaxLogout()
    {
        if ($this->Logout()) {
            $javascript = 'showMessage("登出成功");';
            $javascript .= 'window.location.href="index.php";';
            $this->PAE(array('javascript' => $javascript));
        }
    }

    public function ajaxGetMemberName($get, $post)
    {
        $javascript = '';
        if (isset($post['targetID'])) {
            $MName = $this->getManagerName(addslashes($post['targetID']));
            $javascript = "$(\"input#parent_name\").val(\"{$MName}\");";
        }
        $this->PAE(array('javascript' => $javascript));
    }

    public function ajaxEmailDuplicate($get, $post)
    {
        if (isset($post['Email'])) $result = $this->checkEmailDuplicate($post['Email']);
        if ($result === true) $this->PAE(['javascript' => 'alert("電子郵件重複");$("#Email").val("");']);
    }

    public function ajaxFavorite($get, $post)
    {
        $mid = addslashes($post['memberNO']);
        $pid = addslashes($post['productID']);
        if ($mid === '') $this->PAE(['javascript' => 'showMessage("請先登入方可使用本功能");']);
        $status = $this->checkFavorite($mid, $pid);
        if ($status) {
            $rst = $this->removeFromTrack($mid, $pid);
            if ($rst) $this->PAE(['javascript' => 'showMessage("取消追蹤");$("a#fav_btn' . $pid . '").find("img").attr("src","img/icon/clean.png");']);
        } else {
            $rst = $this->addToTrack($mid, $pid);
            if ($rst) $this->PAE(['javascript' => 'showMessage("完成追蹤");$("a#fav_btn' . $pid . '").find("img").attr("src","img/icon/add.png");']);
        }
    }

    public function ajaxRemoveTrack($get, $post)
    {
        $member_no = addslashes(take('member_no', '', 'session'));
        $productID = addslashes(take('productID', '', 'post'));
        $result = $this->removeFromTrack($member_no, $productID);
        if ($result) $this->PAE(['javascript' => 'showMessage("成功取消追蹤"); $(\'a#' . $productID . '\').closest(\'tr\').remove();']);
    }

    public function checkTrackStatus($mid, $pid)
    {
        $para = ['member_no' => $mid, 'productID' => $pid];
        $table = 'producttrack';
        $rst = $this->checkExistsDataInTable($para, $table);
        $result = "<a id=\"fav_btn{$pid}\" href=\"javascript:void(0);\" onclick=\"favorite({$pid});\">";
        if ($rst) {
            $result .= "<img src=\"img/icon/add.png\">";
        } else {
            $result .= "<img src=\"img/icon/clean.png\">";
        }
        $result .= "</a>";
        return $result;
    }

    public function ajaxAddToCart($get, $post)
    {
        $mid = addslashes($post['member_id']);
        $pid = addslashes($post['productID']);
        $Quantity = addslashes($post['Quantity']);
        if ($mid === '') $this->PAE(['javascript' => 'showMessage("請先登入方可使用本功能");']);
        $result = $this->AddToCart($mid, $pid, $Quantity);
        if ($result) $this->PAE(['javascript' => 'showMessage("成功加入購物車");']);
        else $this->PAE(['javascript' => 'showMessage("加入購物車失敗");']);
    }

    public function ajaxMobileLogout()
    {
        $this->UpdateIMEI();
        $javascript = 'showMessage("成功登出");';
        $javascript .= 'location.href="login.htmlk";';
        $this->PAE(['javascript' => $javascript]);
    }

    private function checkCartStatus($mid, $pid)
    {
        $para = ['productID' => $pid, 'member_no' => $mid];
        $table = 'shoppingcart';
        $rst = $this->checkExistsDataInTable($para, $table);
        return $rst;
    }

    public function listCart($member_id = '')
    {
        $SQL = '';
        $SQL .= 'select a.productID as id, PName, unitPrice, Quantity , Prelease from shoppingcart as a';
        $SQL .= ' left join product as b on a.productID = b.id where member_no = :member_id;';
        $para = ['member_id' => addslashes($member_id)];
        $rst = $this->PDOOperator($SQL, $para);
        print_r($rst);
        if (!isset($rst[0])) return null;
        foreach ($rst as $key => $value) {
            $rst[$key]['Prelease'] = ($rst[$key]['Prelease'] === '1') ? '上架' : '下架';
        }
        return $rst;
    }

    public function GetRecord($member_no, $startDate = '', $endDate = '')
    {
        $now = date('Y-m') . '-00';
        if ($startDate < $endDate) {
            $tmp = $startDate;
            $startDate = $endDate;
            $endDate = $tmp;
        }
        $SQL = "select * from record_member where member_no = :member_no and ReMonth >= :startMonth and ReMonth <= :endMonth;";
        $Para['member_no'] = $member_no;
        $Para['startMonth'] = $startDate === '' ? $now : $startDate;
        $Para['endMonth'] = $endDate === '' ? $now : $endDate;
        $rst = $this->PDOOperator($SQL, $Para, Base17mai::DO_SELECT);
        return $rst;
    }

}

?>