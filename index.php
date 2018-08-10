<?php
session_start();
require_once 'admin/mysql.php';
sql();
$member_id = '';
/*
 * 判斷登入的是一般會員還是fb註冊會員
 * 優先權是 member_no > fb_id
 * 所以有 member_no 會蓋過 fb_id
 */
if (isset($_SESSION['fb_id'])) $member_id = $_SESSION['fb_id'];
if (isset($_SESSION["member_no"])) $member_id = $_SESSION["member_no"];

/*
 * 判斷登入的是電腦版還是手機版
 */
$_SESSION['device'] = checkUserAgent();
$htm_para1 = '';
$htm_para2 = '';
$mobileStyle = '';
$my_nav = '';

if ($_SESSION['device'] === 'mobile') {
    $htm_para1 .= 'class="aa-header-top-area hidden-xs"';
    $my_nav = showMyNav();
    $mobileStyle = styleOfMobile();
}
if ($_SESSION['device'] === 'desktop') {
    $htm_para1 .= 'class="aa-header-top-area"';
    $htm_para2 .= '<li><a href="index.php?url=member_center">會員專區</a></li>';
    if (isset($_SESSION['front_id']) && $_SESSION['front_id'] !== "") {
        $htm_para2 .= '<li><a href="logout.php">登出</a></li>';
    } else {
        $htm_para2 .= '<li><a href="javascript:void(0);" data-toggle="modal" data-target="#login-modal">登入</a></li>';
        $htm_para2 .= '<li><a href="index.php?url=Register" > 註冊</a ></li >';
    }
}

function showMyNav()
{
    $result = '
    <div class="my_nav">
        <ul>
            <li>
                <a href="index.php"><span><img src="img/icon/home.png"></span><span>首頁</span></a>
            </li>
            <li style="position:relative;">
                <a href="index.php?url=member_center"><span><img src="img/icon/foot-member.png"></span><span>會員專區</span></a>
            </li>
            <li id="li3">
                <a id=\'start_app_btn\' href="javascript:void(0);"><span><img src="img/icon/mobile.png"></span><span>開啟APP</span></a>
            </li>
            <li id="li4" style="display: none;">
                <a id=\'back_btn\' href="javascript:void(0);"><span><img src="img/icon/back.png"></span><span>返回</span></a>
            </li>
            <li>
                <a href="index.php?url=cart"><span><img src="img/icon/shopcart_icon.png"></span><span>購物車</span></a>
            </li>
        </ul>
    </div>
    ';
    return $result;
}

function styleOfMobile()
{
    $result = '
            .my_nav {
                margin: 0 auto;
                display: block;
                background: #FF6666;
                padding: 10px 0 6px 0;
                width: 100%;
                height: auto;
                position: fixed;
                left: 0;
                bottom: 0;
            }

            .my_nav ul {
                height: 0px;
            }

            .my_nav ul li {
                float: left;
                width: 25%;
                text-align: center;
                list-style-type: none;
                margin: 0px;
                padding: 0px;
            }

            .my_nav ul li span {
                display: block;
                color: #fff;
                font-size: 14px;
                font-family: "微軟正黑體";
                line-height: 22px;
            }

            #aa-footer {
                display: none;
            }

            a {
                color: #000;
                text-decoration: none;
            }

            * {
                padding: 0;
                margin: 0;
                list-style: none;
                font-weight: normal;
            }

            .product_content {
                margin-top: 40px;
            }

            .product_content2 {
                margin-top: -60px;
            }

            .product_content3 {
                margin-top: -40px;
            }

            .mobile-slider {
                height: 150px;
            }

            #s_img_display {
                margin-top: 22px;
            }
            ';
    return $result;
}

function checkUserAgent()
{

    $regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
    $regex_match .= "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
    $regex_match .= "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
    $regex_match .= "symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
    $regex_match .= "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
    $regex_match .= ")/i";
    $result = preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));

    if ($result) {
        //header("Location: mobile.php"); //手機版
        return 'mobile';
    } else {
        // header("Location: pc.php");  //電腦版
        return 'desktop';
    }
}

