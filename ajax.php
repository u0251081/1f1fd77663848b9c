<?php
session_start();
include_once 'admin/mysql.php';
sql();
@$type = $_POST['type'];

switch ($type) {
    case 'checkname':
        checkname();
        break;

    case 'login':
        login();
        break;

    case 'fblogin':
        fblogin();
        break;

    case 'favorite':
        favorite();
        break;

    case 'favorite_search':
        favorite_search();
        break;

    case 'cart':
        cart();
        break;

    case 'cart_search':
        cart_search();
        break;

    case 'p_detial_cart':
        p_detial_cart();
        break;

    case 'p_detial_favorite':
        p_detial_favorite();
        break;

    case 'update_cart':
        update_cart();
        break;

    case 'set_regid':
        set_regid();
        break;

    case 'mobile_login':
        mobile_login();
        break;

    case 'mobile_reg':
        mobile_reg();
        break;

    case 'remove_cart':
        remove_cart();
        break;

    case 'wish_to_cart':
        wish_to_cart();
        break;

    case 'lower_limit':
        lower_limit();
        break;

    case 'rem_mg':
        rem_mg();
        break;

    case 'convert_bonus':
        convert_bonus();
        break;

    case 'check_is_reg':
        check_is_reg();
        break;

    case 'update_use_bonus':
        update_use_bonus();
        break;

    case 'search_member_info':
        search_member_info();
        break;

    case 'member_update_order':
        member_update_order();
        break;

    case 'clean_order':
        clean_order(); //前台使用者取消訂單
        break;

    case 'fb_search':
        fb_search();
        break;
}

function checkname()
{
    $sql = "select m_name from member right join seller_manager on member_no = member_id";
    $sql_where = " where m_name = '" . $_POST['content'] . "'";
    $result = mysql_query($sql . $sql_where);
    if (mysql_num_rows($result) > 0) {
        echo json_encode(array('msg' => 'checked'));
    } else {
        echo json_encode(array('msg' => mysql_error()));
    }
}

function login()
{
    $account = addslashes($_POST['account']);
    $password = addslashes($_POST['password']);
    // $sql = "SELECT * FROM member WHERE email='$account' AND password='$password' AND `status`='1'";
    // 這裡無法使用 pdo 帶入參數, 因此使用普通 sql
    $sql = "select *,'secret' as password from member where email = '{$account}' and password = unhex(md5('{$password}')) and status = '1';";
    $member_arr = pdo_select_sql($sql);
    $member_info = isset($member_arr[0])? $member_arr[0]:null;
    if (!is_null($member_info)) {
        $sql = "select manager_no from seller_manager where member_id = :member_id and manager_status = '1';";
        $para = ['member_id' => $member_info['member_no']];
        $manager_arr = pdo_select_sql($sql, $para);
        $manager_info = isset($manager_arr[0])? $manager_arr[0]:null;


        $_SESSION['front_id'] = $member_info['id']; //會員表的id
        $_SESSION['front_identity'] = 'member'; //身分
        $_SESSION["member_no"] = $member_info['member_no']; //會員編號
        $_SESSION['manager_no'] = isset($manager_info['manager_no'])? $manager_info['manager_no']: ''; //如果是行銷經理會有行銷經理編號
        $_SESSION['device'] = 'desktop'; //判斷登入的裝置
        print 'success';
    } else {
        print 'fail';
    }
    exit();
}

