<style>
    td:hover
    {
        background-color: pink;
        cursor:pointer;
    }
</style>

<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li class="active">行銷經理專區</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->

<?php
if(@$_SESSION['manager_no'] !="")
{
    ?>
    <!-- Cart view section -->
    <section id="aa-myaccount">
        <div class="container">
            <div class="row">
                <table class="table table-bordered text-center" style="margin-bottom: 200px; padding-top: 1px;">
                    <tr>
                        <td class="cp tab-width" onclick="location.href='index.php?url=show_qr';">
                            <div class="member-icon">
                                <img src="img/icon/qr.png">
                            </div>
                            <span class="member-icon-text">顯示QR碼</span>
                        </td>
                        <td class="cp tab-width" onclick="location.href='index.php?url=profit_search';">
                            <div class="member-icon">
                                <img src="img/icon/order.png">
                            </div>
                            <span class="member-icon-text">查看分享</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="cp tab-width" onclick="location.href='index.php?url=member_center';">
                            <div class="member-icon">
                                <img src="img/icon/logout.png">
                            </div>
                            <span class="member-icon-text">返回會員專區</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </section>
    <!-- / Cart view section -->
    <?php
}
else
{
    ?>
    <script>
        alert('請先申請成為行銷經理');
        location.href='index.php?url=member_center';
    </script>
    <?php
}
?>

<script>
    $("html,body").scrollTop(750);
</script>