function showMessagesAndRedirect($str = '', $url = '')
{
    print '<script>';
    if ($_SESSION['device'] === 'mobile') print "window.javatojs.showInfoFromJs('{$str}');";
    if ($_SESSION['device'] === 'desktop') print "alert('{$str}');";
    if (!empty($url)) print "location.href='{$url}';";
    print '</script>';
}

function construct_menu()
{
    $list_data = get_list();
    $list_html = list_html($list_data);
    print $list_html;
}

function list_html($list_data = false)
{
    if (is_array($list_data)) {
        $html = '';
        foreach ($list_data as $v) {
            $html .= "<li>";
            $html .= "<a href='index.php?url=product&pid={$v['id']}'>{$v['name']}";
            if (is_array($v['child'])) $html .= '<span class="caret"></span>';
            $html .= "</a>";
            if (is_array($v['child'])) $html .= '<ul class="dropdown-menu">' . list_html($v['child']) . "</ul>";
            $html .= "</li>";
        }
        return $html;
    }
}

function get_list($id = false)
{
    if ($id === false) {
        $sql = 'select * from class where id = parent_id;';
        $list = pdo_select_sql($sql);
    } else {
        $sql = 'select * from class where id != parent_id and parent_id = :parent_id;';
        $para = ['parent_id' => $id];
        $list = pdo_select_sql($sql, $para);
    }
    if (is_array($list)) {
        foreach ($list as $key => $value) {
            if (has_child($value['id'])) {
                $list[$key]['child'] = get_list($value['id']);
            } else {
                $list[$key]['child'] = null;
            }
        }
    }
    return $list;
}

function has_child($id = false)
{
    $sql = 'select count(*) from class where id != parent_id and parent_id = :parent_id;';
    $para = ['parent_id' => $id];
    $result = pdo_select_sql($sql, $para);
    if ($result[0]['count(*)'] === '0') return false;
    else return true;
}

function finish_profile()
{
    if ($_GET['url'] !== 'member_info') {
        $msg = '請先完成個人資料';
        $url = 'index.php?url=member_info';
        showMessagesAndRedirect($msg, $url);
    }
}

function checkMemberInfoStatus($member_id)
{
    $status = true;
    $sql = "select * from member where member_no = :member_id";
    $para = ['member_id' => $member_id];
    $member_info = pdo_select_sql($sql, $para)[0];
    $require_column = ['id_card', 'address', 'cellphone'];
    foreach ($require_column as $v) {
        if (empty($member_info[$v])) $status = false;
    }
    return $status;
}

function requireMemberAccount()
{
    $message = '請先登入或註冊成為會員';
    $url = '';
    if ($_SESSION['device'] === 'mobile') $url = 'login.html';
    if ($_SESSION['device'] === 'desktop') $url = 'index.php?url=Register';
    showMessagesAndRedirect($message, $url);
}

function checkPermissionRequire($url = '')
{
    $permission = 'public';
    $memberRequirePages = ['member_center', 'member_info', 'wishlist', 'cart', 'bonus_search', 'bonus_use',
        'order_search', 'to_manager', 'pay_check', 'order_detail'];
    if (in_array($url, $memberRequirePages)) $permission = 'member';
    return $permission;
}