function fblogin()
{
    $account = $_POST['account'];
    $fbid = $_POST['fbid'];
    $name = $_POST['name'];
    if ($account != "" && $fbid != "" && $name != "") {
        $sql = "SELECT * FROM member WHERE email='$account' AND fb_id='$fbid' AND m_name='$name'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);
        if ($row['id'] != "") {
            $sql2 = "SELECT manager_no FROM seller_manager WHERE member_id='" . $row['member_no'] . "' AND manager_status='1'";
            $res2 = mysql_query($sql2);
            $row2 = mysql_fetch_array($res2);
            $_SESSION['front_id'] = $row['id']; //會員表的id
            $_SESSION['front_identity'] = 'member'; //身分
            $_SESSION["member_no"] = $row['member_no']; //會員編號
            $_SESSION['manager_no'] = $row2['manager_no']; //如果是行銷經理會有行銷經理編號
            $_SESSION['device'] = 'desktop'; //判斷登入的裝置
            echo 1;
        }
        if ($row['id'] == "") {
            $member_no = '';
            for ($i = 1; $i <= 10; $i++) {
                $num = rand(1, 9);
                $member_no .= $num;
            }
            $password = "";
            for ($i = 1; $i <= 10; $i++) {
                $num2 = rand(1, 9);
                $password .= $num2;
            }
            $sql3 = "SELECT MAX(id) FROM member";
            $res3 = mysql_query($sql3);
            $row3 = mysql_fetch_array($res3);
            $id = $row3['MAX(id)'] + 1;
            $_SESSION['front_id'] = $id; //會員表的id
            $_SESSION['front_identity'] = 'member'; //身分
            $_SESSION["member_no"] = $num; //會員編號
            $_SESSION['device'] = 'desktop'; //判斷登入的裝置


            $sql3 = "INSERT INTO member(email,password,member_no,status,identity,m_name,fb_id,registration_time) VALUES('$account','$password','$member_no','1','member','$name','$fbid','" . date('Y-m-d H:i:s') . "')";
            mysql_query($sql3);
            echo 1;
        }
    } else {
        echo 0;
    }
}

function favorite()
{
    $pid = $_POST['pid'];
    if (isset($_SESSION["member_no"])) {
        $member_id = $_SESSION["member_no"];
    } else {
        $member_id = $_SESSION["fb_id"];
    }

    if (isset($member_id)) {
        $sql = "SELECT * FROM product_track WHERE p_id='$pid' AND m_id='" . $member_id . "'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);
        if ($row['id'] != "") {
            $sql = "DELETE FROM product_track WHERE p_id='$pid' AND m_id='" . $member_id . "'";
            mysql_query($sql);
            echo 0;
        } else {
            $sql = "INSERT INTO product_track SET p_id='$pid', m_id='" . $member_id . "', track_day='" . date('Y-m-d H:i:s') . "'";
            mysql_query($sql);
            echo 1;
        }
    } else {
        echo 2;
    }
}

function favorite_search()
{
    if (isset($_SESSION["member_no"])) {
        $member_id = $_SESSION["member_no"];
    } else {
        $member_id = $_SESSION["fb_id"];
    }

    if (isset($member_id)) {
        $sql = "SELECT * FROM product_track WHERE m_id='" . $member_id . "'";
        $res = mysql_query($sql);
        while ($row = mysql_fetch_array($res)) {
            echo $row['p_id'] . ",";
        }
    }
}

function cart()
{
    $pid = $_POST['pid'];
    if (isset($_SESSION["member_no"])) {
        $member_id = $_SESSION["member_no"];
    } else {
        $member_id = $_SESSION["fb_id"];
    }

    if (isset($member_id)) {
        $sql = "SELECT * FROM shoping_cart WHERE p_id='$pid' AND m_id='" . $member_id . "'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);
        if ($row['id'] != "") {
            $sql = "DELETE FROM shoping_cart WHERE p_id='$pid' AND m_id='" . $member_id . "'";
            mysql_query($sql);
            echo 0;
        } else {
            $sql = "INSERT INTO shoping_cart SET p_id='$pid', m_id='" . $member_id . "', p_qty='1', add_day='" . date('Y-m-d H:i:s') . "'";
            mysql_query($sql);
            echo 1;
        }
    } else {
        echo 2;
    }
}

function cart_search()
{
    if (isset($_SESSION["member_no"])) {
        $member_id = $_SESSION["member_no"];
    } else {
        $member_id = $_SESSION["fb_id"];
    }

    if (isset($member_id)) {
        $sql = "SELECT * FROM shoping_cart WHERE m_id='" . $member_id . "'";
        $res = mysql_query($sql);
        while ($row = mysql_fetch_array($res)) {
            echo $row['p_id'] . ",";
        }
    }
}

