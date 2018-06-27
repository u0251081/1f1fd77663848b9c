<?php include_once 'mysql.php'; ?>
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>一起購管理後端</title>
    <?php @session_start(); ?>
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
    <style>
        td{
            font-size:2.5vh;
        }
    </style>
</head>
<body>
<?php
sql();
@$id = $_SESSION['id'];
@$identity = $_SESSION['identity'];
if ($id != "")
{
    ?>
    <div class="mainwrapper">

        <div class="header">
            <div class="logo">
                <a href="home.php" style="text-decoration:none;"><p class="mylogo">一起購管理系統</p></a>
            </div>
            <div class="headerinner">
                <ul class="headmenu">
                    <li class="right">
                        <div class="userloggedinfo">
                            <?php
                            if($_SESSION['identity'] == 'admin')
                            {
                                $sql = "SELECT * FROM admin WHERE id='".$_SESSION['id']."'";
                                $res = mysql_query($sql);
                                $row = mysql_fetch_array($res);
                            }
                            ?>
                            <img src="<?php echo $row['img']; ?>"/>
                            <div class="userinfo">
                                <h5>
                                    <big id="login_name"><?php echo @$_SESSION['name']; ?></big>
                                    <big>您好</big>
                                </h5>
                                <ul>
                                    <li id="identity">
                                        <?php
                                        switch ($identity) {
                                            case 'admin':
                                                echo '您的身分為:管理員';
                                                break;

                                            case 'supplier':
                                                echo '您的身分為:供應商';
                                                break;

                                            case 'store':
                                                echo '您的身分為:行銷經理';
                                                break;
                                        }
                                        ?>
                                    </li>
                                </ul>
                                <ul>
                                    <li><a href="logout.php">登出</a></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul><!--headmenu-->
            </div>
        </div>

        <div class="leftpanel"><!--這裡加上style='height:555px;可以讓中間縮小時不出現黑邊'-->
            <div class="leftmenu">
                <ul id="my_menu" class="nav nav-tabs nav-stacked">
                    <li class="nav-header">導航列</li>
<!--                    <li><a href="--><?php //echo "home.php?url=class"; ?><!--"><span class="icon-th-list"></span>功能設定</a></li>-->
<!--                    <li><a href="../index.php" target="_blank"><span class="iconfa-laptop"></span> 查看前台</a></li>-->

                    <?php
                    $sql = "SELECT * FROM fn_set ORDER BY `sort`";
                    $res = mysql_query($sql);
                    while ($row = mysql_fetch_array($res))
                    {
                        if ($row['id'] == $row['parent_id'] && $row['url'] == '#' && $row['identity'] == $identity)
                        {
                            $sql2 = "SELECT * FROM fn_set WHERE parent_id = '" . $row['id'] . "' AND id != parent_id ORDER BY `sort`";
                            $res2 = mysql_query($sql2);
                            while ($row2 = mysql_fetch_array($res2))
                            {
                                if ($row2['id'] != $row2['parent_id'] && $row2['url'] == '#')
                                {
                                    echo "<li class='dropdown'><a href='" . $row2['url'] . "'>" . $row2['name'] . "</a>";
                                        echo "<ul>";
                                        $sql3 = "SELECT * FROM fn_set WHERE parent_id = '" . $row2['id'] . "' AND id != parent_id ORDER BY `sort`";
                                        $res3 = mysql_query($sql3);
                                        while ($row3 = mysql_fetch_array($res3))
                                        {
                                            ?>
                                                <li><a href="<?php echo $row3['url']; ?>"><?php echo $row3['name']; ?></a></li>
                                            <?php
                                        }
                                     echo "</ul></li>";
                                }
                                else if ($row2['id'] != $row2['parent_id'] && $row2['url'] != '#')
                                {
                                    ?>
                                        <li><a href="<?php echo $row2['url']; ?>"><?php echo $row2['name']; ?></a>
                                    <?php
                                }
                            }
                            echo "</ul></li>";
                        }
                        else if ($row['id'] == $row['parent_id'] && $row['url'] != '#')
                        {
                            ?>
                                <li><a href="<?php echo $row['url']; ?>">
                                <span class="<?php echo $row['icon']; ?>"></span><?php echo $row['name']; ?></a>
                            <?php
                        }
                    }
                    ?>
                    <!--<li><a href="dashboard.html"><span class="iconfa-laptop"></span> Dashboard</a></li>-->
                </ul>

            </div><!--leftmenu-->
        </div><!-- leftpanel -->

        <div class="rightpanel">
            <!--麵包導航開始-->
<!--            <ul class="breadcrumbs">-->
<!--                <li>-->
<!--                    <a href="dashboard.html">-->
<!--                        <i class="iconfa-home"></i>-->
<!--                    </a><span class="separator"></span>-->
<!--                </li>-->
<!--                <li>-->
<!--                    標題-->
<!--                </li>-->
<!--            </ul>-->
            <!--麵包導航結束-->
<!--            <div class="pageheader">-->
<!--                <form action="" method="post" class="searchbar">-->
<!--                    <input type="text" name="keyword" placeholder="請輸入關鍵字" />-->
<!--                </form>-->
<!--                <div class="pageicon"><span class="iconfa-laptop"></span></div>-->
<!--                <div class="pagetitle">-->
<!--                    <h1>-->
<!--                        標題-->
<!--                    </h1>-->
<!--                </div>-->
<!--            </div>-->
            <!--pageheader-->

            <div class="maincontent">
                <div class="maincontentinner">
                    <!--從這裡開始組合內容-->
                    <?php
                    @$url = $_GET['url'];
                    if ($url == "")
                    {
                        include("topone.php");
                    }
                    else
                    {
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
                    <!--footer-->

                </div><!--maincontentinner-->
            </div><!--maincontent-->
        </div><!--rightpanel-->

    </div><!--mainwrapper-->
<?php
}
else
{
?>
    <script type="text/javascript">
        alert("請勿非法登入");
        window.location.href = "admin_login.php";
    </script>
    <?php
}
?>
</body>
</html>
