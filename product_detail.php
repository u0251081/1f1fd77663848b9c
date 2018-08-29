<?php

use Base17Mai\Product, Base17Mai\Member;
use function Base17Mai\take;

$productID = take('id');
$Product = new Product();
$PID = $Product->getOperatorCode($productID);
$images = $Product->getImage($PID);
$imagesHTML = imagesToHtml($images);
$history = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

function imagesToHtml($images = [])
{
    $result = '';
    $result .= '<div class="simpleLens-container">';
    $result .= '<div class="simpleLens-big-image-container">';
    $result .= "<a data-lens-image=\"admin/{$images['Cover']}\"";
    return $result;
}

function specificationHTML($specs = [])
{
    if (count($specs) === 0) return false;
    $result = '';
    $result .= "商品規格：<select name=\"spec\" style=\"height: 25px;\">";
    $result .= "<option value=\"none\">請選擇規格";
    foreach ($specs as $key => $value) {
        $result .= "<option value=\"{$value['specCode']}\">{$value['specification']}";
    }
    $result .= "</select>&nbsp;&nbsp;&nbsp;";
    return $result;
}

?>
<script src="js/clipboard.min.js"></script>
<style>
    .my_cart_btn {
        border: 1px solid #ccc;
        color: #555;
        display: inline-block;
        font-size: 14px;
        font-weight: bold;
        letter-spacing: 0.5px;
        margin-top: 5px;
        padding: 10px 15px;
        text-transform: uppercase;
        transition: all 0.5s ease 0s;
        cursor: pointer;
    }

    .my_add_cart {
        border-color: #CC6060;
        color: #CC6060;
    }

    #copy_url {
        word-wrap: break-word;
        word-break: normal;
    }
</style>


<?php

if (!empty($_GET['fb_no'])) {

    $fb_no = $_GET['fb_no'];

} else {
    $fb_no = 0;
}

$storefb_no = 0;
if (preg_match("/-/i", $fb_no)) {
    $storefb_no = $fb_no - '-';
}


if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $myip = $_SERVER['HTTP_CLIENT_IP'];
} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $myip = $_SERVER['REMOTE_ADDR'];
}
$_SESSION['ip'] = $myip;
$time = date('Y-m-d H:i:s');

if (!empty($_GET['fb_no'])) {
    $fb_no = $_GET['fb_no'];
    $sqlfb = "SELECT * FROM fans_data WHERE id=$storefb_no";
    $resfb = mysql_query($sqlfb);
    $rowfb = mysql_fetch_array($resfb);
    if ($rowfb[0] != "") {
        $sqlpre = "SELECT * FROM fb_fans WHERE name='$rowfb[1]' AND ip='$myip' AND page='$fb_no'";
        $respre = mysql_query($sqlpre);
        $rowpre = mysql_fetch_array($respre);
        $sqltime = "SELECT MAX(storetime) FROM fb_fans WHERE name='$rowfb[1]' AND ip='$myip'";
        $restime = mysql_query($sqltime);
        $rowtime = mysql_fetch_array($restime);
        // echo $rowpre['ip'];
        // echo $rowtime[0];
        $timespace = (strtotime($time) - strtotime($rowtime[0]));
        if (empty($rowpre)) {
            $sqlfan = "INSERT INTO fb_fans(name,page,storetime,ip) VALUES ('$rowfb[1]','$fb_no','$time','$myip')";
            $resfan = mysql_query($sqlfan);
            // echo 1;
        } else if ($rowpre['ip'] == $myip && $rowpre['name'] == $rowfb[1] && $timespace > 1800) {
            $sqlfan = "INSERT INTO fb_fans(name,page,storetime,ip) VALUES ('$rowfb[1]','$fb_no','$time','$myip')";
            $resfan = mysql_query($sqlfan);
            // echo 2 ;
        }


    }
}

?>


