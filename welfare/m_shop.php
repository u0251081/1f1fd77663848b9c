<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>一起購</title>

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

    <!-- Google Font -->
    <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

      <!-- jQuery library -->
      <script src="admin/js/jquery-1.9.1.min.js"></script>
      <style>
          .my_nav {
              display: none;
          }
          @media (max-width: 768px)
          {
              .my_nav
              {
                  margin:0 auto;
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

              #aa-footer
              {
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
          }

          .video-container
          {
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
      </style>
  </head>
  <body>
  <?php
  @session_start();
  include_once 'admin/mysql.php';
  sql();
  ?>
   <!-- wpf loader Two -->
    <!--<div id="wpf-loader-two">
      <div class="wpf-loader-two-inner">
        <span>載入中</span>
      </div>
    </div>-->
    <!-- / wpf loader Two -->

  <!-- SCROLL TOP BUTTON -->
    <a class="scrollToTop" id="scroll_tp" href="#"><i class="fa fa-chevron-up"></i></a>
  <!-- END SCROLL TOP BUTTON -->

  <!-- Start header section -->
  <header id="aa-header">
    <!-- start header top  -->
    <div class="aa-header-top">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="aa-header-top-area hidden-xs">
              <!-- start header top left -->
              <div class="aa-header-top-left">
                  <div class="aa-header-top-right">
                      <ul class="aa-head-top-nav-right">
                          <li><a class="hidden-xs" href="index.php?url=member_center">會員專區</a></li>
                          <!--<li class="hidden-xs"><a href="index.php?url=wishlist">追蹤清單</a></li>
                          <li class="hidden-xs"><a href="index.php?url=cart">購物車</a></li>-->
                          <li class="hidden-xs">
                              <?php
                              if(@$_SESSION['front_id'] != "")
                              {
                                  ?>
                                  <a href="logout.php">登出</a>
                                  <?php
                              }
                              else
                              {
                                  ?>
                                  <a href="javascript:void(0);" data-toggle="modal" data-target="#login-modal">登入</a>
                                  <?php
                              }
                              ?>
                          </li>
                          <!--<li class="hidden-xs"><a href="checkout.html">Checkout</a></li>-->
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
                <!-- Text based logo -->
                <a href="m_shop.php">
                  <span class="fa fa-shopping-cart"></span>
                  <p>一起<strong>購</strong><span>電子商城</span></p>
                </a>
                <!-- img based logo -->
                <!-- <a href="index.html"><img src="img/logo.jpg" alt="logo img"></a> -->
              </div>
              <!-- / logo  -->

              <!-- search box -->
                <!-- <div class="aa-search-box">
                  <form action="">
                    <input type="text" name="" id="" placeholder="請輸入關鍵字查詢">
                    <button type="submit"><span class="fa fa-search"></span></button>
                  </form>
                </div>-->
                <!-- / search box -->
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

                <!--<li><a href="#">第一層 <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">第二層<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">第三層</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>-->

                <?php
                $sql = "SELECT class_id FROM seller_manager WHERE manager_no='".$_SESSION['manager_no']."'"; //先取得行銷經理的擅長商品類別
                $res = mysql_query($sql);
                $row = mysql_fetch_array($res);

                $nav_sql = "SELECT * FROM `class` WHERE id IN(".$row['class_id'].")";
                $nav_res = mysql_query($nav_sql);
                while($nav_row = mysql_fetch_array($nav_res))
                {
                    ?>
                    <li><a href="m_shop.php?m_id=<?php echo $_SESSION['manager_no']; ?>&nav_id=<?php echo $nav_row['id']; ?>"><?php echo $nav_row['name']; ?></a></li>
                    <?php
                }
                ?>
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
      <div id="sequence" class="seq">
        <div class="seq-screen">
          <ul class="seq-canvas">
            <!-- 輪播 -->
              <?php
              $banner_sql = "SELECT * FROM banner WHERE `status`='1'";
              $banner_res = mysql_query($banner_sql);
              while ($banner_row = mysql_fetch_array($banner_res))
              {
                  ?>
                  <li>
                      <div class="seq-model">
                          <picture>
                              <img src="<?php echo 'admin/'.$banner_row['img']; ?>" />
                              <source media="(min-width: 500px)" srcset="img/ImgPrev11479312142.jpg" >
                          </picture>
                      </div>
                      <div class="seq-title">
                          <!--<span data-seq>標題1</span>-->
                          <!--<h2 data-seq><?php //echo $banner_row['title']; ?></h2>-->
                          <!--<p data-seq>標題3</p>-->
                          <!--<a data-seq href="#" class="aa-shop-now-btn aa-secondary-btn">SHOP NOW</a>-->
                      </div>
                  </li>
                  <?php
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
  if ($url == "")
  {
      include("main2.php");
  }
  else
  {
      include_once('404.php');
  }
  ?>

  <div class="my_nav">
      <ul>
          <li>
              <a href="index.php"><span><img src="img/icon/home.png"></span><span>首頁</span></a>
          </li>
          <li style="position:relative;">
              <a href="index.php?url=member_center"><span><img src="img/icon/foot-member.png"></span><span>會員專區</span></a>
          </li>
          <li>
              <a id='start_app_btn' href="javascript:void(0);"><span><img src="img/icon/mobile.png"></span><span>下載/開啟APP</span></a>
          </li>
          <li>
              <a href="index.php?url=cart"><span><img src="img/icon/shopcart_icon.png"></span><span>購物車</span></a>
          </li>
      </ul>
  </div>

  <span id="m_id" style="display: none;">
      <?php
          if(isset($_SESSION["member_no"]))
          {
              $member_id = $_SESSION["member_no"];
          }
          else
          {
              $member_id = $_SESSION["fb_id"];
          }
      ?>
  </span>

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

    $(function()
    {
        if($(window).width() < 767)
        {
            $("#scroll_tp").remove();
            $("#signout_block").hide();
            $(".ax").css('height','1800px');
        }
    })

    $("#start_app_btn").click(function()
    {
        var url = $("#url").text();
        var pid = $("#pid").text();
        $(this).attr('href','intent://jjl/openwith?url='+url+'&pid='+pid+'#Intent;scheme=myapp;package=com.nkfust.firstshop;S.browser_fallback_url=http%3A%2F%2Fwww.google.com;end');
        //alert(window.location.href);
        //$(this).attr('href','myapp://jjl/openwith?url='+url+'&pid='+pid);
    });
	
	function getFBID(fb_email, fb_id, imei, regid)
	{
		if(fb_id != "" && imei != "")
		{
			$.ajax
			({
				url:"ajax.php", //接收頁
				type:"POST", //POST傳輸
				data:{type:"set_regid", fb_id:fb_id, regid:regid}, // key/value
				dataType:"text", //回傳形態
				success:function(i) //成功就....
				{
			
				},
				error:function()//失敗就...
				{
				}
			});
		}
	}
	
	function getIMEI(imei,regid)
	{
		//alert(imei + " / " + regid);
	}
</script>