function p_detial_cart()
{
    if (isset($_SESSION["member_no"])) {
        @$member_id = $_SESSION["member_no"];
    } else {
        @$member_id = $_SESSION["fb_id"];
    }

    if (isset($member_id) && !empty($member_id)) {
        $pid = $_POST['pid'];
        $p_qty = $_POST['p_qty'];
        $p_price = $_POST['p_price'];
        $sql = "SELECT * FROM shoping_cart WHERE p_id='$pid' AND m_id='" . $member_id . "'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);
        if ($row['id'] != "") {
            $p_qty = $p_qty + $row['p_qty'];
            $update = "UPDATE shoping_cart SET p_price='$p_price', p_qty='$p_qty' WHERE id='" . $row['id'] . "' AND m_id='" . $member_id . "'";
            mysql_query($update);
            echo 1;
        } else {
            $insert = "INSERT INTO shoping_cart SET p_id='$pid', m_id='" . $member_id . "', p_price='$p_price', p_qty='$p_qty', add_day='" . date('Y-m-d H:i:s') . "'";
            mysql_query($insert);
            echo 1;
        }
    } else {
        echo 2;
    }
}

function p_detial_favorite()
{
    if (isset($_SESSION["member_no"])) {
        @$member_id = $_SESSION["member_no"];
    } else {
        @$member_id = $_SESSION["fb_id"];
    }

    if (isset($member_id) && !empty($member_id)) {
        $pid = $_POST['pid'];
        $sql = "SELECT * FROM product_track WHERE p_id='$pid' AND m_id='" . $member_id . "'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);
        if ($row['id'] != "") {
            echo 1;
        } else {
            $sql = "INSERT INTO product_track SET p_id='$pid', m_id='" . $member_id . "', track_day='" . date('Y-m-d H:i:s') . "'";
            mysql_query($sql);
            echo 2;
        }
    } else {
        echo 3;
    }
}

function update_cart()
{
    $pid_arr = $_POST['pid_arr'];
    $qty_arr = $_POST['qty_arr'];
    $price_arr = $_POST['price_arr'];
    if (isset($_SESSION["member_no"])) {
        $member_id = $_SESSION["member_no"];
    } else {
        $member_id = $_SESSION["fb_id"];
    }

    for ($i = 0; $i < count($pid_arr); $i++) {
        $sql = "UPDATE shoping_cart SET p_qty='$qty_arr[$i]', p_price='" . $price_arr[$i] . "' WHERE p_id='$pid_arr[$i]' AND m_id='" . $member_id . "'";
        mysql_query($sql);
    }
    echo 1;
}

function set_regid()
{
    @$fb_id = $_POST['fb_id'];
    @$regid = $_POST['regid'];
    $sql = "SELECT id FROM fb WHERE fb_id='$fb_id'";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    if ($row['id'] != "") {
        $sql = "UPDATE fb SET reg_id='$regid' WHERE fb_id='$fb_id'";
        mysql_query($sql);
    }
}

function mobile_login()
{
    $login_mail = $_POST['login_mail'];
    $login_password = $_POST['login_password'];
    @$imei = $_POST['imei'];
    @$regid = $_POST['regid'];
    if (isset($login_mail) && isset($login_password)) {
        $sql = "SELECT * FROM member WHERE email='$login_mail' AND password='$login_password'";
        $res = mysql_query($sql);
        @$row = mysql_fetch_array($res);
        if ($row['id'] != "") {
            $sql2 = "UPDATE member SET imei='$imei',reg_id='$regid' WHERE id='" . $row['id'] . "'";
            mysql_query($sql2);
            echo "index.php?url=load_info&type=set_login&imei=" . $imei . "";
        } else {
            echo '資料有誤，請重新檢查';
        }
    }
}

function mobile_reg()
{
    $member_no = "";
    for ($i = 1; $i <= 10; $i++) {
        $num = rand(1, 9);
        $member_no .= $num;
    }
    $reg_mail = $_POST['reg_mail'];
    $reg_password = $_POST['reg_password'];
    @$imei = $_POST['imei'];
    @$regid = $_POST['regid'];
    if (isset($reg_mail) && isset($reg_password)) {
        $sql = "INSERT INTO member SET email='$reg_mail', password='$reg_password', member_no='$member_no', `status`='1', `identity`='member', imei='$imei', reg_id='$regid', registration_time='" . date('Y-m-d H:i:s') . "'";
        mysql_query($sql);
        echo "index.php?url=load_info&type=set_login&imei=" . $imei . "";
    } else {
        echo '資料有誤，請重新檢查';
    }
}

