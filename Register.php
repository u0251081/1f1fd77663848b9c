<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2018/8/5
 * Time: 下午 11:39
 */

require_once 'admin/mysql.php';
if (isset($_SESSION['front_id']) && $_SESSION['front_id'] !== '') print '<script>location.href="index.php";</script>';
if (isset($_POST['account']) && isset($_POST['password'])) {
    print_r($_POST);
    $register = array(
        'account' => addslashes($_POST['account']),
        'password' => addslashes($_POST['password'])
    );
    unset($_POST['account']);
    unset($_POST['password']);
    $member_no = '';
    do {
        for ($i = 1; $i <= 10; $i++) {
            $num = rand(1, 9);
            $member_no .= $num;
        }
        print $member_no;
    } while (!checkMemberNoRepeat($member_no));
    $register['member_no'] = $member_no;
    if (!create_account($register)) exit('When creating account encountered an error!!');
    //auto_login($register);
}

function checkMemberNoRepeat($member_no)
{
    $sql = '';
    $sql .= "select * from member where member_no = :member_no;";
    $parameter = array('member_no' => $member_no);
    $rst = pdo_select_sql($sql, $parameter);
    if (empty($rst)) return true;
    else print false;
}

function create_account($register)
{
    $account = $register['account'];
    $password = $register['password'];
    $member_no = $register['member_no'];
    $sql = '';
    $sql .= "insert into member set email = :account, password = unhex(md5(:password)), member_no = :member_no,";
    $sql = "insert into member set email = '{}', password = unhex(md5('')), member_no = '{}'";
    $sql .= " identity = 'member', registration_time = :regTime ;";
    $para = array(
        'account' => $account,
        'password' => $password,
        'member_no' => $member_no,
        'regTime' => date('Y-m-d H:i:s')
    );
    print_r($para);
    // $rst = pdo_insert_sql($sql, $para);
    // return $rst;
}

function auto_login($register)
{
    $account = $register['account'];
    $password = $register['password'];
    $command = '';
    $command .= "var account = '{$account}'";
    $command .= "var password = '{$password}'";
    $ajax_data = '';
    $ajax_data .= '{';
    $ajax_data .= 'url: "index.php",';
    $ajax_data .= 'method: "post",';
    $ajax_data .= 'data: {type: "login", account: account, password: password},'; // key:value
    $ajax_data .= 'success: function(msg) {if (msg === "1") {alert("註冊成功");location.href="index.php";}},'; // key:value
    $ajax_data .= 'error: function() {},'; // key:value
    $ajax_data .= '}';
    $command .= '$.ajax(' . $ajax_data . ');';
    print "<script>$command</script>";
}

?>
<section id="aa-myaccount" style="margin-top: -80px;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="aa-myaccount-area">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-2">
                            <div class="aa-myaccount-login">
                                <h4>註冊</h4>
                                <form method="post" id="reg_form" class="aa-login-form">
                                    <label for="">E-mail(帳號)<span>*</span></label>
                                    <input type="text" name="account" placeholder="E-mail(帳號)">
                                    <span style="position:absolute; top: 150px; color: red; font-size: 14px;"
                                          id="email-hint"></span>
                                    <br><br>
                                    <label for="">密碼<span>*</span></label>
                                    <input type="password" name="password" placeholder="密碼">
                                    <input type="button" name="reg_btn" class="aa-browse-btn" value="註冊">&nbsp;&nbsp;&nbsp;&nbsp;

                                    <!--<label class="rememberme" for="rememberme"><input type="checkbox" id="rememberme"> Remember me </label>-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $("html,body").scrollTop(700);
    $(function () {
        $("#email-hint").hide();
    });

    $("form#reg_form").submit(function () {
        let email = $(this).find('[name="account"]').val();
        let passwd = $(this).find('[name="password"]').val();
        let para = {email: email, password: passwd};
        ajax17mai('Member', 'CreateAccount',{},para);
        return false;
    });

    $("input[name='reg_btn']").click(function () {
        var email = $("input[name='account']").val();
        var email_reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/;
        if (email != "") {
            if (!email_reg.test(email)) {
                $("#email-hint").hide();
                $("#email-hint").text('請填正確的email格式').slideDown(1000).show();
            }
            else {
                $("form#reg_form").submit();
            }
        }
        else {
            $("#email-hint").hide();
            $("#email-hint").text('email必填').slideDown(1000).show();
        }
    });

    function create_account(email, password) {
        console.log('start create account');
        $.ajax({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {
                type: "desktop_reg",
                account: email,
                password: password
            },
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                if (i === 'success') autoLogin(email, password);
                else console.log(i);
            },
            error: function ()//失敗就...
            {
                console.log('critical error');
            }
        });
        return false;
    }

    function autoLogin(account, password) {
        console.log('start login');
        $.ajax({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "login", account: account, password: password}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                console.log(i);
                if (i === 'success') {
                    alert('註冊成功');
                    location.href = 'index.php';
                }
            },
            error: function ()//失敗就...
            {
            }
        });
        return false;
    }

    $("input[name='account']").blur(function () {
        var user_input = $(this).val();
        if (user_input) {
            $.ajax
            ({
                url: "ajax.php", //接收頁
                type: "POST", //POST傳輸
                data: {type: "check_is_reg", account: user_input}, // key/value
                dataType: "text", //回傳形態
                success: function (i) //成功就....
                {
                    if (i == 1) {
                        $("input[name='account']").val('');
                        $("#email-hint").hide();
                        $("#email-hint").text('此帳號已有人註冊過').slideDown(1000).show();
                    }
                    else {
                        $("#email-hint").hide();
                    }
                },
                error: function ()//失敗就...
                {
                }
            });
        }
    })
</script>