<!-- product category -->
<section id="aa-product-details" class='product_content2'>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="aa-product-details-area">
                    <div class="aa-product-details-content">
                        <div class="row">
                            <!-- Modal view slider -->
                            <?php
                            $img_ary = array();
                            // @$id = $_GET['product_id'];
                            $sql = "SELECT * FROM productimage where productID = '{$PID}';";
                            $res = mysql_query($sql);
                            while ($row = mysql_fetch_array($res)) {
                                $img_ary[] = $row['picture'];
                            }
                            // $img_ary = $images;
                            ?>
                            <div class="col-md-5 col-sm-5 col-xs-12">
                                <div class="aa-product-view-slider">
                                    <div id="demo-1" class="simpleLens-gallery-container">
                                        <div class="simpleLens-container">
                                            <div class="simpleLens-big-image-container">
                                                <?php
                                                if (is_file("admin/" . $img_ary[0])) {
                                                    ?>
                                                    <a data-lens-image="admin/<?php echo $img_ary[0]; ?>"
                                                       class="simpleLens-lens-image">
                                                        <img src="admin/<?php echo $img_ary[0]; ?>"
                                                             class="simpleLens-big-image">
                                                    </a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="simpleLens-thumbnails-container">
                                            <?php
                                            if (is_file("admin/" . $img_ary[0])) {
                                                ?>
                                                <a data-big-image="admin/<?php echo $img_ary[0]; ?>"
                                                   data-lens-image="admin/<?php echo $img_ary[0]; ?>"
                                                   class="simpleLens-thumbnail-wrapper" href="javascript:void(0);">
                                                    <img src="admin/<?php echo $img_ary[0]; ?>" width="50" height="55">
                                                </a>
                                                <?php
                                            }

                                            if (is_file("admin/" . $img_ary[1])) {
                                                ?>
                                                <a data-big-image="admin/<?php echo $img_ary[1]; ?>"
                                                   data-lens-image="admin/<?php echo $img_ary[1]; ?>"
                                                   class="simpleLens-thumbnail-wrapper" href="javascript:void(0);">
                                                    <img src="admin/<?php echo $img_ary[1]; ?>" width="50" height="55">
                                                </a>
                                                <?php
                                            }

                                            if (is_file("admin/" . $img_ary[2])) {
                                                ?>
                                                <a data-big-image="admin/<?php echo $img_ary[2]; ?>"
                                                   data-lens-image="admin/<?php echo $img_ary[2]; ?>"
                                                   class="simpleLens-thumbnail-wrapper" href="javascript:void(0);">
                                                    <img src="admin/<?php echo $img_ary[2]; ?>" width="50" height="55">
                                                </a>
                                                <?php
                                            }

                                            if (is_file("admin/" . $img_ary[3])) {
                                                ?>
                                                <a data-big-image="admin/<?php echo $img_ary[3]; ?>"
                                                   data-lens-image="admin/<?php echo $img_ary[3]; ?>"
                                                   class="simpleLens-thumbnail-wrapper" href="javascript:void(0);">
                                                    <img src="admin/<?php echo $img_ary[3]; ?>" width="50" height="55">
                                                </a>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal view content -->
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?php
                                // $sql = "SELECT * FROM product AS a JOIN(SELECT p_id,price,web_price,sell_id FROM price) AS b ON a.id = b.p_id AND b.sell_id='1' WHERE a.id='$id'";
                                $sql = "select * from product where id = '{$productID}';";
                                $res = mysql_query($sql);
                                $row = mysql_fetch_array($res);
                                if ($row['Prelease'] === '0') {
                                    ?>
                                    <script>
                                        alert('此商品已經下架');
                                        window.location.href = 'index.php';
                                    </script>
                                    <?php
                                }
                                $over_qty = $row['QuantityRemain']; //總庫存-已賣出
                                $trackStatus = (new Member())->checkTrackStatus($member_id, $row['id']);
                                ?>
                                <div class="aa-product-view-content">
                                    <h3>商品名稱：<?= $row['PName'] ?></h3>
                                    <div class="aa-price-block">
                                    </div>
                                    <div class="aa-price-block">
                                        <!--
                                        <span class="aa-product-view-price">定價：
                                            <del>
                                                NT$<span><?= $row['unitPrice'] ?></span>
                                            </del>
                                        </span>
                                        <br>
                                        -->
                                        <span class="aa-product-view-price">
                                            定價：NT$<span id='price'><?= $row['unitPrice'] ?></span>
                                        </span>
                                        <br>
                                        <span class="aa-product-view-price">
                                            點數：<?= $row['bonus'] ?>
                                        </span>
                                    </div>
                                    <div id="description" style="height: 15vh;">
                                        <p><?= $row['description'] ?></p>
                                    </div>
                                    <form id="productForm" style="display: flex; flex-direction: row; flex-wrap: wrap;">
                                        <input type="hidden" name="productID" value="<?= $productID ?>">
                                        <div class="aa-prod-quantity">
                                            <?php
                                            $specifications = $Product->ListSpecification($PID);
                                            print specificationHTML($specifications);
                                            ?>
                                        </div>
                                        <div class="aa-prod-quantity" style="">
                                            購買數量
                                            <select id="Quantity" name="Quantity" style="width: 101px; height: 25px;">
                                            </select>
                                            <p class="aa-prod-category">
                                                剩餘:
                                                <a href="javascript:void(0);" id="Remain">
                                                    <?php print ($over_qty <= 0) ? 0 : $over_qty; ?>
                                                </a>
                                            </p>
                                        </div>
                                    </form>
                                    <div class="aa-prod-view-bottom">
                                        <?= $trackStatus ?>&nbsp;&nbsp;&nbsp;
                                        <span class="my_cart_btn" onclick="add_cart(<?= $productID ?>);">加入購物車</span>
                                        &nbsp;&nbsp;&nbsp;
                                        <!-- <span class="my_cart_btn" name="add" b_type="1">加入追蹤清單</span>&nbsp;&nbsp;&nbsp; -->
                                        <span class="my_cart_btn" id="directBuy">直接購買</span>
                                        &nbsp;&nbsp;&nbsp;
                                        <span class="my_cart_btn" id="BackList">返回</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="aa-product-details-bottom">
                        <ul class="nav nav-tabs" id="myTab2">
                            <li><a href="#information" data-toggle="tab">詳細介紹</a></li>
                            <div id="information"><?= $row['productInformation'] ?></div>
                            <?php
                            if ($row['youtube'] != "") {
                                ?>
                                <li><a href="#review" data-toggle="tab">商品廣告</a></li>
                                <?php
                            }
                            ?>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div align="center" class="tab-pane fade " id="review">
                                <br>
                                <div class="video-container">
                                    <iframe src="<?php echo $row['youtube']; ?>" frameborder="0"
                                            allowfullscreen></iframe>
                                </div>
                            </div>
                            <br><br><br>
                        </div>
                    </div>
                    <!-- Related product -->
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).on('change', 'select[name="spec"]', refrechQuantity);

    $(document).on('click', 'span#directBuy', function () {
        directBuy(this)
    });

    $(document).on('click', 'span#BackList', function () {
        window.location.href = '<?= $history ?>';
    });


    function favorite(id) {
        ajax17mai('Member', 'Favorite', {}, {memberNO: '<?= $member_id ?>', productID: id});
    }

    function directBuy(element) {
        let formData = getFormData('form#productForm');
        console.log(formData);
        ajax17mai('Consumer', 'DirectBuy', {}, formData);
    }

    (function () {
        refrechQuantity();
    })();

    function add_cart(id) {
        ajax17mai('Consumer', 'AddToCart', {}, getFormData('#productForm'));
    }

    function refrechQuantity() {
        ajax17mai('Product', 'RefreshQuantity', {}, getFormData('#productForm'));
    }
</script>
<form id="order_form" action="index.php?url=pay_check" method="post">
    <input type="hidden" name="p_id" value="<?php echo @$_GET['product_id']; ?>">
    <input type="hidden" name="manager_id" value="<?php echo @$_SESSION['share_manager_no']; ?>">
    <input type="hidden" name="vip_id" value="<?php echo @$_SESSION['share_vip_id']; ?>">
    <input type="hidden" name="fb_no" value="<?php echo $fb_no ?>">
    <input type="hidden" name="pay_qty" id="pay_qty">
</form>

<span id='url' style='display:none;'><?php echo @$_GET['url']; ?></span>
<span id='pid' style='display:none;'><?php echo @$_GET['product_id']; ?></span>
<span id='vip_id' style='display:none;'><?php echo @$_GET['vip_id']; ?></span>

<?php
if (empty($_SESSION['share_manager_no']) && !empty($_GET['manager_id'])) {
    //只要有收到行銷經理id先存入session，怕有心人改get的參數因此讓session只存第一次
    //此session是導購的判斷，只要此session有值得交易都算導購，在收到歐付寶付款通知後再unset刪除此session
    $_SESSION['share_manager_no'] = $_GET['manager_id'];
}

if (empty($_SESSION['share_vip_id']) && !empty($_GET['vip_id'])) {
    //只要有收到vip_id先存入session，怕有心人改get的參數因此讓session只存第一次
    //此session是vip再次分享的判斷，只要此session即可知道是誰分享，在收到歐付寶付款通知後再unset刪除此session
    $_SESSION['share_vip_id'] = $_GET['vip_id'];
}


if (isset($_GET['manager_id']) && @$_SESSION['manager_no'] != $_GET['manager_id']) {
    //如果url有行銷經理id且該id不等於自己的行銷經理id
    ?>
    <span id='manager_id' style='display: none;'>
        <?php echo @$_GET['manager_id']; ?>
    </span>
    <span id='my_manager_id' style='display: none;'>
        <?php echo @$_SESSION['manager_no']; ?>
    </span>
    <?php
} else {
    ?>
    <span id='my_manager_id' style='display: none;'>
        <?php echo @$_SESSION['manager_no']; ?>
    </span>
    <?php
}

//以下為瀏覽紀錄處理
if (isset($_SESSION['history_ary'])) {
    $key = array_search($productID, $_SESSION['history_ary']);
    if ($key !== false) {
        //unset($_SESSION['history_ary'][$key]);
        //array_values($_SESSION['history_ary']);
        while (count($_SESSION['history_ary']) > 5) {
            array_pop($_SESSION['history_ary']);
        }
    } else {
        array_push($_SESSION['history_ary'], $productID);
    }
} else {
    $_SESSION['history_ary'] = array();
    array_push($_SESSION['history_ary'], $productID);
}
?>

<!-- 彈出視窗 -->
<div class="modal fade" id="copy_url_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4>請複製下列網址，並把它分享給您的朋友，謝謝</h4>
                <div id="copy_url"></div>
                <button id='copy_btn' style="margin: 10px;" class="btn btn-primary pull-right"
                        data-clipboard-target="#copy_url">複製
                </button>
                <span id='copy_success' style="display: none;">複製成功</span>
            </div>
        </div><!-- /.彈出視窗內容 -->
    </div><!-- /.彈出視窗結束 -->
</div>

<script>
    var over_qty = $("#over_qty").text().trim();
    var clipboard = new Clipboard('#copy_btn');
    clipboard.on('success', function (e) {
        if ($("#device").text() == 'mobile') {
            window.javatojs.showInfoFromJs('複製成功');
            $('#copy_url_modal').modal('hide');
        }
        else {
            $("#copy_success").show();
            //$('#copy_url_modal').modal('hide');
        }
    });

    $("#url_btn").click(function () {
        var m_id = $("#m_id").text().trim(); //會員id判斷是否從fb登入
        var my_manager_id = $("#my_manager_id").text().trim(); //如果沒有收到行銷經理id，且自己也是行銷經理則載入自己的id
        var manager_id = $("#manager_id").text().trim(); //判斷是否有收到行銷經理id
        var device = $("#device").text().trim(); //判斷目前載具
        var vip_id = $("#vip_id").text().trim(); //判斷有無vip_id

        if (manager_id != "" && vip_id == '') {
            $("#copy_success").hide();
            $("#copy_url").text(window.location.href + '&vip_id=' + m_id);
        }
        else if (manager_id != "" && vip_id != "") {
            $("#copy_success").hide();
            $("#copy_url").text(window.location.href);
        }
        else {
            if (my_manager_id != "") {
                $("#copy_success").hide();
                $("#copy_url").text(window.location.href + '&manager_id=' + my_manager_id);
            }
            else {
                $("#copy_success").hide();
                $("#copy_url").text(window.location.href);
            }
        }
    });

    $("span[name='add_cart_btn']").click(function () {
        var pid = $("#pid").text();
        var p_qty = $("#p_qty").val();
        var p_price = $("#price").text();
        if ($(this).attr('b_type') == 0) {
            if (over_qty <= 0) {
                if ($("#device").text() == 'mobile') {
                    window.javatojs.showInfoFromJs('此商品已完售，無法加入購物車');
                }
                else {
                    if ($("#m_id").text().trim() != '') {
                        alert('此商品已完售，無法加入購物車');
                    }
                    else {
                        alert('請先登入才能使用此功能');
                    }
                }
            }
            else {
                $.ajax
                ({
                    url: "ajax.php", //接收頁
                    type: "POST", //POST傳輸
                    data: {type: "p_detial_cart", pid: pid, p_qty: p_qty, p_price: p_price}, // key/value
                    dataType: "text", //回傳形態
                    success: function (i) //成功就....
                    {
                        if (i == 1) {
                            if ($("#device").text() == 'mobile') {


                                window.javatojs.showInfoFromJs('加入購物車成功');
                                <?php
                                if (!empty($_GET['fb_no'])) {

                                    $_SESSION['share_fb_no'] = $fb_no;
                                }
                                ?>

                            }
                            else {
                                <?php
                                if (!empty($_GET['fb_no'])) {
                                    $_SESSION['share_fb_no'] = $fb_no;
                                }
                                ?>
                                alert('加入購物車成功');

                            }
                        }
                        else if (i == 2) {
                            alert('請先登入才能使用此功能');
                        }
                    },
                    error: function ()//失敗就...
                    {

                    }
                });
            }
        }
        else if ($(this).attr('b_type') == 1) {
            $.ajax
            ({
                url: "ajax.php", //接收頁
                type: "POST", //POST傳輸
                data: {type: "p_detial_favorite", pid: pid}, // key/value
                dataType: "text", //回傳形態
                success: function (i) //成功就....
                {
                    if (i == 1) {
                        if ($("#device").text() == 'mobile') {
                            window.javatojs.showInfoFromJs('商品已再追蹤清單中');
                        }
                        else {
                            alert('商品已再追蹤清單中');
                        }
                    }
                    else if (i == 2) {
                        if ($("#device").text() == 'mobile') {
                            window.javatojs.showInfoFromJs('追蹤成功');
                        }
                        else {
                            alert('追蹤成功');
                        }

                        if ($("span[name='add_cart_btn']").attr('b_type') == 1) {
                            $(this).attr('class', 'my_cart_btn my_add_cart');
                        }
                    }
                    else if (i == 3) {
                        alert('請先登入才能使用此功能');
                    }
                },
                error: function ()//失敗就...
                {

                }
            });
            //$(this).toggleClass('my_add_cart');
        }
        else if ($(this).attr('b_type') == 2) {
            if (over_qty <= 0) {
                if ($("#device").text() == 'mobile') {
                    window.javatojs.showInfoFromJs('此商品已完售，請購買其他商品');
                }
                else {
                    if ($("#m_id").text().trim() != '') {
                        alert('此商品已完售，請購買其他商品');
                    }
                    else {
                        $("form#order_form").submit();
                    }
                }
            }
            else {
                $("form#order_form").submit();
            }
        }
    });

    $('.modal-body').css('max-height', window.innerHeight).css('overflow', 'auto');

    if ($(window).width() < 767) {
        $("html,body").scrollTop(330);
    }
    else {
        $("html,body").scrollTop(550);
    }

    $("#p_qty").change(function () {
        if (this.value > 1) {
            $("#pay_qty").val(this.value);
        }
        else {
            $("#pay_qty").val('1');
        }
    });

    if ($('#pay_qty').val() == "") {
        $("#pay_qty").val('1');
    }
</script>