function remove_cart()
{
    if (isset($_SESSION["member_no"])) {
        $member_id = $_SESSION["member_no"];
    } else {
        $member_id = $_SESSION["fb_id"];
    }

    @$pid = $_POST['pid'];
    if ($pid != "") {
        $sql = "DELETE FROM shoping_cart WHERE id='$pid' AND m_id='$member_id'";
        mysql_query($sql);
        if (mysql_affected_rows()) {
            echo 1;
        } else {
            echo 0;
        }
    }
}

function wish_to_cart()
{
    @$pid = $_POST['pid'];
    if (isset($_SESSION["member_no"])) {
        $member_id = $_SESSION["member_no"];
    } else {
        $member_id = $_SESSION["fb_id"];
    }

    if ($pid != "") {
        $sql = "SELECT * FROM shoping_cart WHERE p_id='$pid' AND m_id='$member_id'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);
        if ($row['id'] != "") {
            echo 1;
        } else {
            $sql = "INSERT INTO shoping_cart SET p_id='$pid', m_id='$member_id', p_qty='1', add_day='" . date('Y-m-d H:i:s') . "'";
            mysql_query($sql);
            echo 2;
        }
    }
}

function lower_limit()
{
    $qr_str = explode("/", $_POST['qr_str']); //QRcode內容(行銷經理ID)
    $m_id = $_POST['m_id']; //會員no
    if ($qr_str != "" && $m_id != "") {
        $sql = "SELECT * FROM lower_limit WHERE member_id='$m_id' AND manager_id='" . trim($qr_str[0]) . "'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);
        if ($row['id'] != "") {
            echo 0;
        } else {
            $sql = "INSERT INTO lower_limit SET member_id='$m_id', manager_id='" . trim($qr_str[0]) . "', mg_member_id='" . trim($qr_str[1]) . "', add_time='" . date('Y-m-d H:i:s') . "'";
            mysql_query($sql);
            echo 1;
        }
    }
}

function rem_mg()
{
    @$r_id = $_POST['r_id'];
    if (isset($_SESSION["member_no"])) {
        $member_id = $_SESSION["member_no"];
    } else {
        $member_id = $_SESSION["fb_id"];
    }

    if (isset($r_id)) {
        $sql = "DELETE FROM lower_limit WHERE member_id='$member_id' AND id='$r_id'";
        mysql_query($sql);
        if (mysql_affected_rows()) {
            echo 1;
        } else {
            echo 0;
        }
    }
}

function convert_bonus()
{
    if (isset($_SESSION["member_no"])) {
        $member_id = $_SESSION["member_no"];
    } else {
        $member_id = $_SESSION["fb_id"];
    }

    $id = $_POST['id'];
    $b_title = $_POST['b_title'];
    $b_price = $_POST['b_price'];

    if ($id != "" && $member_id != "") {
        if (strlen($member_id) > 10) {
            $sql = "SELECT * FROM fb WHERE fb_id='$member_id'";
            $res = mysql_query($sql);
            $row = mysql_fetch_array($res);
            if ($row['bonus'] != "" && $row['bonus'] >= $b_price) {
                $last_bonus = $row['bonus'] - $b_price;
                $insert = "INSERT INTO convert_info SET bonus_pid='$id', title='$b_title', bonus_price='$b_price', people='$member_id', is_use='0', convert_date='" . date('Y-m-d H:i:s') . "'";
                mysql_query($insert);
                $update = "UPDATE fb SET bonus='$last_bonus' WHERE fb_id='$member_id'";
                mysql_query($update);
                echo "1/" . $b_price;
            } else {
                echo "0/0";
            }
        } else {
            $sql = "SELECT * FROM member WHERE member_no='$member_id'";
            $res = mysql_query($sql);
            $row = mysql_fetch_array($res);
            if ($row['bonus'] != "" && $row['bonus'] >= $b_price) {
                $last_bonus = $row['bonus'] - $b_price;
                $insert = "INSERT INTO convert_info SET bonus_pid='$id', title='$b_title', bonus_price='$b_price', people='$member_id', is_use='0', convert_date='" . date('Y-m-d H:i:s') . "'";
                mysql_query($insert);
                $update = "UPDATE member SET bonus='$last_bonus' WHERE member_no='$member_id'";
                mysql_query($update);
                echo "1/" . $b_price;
            } else {
                echo "0/0";
            }
        }
    }
}

