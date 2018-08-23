<?php
include_once 'mysql.php';
require_once '../vendor/autoload.php';
require_once '../lib/toolFunc.php';
session_start();

use Base17Mai\take, Base17Mai\Administrator;

$admin = new Administrator();
$loginStatus = $admin->checkLogin();
if (!$loginStatus && take('url') !== 'Login') die(NoticeToLogin());
$photoSticker = $admin->getImage();
$name = $admin->getName();
$identity = $admin->getIdentity();
$menuHtml = constructMenu();
$defaultContentinner = '';


function NoticeToLogin()
{
    $str = '';
    $str .= '<script>';
    $str .= 'alert("使用請先登入");';
    $str .= 'location.href="?url=Login";';
    $str .= '</script>';
    return $str;
}

function constructMenu()
{
    global $admin;
    $result = print_r($admin->constructAdminMenu(), true);
    return $result;
}

?>
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>一起購管理後端</title>
    <link rel="stylesheet" href="css/style.default.css" type="text/css"/>
    <link rel="stylesheet" href="css/responsive-tables.css" type="text/css"/>
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-migrate-1.1.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.9.2.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jquery.cookie.js"></script>
    <script type="text/javascript" src="js/jquery.uniform.min.js"></script>
    <script type="text/javascript" src="js/flot/jquery.flot.min.js"></script>
    <script type="text/javascript" src="js/flot/jquery.flot.resize.min.js"></script>
    <script type="text/javascript" src="js/responsive-tables.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>
    <script type="text/javascript" src="js/modernizr.min.js"></script>
    <script type="text/javascript" src="js/AJAX17mai.js"></script>
    <style>
        td {
            font-size: 2.5vh;
        }
    </style>
</head>

<?php if ($loginStatus) : ?>
    <body>
    <div class="mainwrapper">
        <div class="header">
            <div class="logo">
                <a href="home.php" style="text-decoration:none;"><p class="mylogo">一起購管理系統</p></a>
            </div>
            <div class="headerinner">
                <ul class="headmenu">
                    <li class="right">
                        <div class="userloggedinfo">
                            <img src="<?= $photoSticker ?>"/>
                            <div class="userinfo">
                                <h5>
                                    <big id="login_name"><?= $name ?></big>
                                    <big>您好</big>
                                </h5>
                                <ul>
                                    <li id="identity">
                                        您的身份為：<?= $identity ?>
                                    </li>
                                </ul>
                                <ul>
                                    <li><a href="javascript:void(0);" id="Logout_btn">登出</a></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul><!--headmenu-->
            </div>
        </div>
        <!-- .header -->
        <div class="leftpanel"><!--這裡加上style='height:555px;可以讓中間縮小時不出現黑邊'-->
            <div class="leftmenu">
                <ul id="my_menu" class="nav nav-tabs nav-stacked">
                    <li class="nav-header">導航列</li>
                    <?= $menuHtml ?>
                </ul>

            </div><!--leftmenu-->
        </div>
        <!-- .leftpanel -->
        <div class="rightpanel">
            <div class="maincontent">
                <div class="maincontentinner">
                    <!--從這裡開始組合內容-->
                    <?php

                    sql();
                    @$id = $_SESSION['id'];
                    @$identity = $_SESSION['identity'];
                    @$url = $_GET['url'];
                    if ($url == "") {
                        include("topone.php");
                    } else {
                        include_once($url . '.php');
                    }
                    ?>
                    <div class="footer">
                        <div class="footer-left">
                            <span><h4>僅供學術研究測試(Only for academic research test)</h4></span>
                        </div>
                        <div class="footer-right">
                            <span></span>
                        </div>
                    </div>
                    <!-- .footer -->
                </div>
                <!-- .maincontentinner -->
            </div>
            <!-- .maincontent -->
        </div>
        <!-- .rightpanel -->
    </div>
    <!-- .mainwrapper -->
    <script>
        $(document).on('click', 'a#Logout_btn', function () {
            ajax17mai('Administrator', 'Logout');
        });
    </script>
    </body>
<?php else: include_once 'Login.php'; endif ?>
</html>
