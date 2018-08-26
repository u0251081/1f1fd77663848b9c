<?php
include_once 'mysql.php';
require_once '../vendor/autoload.php';
require_once '../lib/toolFunc.php';
define('BaseSecurity', 'this is 17mai');
session_start();
$_SESSION['checkCode'] = BaseSecurity;
use function Base17Mai\take;
use Base17Mai\Administrator;

$admin = new Administrator();
$loginStatus = $admin->checkLogin();
if (!$loginStatus && take('url') !== 'Login') die(NoticeToLogin());
$photoSticker = $admin->getImage();
$name = $admin->getName();
$identity = $admin->getIdentity();
$identityStr = $admin->getIdentityStr();
$menuHtml = $admin->constructAdminMenu();
$defaultContentinner = '';
$includePage = 'topone';
$url = take('url');
if ($url !== "") $includePage = $url;
// old setting
sql();
$id = take('id', '', 'session');


function NoticeToLogin()
{
    $str = '';
    $str .= '<script>';
    $str .= 'alert("使用請先登入");';
    $str .= 'location.href="?url=Login";';
    $str .= '</script>';
    return $str;
}

function includePage()
{
    $url = take('url');
    // print 'id:'.$id;
    // print 'identity:'.$identity;
    if ($url === "") {
        include("topone.php");
    } else {
        include_once($url . '.php');
    }
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

    <!-- for switchButton -->
    <script type="text/javascript" src="js/jquery.switchbutton.min.js"></script>
    <script type="text/javascript" src="js/jquery.tmpl.min.js"></script>
    <link rel="stylesheet" href="css/ui.switchbutton.min.css" type="text/css"/>

    <!-- Bootstrap Notify -->
    <script type="text/javascript" src="js/bootstrap-notify.min.js"></script>

    <!-- DataTable -->
    <link rel="stylesheet" href="../assets/vendor/DataTable/datatables.css">
    <script type="text/javascript" src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

    <script>
        function showMessage(msg) {
            $.notify(msg, {
                allow_dismiss: false,
                type: 'myNotify',
                placement: {
                    from: 'bottom',
                    align: 'right'
                }
            });
        }

        function customerMsg(mst) {
            $.notify({
                icon: 'https://randomuser.me/api/portraits/med/men/77.jpg',
                title: 'Byron Morgan',
                message: 'Momentum reduce child mortality effectiveness incubation empowerment connect.'
            }, {
                type: 'minimalist',
                delay: 5000,
                icon_type: 'image',
                template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
                    '<img data-notify="icon" class="img-circle pull-left">' +
                    '<span data-notify="title">{1}</span>' +
                    '<span data-notify="message">{2}</span>' +
                    '</div>'
            });
        }
    </script>
    <style>
        td {
            font-size: 2.5vh;
        }

        .alert-myNotify {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: rgb(0, 0, 0);
            border-radius: 25px;
            font-size: 20px;
            width: 200px;
            height: 50px;
            color: #FFFFFF;
        }

        /* for notify */
        .alert-minimalist {
            background-color: rgb(241, 242, 240);
            border-color: rgba(149, 149, 149, 0.3);
            border-radius: 3px;
            color: rgb(149, 149, 149);
            padding: 10px;
        }

        .alert-minimalist > [data-notify="icon"] {
            height: 50px;
            margin-right: 12px;
        }

        .alert-minimalist > [data-notify="title"] {
            color: rgb(51, 51, 51);
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .alert-minimalist > [data-notify="message"] {
            font-size: 80%;
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
                                        您的身份為：<?= $identityStr ?>
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
                    <?php include_once($includePage . '.php'); ?>
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
