<style>
    td:hover {
        background-color: pink;
        cursor: pointer;
    }
</style>

<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li class="active">會員專區</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->

<!-- Cart view section -->
<section id="aa-myaccount">
    <div class="container">
        <div class="row">
            <table class="table table-bordered text-center" style="margin-bottom: 200px; padding-top: 1px;">
                <tr>
                    <td <?php if (@$_SESSION['device'] == 'mobile') {
                        echo "width='120'";
                    } ?> class="cp tab-width" onclick="location.href='index.php?url=member_info';">
                        <div class="member-icon">
                            <img src="img/icon/member.png">
                        </div>
                        <span class="member-icon-text">個人資料</span>
                    </td>

                    <td class="cp tab-width" onclick="location.href='index.php?url=TrackList';">
                        <div class="member-icon">
                            <img src="img/icon/heart.png">
                        </div>
                        <span class="member-icon-text">追蹤清單</span>
                    </td>

                    <td class="cp tab-width" onclick="location.href='index.php?url=cart';">
                        <div class="member-icon">
                            <img src="img/icon/shopcart.png">
                        </div>
                        <span class="member-icon-text">購物車</span>
                    </td>
                </tr>

                <tr>

                    <td class="cp tab-width" onclick="location.href='index.php?url=order_search';">
                        <div class="member-icon">
                            <img src="img/icon/order.png">
                        </div>
                        <span class="member-icon-text">訂單查詢</span>
                    </td>

                    <td class="cp tab-width" onclick="location.href='index.php?url=to_manager';">
                        <div class="member-icon">
                            <img src="img/icon/add_manager.png">
                        </div>
                        <span class="member-icon-text">團購家族-申請</span>
                    </td>

                </tr>

                <tr>
                    <?php if ($manager_no !== '') : ?>
                        <td class="cp tab-width" onclick="location.href='index.php?url=crew';">
                            <div class="member-icon">
                                <img src="img/icon/crew.png" style="height: 50px;">
                            </div>
                            <span class="member-icon-text">家族成員</span>
                        </td>
                    <?php endif; ?>
                    <?php if ($manager_no !== '') : ?>
                        <td class="cp tab-width" onclick="location.href='index.php?url=bonus';">
                            <div class="member-icon">
                                <img src="img/icon/points.png" style="height: 50px;">
                            </div>
                            <span class="member-icon-text">獎勵查詢</span>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php
                if ($_SESSION['device'] !== 'mobile') {
                    ?>
                    <tr>
                        <td colspan="3" class="cp tab-width" onclick="location.href='index.php?url=logout';">
                            <div class="member-icon">
                                <img src="img/icon/logout.png">
                            </div>
                            <span class="member-icon-text">登出</span>
                        </td>
                    </tr>
                    <?php
                }else {
                    ?>
                    <tr>
                        <td colspan="3" class="cp tab-width" onclick="mobileLogout();">
                            <div class="member-icon">
                                <img src="img/icon/logout.png">
                            </div>
                            <span class="member-icon-text">特殊登出</span>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
</section>
<!-- / Cart view section -->

<script>
    if ($(window).width() < 767) {
        $("html,body").scrollTop(550);
    }
    else {
        $("html,body").scrollTop(700);
    }

    function add_mg() {
        window.javatojs.aaa();
    }

    function mobileLogout() {
        ajax17mai('Member','MobileLogout');
    }
</script>