print '<!--';
print_r($_SERVER);
print '-->';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-110556098-5"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-110556098-5');
    </script>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>春燕築巢 地方創生電子商城</title>

    <!-- Font awesome -->
    <link href="css/font-awesome.css" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <!-- SmartMenus jQuery Bootstrap Addon CSS -->
    <link href="css/jquery.smartmenus.bootstrap.css" rel="stylesheet">
    <!-- Product view slider -->
    <link rel="stylesheet" type="text/css" href="css/jquery.simpleLens.css">
    <!-- slick slider -->
    <link rel="stylesheet" type="text/css" href="css/slick.css">
    <!-- price picker slider -->
    <link rel="stylesheet" type="text/css" href="css/nouislider.css">
    <!-- Theme color -->
    <link id="switcher" href="css/theme-color/default-theme.css" rel="stylesheet">
    <!-- <link id="switcher" href="css/theme-color/bridge-theme.css" rel="stylesheet"> -->
    <!-- Top Slider CSS -->
    <link href="css/sequence-theme.modern-slide-in.css" rel="stylesheet" media="all">

    <!-- Main style sheet -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/supplier.css" rel="stylesheet">
    <!-- Google Font -->
    <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery library -->
    <script src="admin/js/jquery-1.9.1.min.js"></script>
    <script></script>
    <style>
        .my_nav {
            display: none;
        }

        #no-login-slider {
            width: 100%;
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            padding-top: 30px;
            height: 0;
            overflow: hidden;
        }

        .video-container iframe,
        .video-container object,
        .video-container embed {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        <?=$mobileStyle?>

    </style>
</head>
<body>

<span id="m_id" style="display: none;"><?= $member_id ?></span>
<span id="device" style="display: none;"><?= $_SESSION['device'] ?></span>

<!-- 回頂部按鈕設定 -->
<a class="scrollToTop" id="scroll_tp" href="#"><i class="fa fa-chevron-up"></i></a>
<!-- 回頂部按鈕設定 -->

<!-- Start header section -->
<header id="aa-header">
    <!-- start header top  -->
    <div class="aa-header-top">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div <?= $htm_para1 ?>>
                        <!-- start header top left -->
                        <div class='aa-header-top-left'>
                            <div class="aa-header-top-right">
                                <ul class="aa-head-top-nav-right">
                                    <!--<li class="hidden-xs"><a href="index.php?url=wishlist">追蹤清單</a></li>
                                        <li class="hidden-xs"><a href="index.php?url=cart">購物車</a></li>-->
                                    <?= $htm_para2 ?>
                                </ul>
                            </div>
                        </div>
                        <!-- / header top left -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / header top  -->

    <!-- start header bottom  -->
    <div class="aa-header-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="aa-header-bottom-area">
                        <!-- logo  -->
                        <div class="aa-logo">
                            <a href="index.php">
                                <span class="fa fa-shopping-cart"></span>
                                <p>守護天使-團購家族<span>電子商城</span></p>
                            </a>
                        </div>
                        <!-- / logo  -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / header bottom  -->
</header>
<!-- / header section -->

<!-- menu -->
<section id="menu">
    <div class="container">
        <div class="menu-area">
            <!-- Navbar -->
            <div class="navbar navbar-default" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse">
                    <!-- Left nav -->
                    <ul class="nav navbar-nav">
                        <?php construct_menu(); ?>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
    </div>
</section>
<!-- / menu -->

<!-- Start slider -->
<section id="aa-slider">
    <div class="aa-slider-area">
        <div id="sequence" class="seq mobile-slider" style="height: 150px;">
            <div class="seq-screen">
                <ul class="seq-canvas">
                    <!-- 輪播 -->
                    <?php
                    $banner_sql = "SELECT * FROM banner WHERE `status`='1'";
                    $banner_res = mysql_query($banner_sql);
                    while (@$banner_row = mysql_fetch_array($banner_res)) {
                        if (@$_SESSION['device'] != "" && $_SESSION['device'] == 'desktop') {
                            ?>
                            <li id="s_img_display">
                                <div class="seq-model">
                                    <a href="<?php echo @$banner_row['url']; ?>"><img
                                                src="<?php echo 'admin/' . $banner_row['img']; ?>"
                                                class="img-responsive" style='width:100%;'/></a>
                                </div>
                            </li>
                            <?php
                        } else if (@$_SESSION['device'] != "" && $_SESSION['device'] == 'mobile') {
                            ?>
                            <li id="s_img_display">
                                <div class="seq-model">
                                    <a href="<?php echo @$banner_row['url']; ?>"><img
                                                src="<?php echo 'admin/' . $banner_row['mobile_img']; ?>"
                                                class="img-responsive" style='width:100%;'/></a>
                                </div>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li id="s_img_display">
                                <div class="seq-model">
                                    <a href="<?php echo @$banner_row['url']; ?>"><img
                                                src="<?php echo 'admin/' . $banner_row['img']; ?>"
                                                class="img-responsive" id="no-login-slider" style='width:100%;'/></a>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    ?>
                    <!-- 輪播 -->
                </ul>
            </div>
            <!-- slider navigation btn -->
            <fieldset class="seq-nav" aria-controls="sequence" aria-label="Slider buttons">
                <a type="button" class="seq-prev" aria-label="Previous"><span class="fa fa-angle-left"></span></a>
                <a type="button" class="seq-next" aria-label="Next"><span class="fa fa-angle-right"></span></a>
            </fieldset>
        </div>
    </div>
</section>
<!-- / slider -->

<?php
@$url = $_GET['url'];
if ($member_id !== '') {
    if (strlen($member_id) > 10) {
        $member_status = checkMemberInfoStatus($member_id);
    } else {
        $member_status = checkMemberInfoStatus($member_id);
    }
    if (!$member_status) finish_profile();
}
// switch content of page
$url = isset($_GET['url']) ? $_GET['url'] : '';
if ($url === '') include 'main.php';
else {
    $requirePermission = checkPermissionRequire($url);
    if ($member_id === '' && $requirePermission === 'member') requireMemberAccount();
    else {
        // check page file exists
        if (is_file($url . ".php")) {
            include_once($url . '.php');
        } else {
            include_once('404.php');
        }
    }
}

//--- unknown what work to ---//
if ($member_id !== '' || (isset($_SESSION['manager_no']) && $_SESSION['manager_no'] !== '')) {
    $file_member = 'pay_log/' . $member_id . '.json';
    $file_manager = '';
    $file = '';
    if (isset($_SESSION['manager_no']) && $_SESSION['manager_no'] !== '')
        $file_manager = 'pay_log/' . $_SESSION['manager_no'] . '.json';
    if (file_exists($file_manager)) $file = $file_manager;
    if (file_exists($file_member)) $file = $file_member;
    if (!empty($file)) {
        $json_string = file_get_contents($file);
        $data = json_decode($json_string, true);
        $share_id = $data[1];
        if (!empty($share_id)) {
            $sql = 'select * from share where id = :share_id and is_effective = \'1\';';
            $para = ['share_id' => $share_id];
            $shareCheck = pdo_select_sql($sql, $para)[0]['id'];
            if (!empty($shareCheck)) {
                unset($_SESSION['share_manager_no']);
                unset($_SESSION['share_vip_id']);
            }
        }
        unlink($file);
    }
}
//-----//
?>

<!-- footer -->
<footer id="aa-footer">
    <!-- footer bottom -->
    <div class="aa-footer-top">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="aa-footer-top-area">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="aa-footer-widget">
                                    <h3>關於我們</h3>
                                    <ul class="aa-footer-nav">
                                        <li><a href="index.php">首頁</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="aa-footer-widget">
                                    <div class="aa-footer-widget">
                                        <h3>常見問題</h3>
                                        <ul class="aa-footer-nav">
                                            <li><a href="#">QA</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="aa-footer-widget">
                                    <div class="aa-footer-widget">
                                        <h3>廠商專區</h3>
                                        <ul class="aa-footer-nav">
                                            <li><a href="#">合作徵才</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="aa-footer-widget">
                                    <div class="aa-footer-widget">
                                        <h3>聯絡我們</h3>
                                        <ul class="aa-footer-nav">
                                            <li>
                                                <a href="https://docs.google.com/forms/d/e/1FAIpQLSc1PwrjkotZqD4x4vneji6jN2wupOFZZSMt4Mzg29R44fIcLw/viewform"
                                                   target="_blank">聯絡我們</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- footer-bottom -->
    <div class="aa-footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="aa-footer-bottom-area">
                        <p><!--版權宣告-->僅供學術測試</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- / footer -->

<!-- Login Modal -->
<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4>登入</h4>
                <form class="aa-login-form">
                    <label for="">帳號<span>*</span></label>
                    <input type="text" id="account" placeholder="帳號">
                    <label for="">密碼<span>*</span></label>
                    <input type="password" id="password" placeholder="密碼">
                    <button class="aa-browse-btn" id="login_btn" type="button">登入</button>
                    <?php /* <button class="aa-browse-btn" id="fb_login_btn" type="button">fb登入</button> */ ?>
                    <p class="aa-lost-password"><a href="index.php?url=forget_password">忘記密碼?</a></p>
                    <div class="aa-register-now">
                        還沒有帳號嗎?<a href="index.php?url=Register">註冊</a>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<?= $my_nav ?>

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.js"></script>
<!-- SmartMenus jQuery plugin -->
<script type="text/javascript" src="js/jquery.smartmenus.js"></script>

<!-- SmartMenus jQuery Bootstrap Addon -->
<script type="text/javascript" src="js/jquery.smartmenus.bootstrap.js"></script>
<!-- To Slider JS -->
<script src="js/sequence.js"></script>
<script src="js/sequence-theme.modern-slide-in.js"></script>
<!-- Product view slider -->
<script type="text/javascript" src="js/jquery.simpleGallery.js"></script>
<script type="text/javascript" src="js/jquery.simpleLens.js"></script>
<!-- slick slider -->
<script type="text/javascript" src="js/slick.js"></script>
<!-- Price picker slider -->
<script type="text/javascript" src="js/nouislider.js"></script>
<!-- Custom js -->
<script src="js/custom.js"></script>
</body>
</html>

<script type="text/javascript">

    $("#fb_login_btn").click(function () {
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                testAPI(reback);

            } else {
                // The person is not logged into your app or we are unable to tell.

                FB.login(function (response) {
                    // handle the response\
                    testAPI(reback);
                }, {scope: 'public_profile,email'});
            }
        }

        function checkLoginState() {
            FB.getLoginStatus(function (response) {
                statusChangeCallback(response);
            });
        }

        window.fbAsyncInit = function () {
            FB.init({
                appId: '1219742794810228',
                autoLogAppEvents: true,
                xfbml: true,
                version: 'v2.11'
            });

            FB.getLoginStatus(function (response) {
                statusChangeCallback(response);
            });
        };

        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = 'https://connect.facebook.net/zh_TW/sdk.js#xfbml=1&version=v2.11&appId=1219742794810228';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));


        function testAPI(callback) {

            FB.api('/me', 'GET', {"fields": "id,name,email"}, function (response) {
                var account = response.email;
                var fbid = response.id;
                var name = response.name;
                if (fbid != "") {
                    $.ajax
                    ({
                        url: "ajax.php", //接收頁
                        type: "POST", //POST傳輸
                        data: {type: "fblogin", account: account, fbid: fbid, name: name}, // key/value
                        dataType: "text", //回傳形態
                        success: function (i) //成功就....
                        {
                            if (i == 1) {
                                alert('登入成功');
                                callback();
                                location.href = 'index.php';


                            }
                            else {
                                alert('登入失敗請重新登入!');
                                console.log(i);
                            }
                        },
                        error: function ()//失敗就...
                        {
                        }
                    });
                }
                else {
                    alert('登入失敗請重新登入!');
                }
            });
        }

        function fbLogoutUser() {
            FB.getLoginStatus(function (response) {
                if (response && response.status === 'connected') {
                    FB.logout(function (response) {

                    });
                }
            });
        }

    });

    function reback() {

        location.href = 'index.php';
    }


    $("#login_btn").click(function () {
        var account = $("#account").val();
        var password = $("#password").val();
        $.ajax
        ({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "login", account: account, password: password}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                if (i === 'success') {
                    alert('登入成功');
                    location.href = 'index.php';
                }
                else {
                    alert('帳號或密碼錯誤');
                    $("#password").val('');
                }
            },
            error: function ()//失敗就...
            {
            }
        });
    });

    $(function () {
        if ($(window).width() < 767) {
            $("#scroll_tp").remove();

            var ax = $(".ax").height();
            var my_nav = $(".my_nav").height();
            if (my_nav < ax) {
                $(".ax").css('height', ax + my_nav + 20 + 'px');
            }
        }
    })

    //手機版本APP喚醒的按鈕
    $("#start_app_btn").click(function () {
        var m_id = $("#m_id").text(); //會員id判斷是否從fb登入
        var my_manager_id = $("#my_manager_id").text().trim(); //如果沒有收到行銷經理id，且自己也是行銷經理則載入自己的id
        var manager_id = $("#manager_id").text().trim(); //判斷是否有收到行銷經理id
        var device = $("#device").text(); //判斷目前載具
        var vip_id = $("#vip_id").text().trim(); //判斷有無vip_id

        var url = $("#url").text();
        var pid = $("#pid").text();//商品id

        if (manager_id != "" && vip_id == "") {
            $(this).attr('href', 'intent://jjl/openwith?url=' + url + '&product_id=' + pid + '&manager_id=' + manager_id + '#Intent;scheme=myapp;package=com.nkfust.firstshop;S.browser_fallback_url=http://17mai.com.tw/index.php?url=app_download;end');
        }
        else if (manager_id != "" && vip_id != "") {
            $(this).attr('href', 'intent://jjl/openwith?url=' + url + '&product_id=' + pid + '&manager_id=' + manager_id + '&vip_id=' + vip_id + '#Intent;scheme=myapp;package=com.nkfust.firstshop;S.browser_fallback_url=http://17mai.com.tw/index.php?url=app_download;end');
        }
        else {
            $(this).attr('href', 'intent://jjl/openwith?url=' + url + '&product_id=' + pid + '#Intent;scheme=myapp;package=com.nkfust.firstshop;S.browser_fallback_url=http://17mai.com.tw/index.php?url=app_download;end');
        }

        //$(this).attr('href','intent://jjl/openwith?url='+url+'&product_id='+pid+'#Intent;scheme=myapp;package=com.nkfust.firstshop;S.browser_fallback_url=http%3A%2F%2Fwww.google.com;end');http%3A%2F%2F163.18.62.22/firstshop/
        //alert($("#vip_id").text());

    });

    //以下是java呼叫網頁的Javascript函數
    function getFBID(fb_email, fb_id, imei, regid) {
        if (fb_id != "" && imei != "") {
            $.ajax
            ({
                url: "ajax.php", //接收頁
                type: "POST", //POST傳輸
                data: {type: "set_regid", fb_id: fb_id, regid: regid}, // key/value
                dataType: "text", //回傳形態
                success: function (i) //成功就....
                {

                },
                error: function ()//失敗就...
                {
                }
            });
        }
    }

    function getIMEI(imei, regid) {
        //alert(imei + " / " + regid);
    }

    function app_is_open_check() {
        $("#li3").remove();
        $("#li4").show();
    }

    $("#back_btn").on('click', function () {
        window.history.back(-1);
    });

    //QR掃描後處理值
    function qr_res(qr_str) {
        var m_id = $("#m_id").text();
        if (qr_str) {
            $.ajax
            ({
                url: "ajax.php",
                type: "POST",
                data: {type: "lower_limit", qr_str: qr_str, m_id: m_id},
                dataType: "text",
                success: function (i) {
                    if (i == 1) {
                        alert('訂閱成功');
                    }
                    else if (i == 0) {
                        alert('已經訂閱過該行銷經理');
                    }
                },
                error: function () {
                }
            });
        }
    }
</script>