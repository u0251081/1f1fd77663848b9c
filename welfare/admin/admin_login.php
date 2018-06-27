<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>第一商城管理者後台</title>
    <?php @session_start(); include("mysql.php"); ?>
    <link rel="stylesheet" href="css/style.default.css" type="text/css"/>
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
</head>

<body class="loginpage">

<div class="loginpanel">
    <div class="loginpanelinner">
        <div class="logo animate0 bounceIn"><p class="mylogo">總管理登入</p></div>
        <form id="login" method="post">

            <div class="inputwrapper animate1 bounceIn">
                <input type="text" name="account" placeholder="帳號"/>
            </div>
            <div class="inputwrapper animate2 bounceIn">
                <input type="password" name="password" placeholder="密碼"/>
            </div>
            <div class="inputwrapper animate3 bounceIn">
                <input type="submit" class="login_btn" value="登入" name="btn" style="width:100%;"/>
            </div>
        </form>
    </div>
</div>

<div class="loginfooter">
    <p><h4><!--版權宣告--></h4></p>
</div>

<?php
sql();
if (@$_SESSION['id'] != '')
{
    ?>
    <script type="text/javascript">
        window.location.href = "home.php";
    </script>
    <?php
}
else
{
    @$account = addslashes($_POST['account']);
    @$password = addslashes($_POST['password']);
    if($account != "" && $password != "")
    {
        $sql = "SELECT * FROM admin WHERE account = '$account' AND password = '$password'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);
        if ($row['id'] != "")
        {
            $_SESSION['id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['identity'] = $row['identity'];
            ?>
                <script>
                    alert('登入成功');
                    location.href = 'home.php';
                </script>
            <?php
        }
        else
        {
            ?>
            <script>
                alert('帳號或密碼錯誤');
                location.href = 'admin_login.php';
            </script>
            <?php
        }
    }
}
?>
</body>
</html>