function check_is_reg()
{
    $account = $_POST['account'];
    $sql = "SELECT id,email FROM member WHERE email='$account'";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    if ($row['id'] != "") {
        echo 1;
    } else {
        echo 0;
    }
}


function update_use_bonus()
{
    $id = $_POST['id'];
    $sql = "SELECT * FROM convert_info WHERE id='$id'";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    if ($row['id'] != "" && $row['is_use'] == 0) {
        $update = "UPDATE convert_info SET is_use='1' WHERE id='$id'";
        mysql_query($update);
        echo '1'; //兌換完成
    } else if ($row['id'] != "" && $row['is_use'] == 1) {
        echo '2'; //已經使用過
    } else {
        echo '3'; //意外錯誤
    }
}

function search_member_info()
{
    $m_id = $_POST['m_id'];
    $ary = array();
    $sql = "SELECT * FROM member WHERE member_no='$m_id'";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    $ary[] = array(
        "m_name" => $row['m_name'],
        "cellphone" => $row['cellphone'],
        "address" => $row['address']
    );
    echo json_encode($ary);
}

function member_update_order()
{
    $order_no = $_POST['order_no'];
    $addressee_name = $_POST['addressee_name'];
    $addressee_cellphone = $_POST['addressee_cellphone'];
    $addressee_address = $_POST['addressee_address'];
    $addressee_date = $_POST['addressee_date'];
    $sql = "UPDATE addressee_set SET `name`='$addressee_name', cellphone='$addressee_cellphone', address='$addressee_address', addressee_date='$addressee_date' WHERE order_no='$order_no'";
    mysql_query($sql);
    echo 1;
}

function clean_order()
{
    $o_id = $_POST['o_id'];
    if (isset($_SESSION["member_no"])) {
        $member_id = $_SESSION["member_no"];
    } else {
        $member_id = $_SESSION["fb_id"];
    }

    if ($o_id != "" && $member_id != "") {
        $sql = "UPDATE consumer_order SET is_effective='2' WHERE id='$o_id' AND m_id='$member_id'"; //訂單資料表中此欄位的2代表取消這筆訂單 1代表有效 0無效
        mysql_query($sql);
        if (mysql_affected_rows() > 0) {
            echo 1;
        } else {
            echo 2;
        }
    }
}

function fb_search()
{
    $time1 = $_POST['time1'];
    $time2 = $_POST['time2'];
    $page = $_POST['page'];
    $date = $_POST['date'];
    $m_id = $_POST['m_id'];
    if ($m_id != "") {
        if ($date == "" && $page == "") {
            $sql = "SELECT name,page,storetime FROM `fb_fans` WHERE fans_id IN($m_id) AND DATE_FORMAT(`storetime`,'%H:%i:%s') BETWEEN '$time1' and '$time2'";

        } else if ($date == "" && $page != "") {
            $sql = "SELECT name,page,storetime FROM `fb_fans` WHERE fans_id IN($m_id) AND DATE_FORMAT(`storetime`,'%H:%i:%s') BETWEEN '$time1' and '$time2' and page like '$page'";
        } else if ($date != "" && $page != "") {
            $sql = "SELECT name,page,storetime FROM `fb_fans` WHERE fans_id IN($m_id) AND DATE_FORMAT(`storetime`,'%H:%i:%s') BETWEEN '$time1' and '$time2' and DATE_FORMAT(`storetime`,'%Y/%m/%d')='$date' and page like '$page'";
        } else if ($date != "" && $page == "") {
            $sql = "SELECT name,page,storetime FROM `fb_fans` WHERE fans_id IN($m_id) AND DATE_FORMAT(`storetime`,'%H:%i:%s') BETWEEN '$time1' and '$time2' and DATE_FORMAT(`storetime`,'%Y/%m/%d')='$date'";
        }


        $arr = array();
        $ressearch = mysql_query($sql);
        while ($row = mysql_fetch_assoc($ressearch)) {
            $arr[] = $row;
        }
        $txt = json_encode($arr);
        echo $txt;
    } else {
        echo "您沒有粉絲團";
    }


}