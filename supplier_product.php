<?php

use Base17Mai\Supplier;
use function Base17Mai\take, Base17Mai\displayToString;

$supplierID = take('sid', false, 'GET');
$supplier = new Supplier($supplierID);
$Products = $supplier->ReleaseProduct();
print '<!-- marked -->';
$contentHTML = displayProduct($Products['content']);
if ($Products['count'] === 0) $contentHTML = '<div style="text-align: center;"><span style="color: #FF0000;">很抱歉，此供應商暫無商品</span></div>';
// print '<!-- ' . print_r($Products['content'], true) . ' -->';
@$sid = $_GET['sid'];
$product_sql = "SELECT * FROM supplier left join product on supplier.id = product.vendorID WHERE supplier.id = '$sid' AND product.Prelease = 1;";
$product_res = mysql_query($product_sql);

function displayProduct($Products = array())
{
    $result = '';

    if (!is_array($Products)) {
        return '系統錯誤';
    }
    if (count($Products) === 0) {
        $result = '<div style="text-align: center;"><span style="color: #FF0000;">很抱歉，此供應商暫無商品</span></div>';
    } else {
        foreach ($Products as $product) {
            $result .= displayToString('product/ListEntry.php', $product);
        }
    }
    return $result;
}

?>
<!-- product category -->
<section id="aa-promo" class="product_content" style="margin-bottom: 150px;">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="aa-promo-area">
                    <div class="tab-content ax">
                        <?= $contentHTML ?>
                    </div>
                </div>
            </div>
        </div>
</section>


<script>
    function favorite(id) {
        ajax17mai('Member', 'Favorite', {}, {memberNO: '<?= $member_id ?>', productID: id});
    }

    function add_cart(id) {
        ajax17mai('Consumer', 'AddToCart', {}, {member_id: '<?= $member_id ?>', productID: id, Quantity: 1, spec: ''});
    }

    function add_favorite(id) {
        $.ajax
        ({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "favorite", pid: id}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                if (i == 1) {
                    $("#fav_btn" + id).find("img").attr('src', 'img/icon/add.png');
                } else if (i == 0) {
                    $("#fav_btn" + id).find("img").attr('src', 'img/icon/clean.png');
                } else {
                    if ($(window).width() < 767) {
                        window.javatojs.showInfoFromJs('請先登入或成為會員，才能使用此功能');
                    } else {
                        alert('請先登入或成為會員，才能使用此功能');
                    }
                }
            },
            error: function ()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    }

    function add_cart(id) {
        $.ajax
        ({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "cart", pid: id}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                if (i == 1) {
                    $("#cart_btn" + id).find("img").attr('src', 'img/icon/add_cart.png');
                } else if (i == 0) {
                    $("#cart_btn" + id).find("img").attr('src', 'img/icon/clean_cart.png');
                } else {
                    if ($(window).width() < 767) {
                        window.javatojs.showInfoFromJs('請先登入或成為會員，才能使用此功能');
                    } else {
                        alert('請先登入或成為會員，才能使用此功能');
                    }
                }
            },
            error: function ()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    }

    $(function () {
        $.ajax
        ({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "favorite_search"}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                var id = i.split(",");
                for (var n = 0; n < id.length; n++) {
                    $("#fav_btn" + id[n]).find("img").attr('src', 'img/icon/add.png');
                }
            },
            error: function ()//失敗就...
            {
                //alert("ajax失敗");
            }
        });

        $.ajax
        ({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "cart_search"}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                var id = i.split(",");
                for (var n = 0; n < id.length; n++) {
                    $("#cart_btn" + id[n]).find("img").attr('src', 'img/icon/add_cart.png');
                }
            },
            error: function ()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    });
</script>