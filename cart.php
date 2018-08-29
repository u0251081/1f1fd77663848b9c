<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2018/8/29
 * Time: 上午 03:20
 */

use function Base17Mai\take;

function getProductFromCart()
{
    $SQL = 'select id as CID, productID, Quantity, specCode from shoppingcart where member_no = :member_no;';
    $Para['member_no'] = take('member_no', '', 'session');
    $result = pdo_select_sql($SQL, $Para);
    return $result;
}

function getProductInformation($ProductList = [])
{
    if (is_array($ProductList)) {
        $result = [];
        foreach ($ProductList as $value) {
            $SQL = 'select id as PID, productID, PName, unitPrice, Prelease from product where id = :id;';
            $para = ['id' => $value['productID']];
            $rst = pdo_select_sql($SQL, $para);
            if (isset($rst[0])) {
                $SQL = 'select specification from productspec where productID = :productID and specCode = :specCode;';
                $para = ['productID' => $rst[0]['productID'], 'specCode' => $value['specCode']];
                $rst2 = pdo_select_sql($SQL, $para);
                $rst[0]['specification'] = isset($rst2[0]) ? $rst2[0]['specification'] : '';
                $rst[0]['Prelease'] = ($rst[0]['Prelease'] === '1') ? '上架' : '下架';
                $rst[0] = array_merge($rst[0], $value);
                $result[] = $rst[0];
            }
        }
        return $result;
    } else {
        return false;
    }
}

function generateTable($ProductList = [])
{
    if (is_array($ProductList)) {
        $result = '';
        foreach ($ProductList as $key => $value) {
            $result .= "<tr id=\"{$value['CID']}\">";
            $result .= '<td>';
            $result .= "<a class=\"remove remove_cart\" href=\"javascript:void(0) pid\"{$value['PID']}\">";
            $result .= "<i class=\"fa fa-close\"></i>";
            $result .= "</a>";
            $result .= "</td>";
            $result .= '<td>';
            $result .= "<a class=\"aa-cart-title\" href=\"index.php?url=product_detail&id={$value['PID']}\">";
            $result .= $value['PName'];
            $result .= "</a>";
            $result .= "<input type='hidden' name='productID' value='{$value['PID']}'>";
            $result .= "</td>";
            $result .= '<td>';
            $result .= "<a>";
            $result .= $value['Prelease'];
            $result .= "</a>";
            $result .= "</td>";
            $result .= '<td>';
            $result .= "<a>";
            $result .= $value['specification'];
            $result .= "</a>";
            $result .= "<input type='hidden' name='specCode' value='{$value['specCode']}'>";
            $result .= "</td>";
            $result .= '<td>';
            $result .= "<input type=\"number\" name=\"Quantity\" value=\"{$value['Quantity']}\" >";
            $result .= "</td>";
            $result .= '<td id="price">';
            $result .= $value['unitPrice'];
            $result .= "</td>";
            $result .= '<td id="amount">';
            $result .= "</td>";
            $result .= '</tr>';
        }
        return $result;
    }
}

$shoppingList = getProductFromCart();
$productList = getProductInformation($shoppingList);
$cartTable = generateTable($productList);

?>

<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li><a href="index.php?url=member_center">會員專區</a></li>
                <li class="active">購物車</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->

<!-- Cart view section -->
<section id="cart-view">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="cart-view-area" style="margin-bottom: 100px; padding-top: 1px;">
                    <div class="cart-view-table">
                        <form id="cartForm" method="post">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>取消</th>
                                        <th>名稱</th>
                                        <th>狀態</th>
                                        <th>規格</th>
                                        <th>數量</th>
                                        <th>金額</th>
                                        <th>小計</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?= $cartTable ?>
                                    <tr>
                                        <td colspan="7" class="aa-cart-view-bottom">
                                            <div class="aa-cart-coupon" style="">
                                                <input class="aa-cart-view-btn" type="button" id="btn1" value="繼續購物">
                                            </div>
                                            <div class="aa-cart-coupon">
                                            </div>
                                            <input class="aa-cart-view-btn" type="button" id="btn2" value="修改數量">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <!-- Cart Total view -->
                        <div class="cart-view-total">
                            <h4>購物車合計</h4>
                            <table class="aa-totals-table">
                                <tbody>
                                <tr>
                                    <th>合計</th>
                                    <td>NT$<span id="price_total"></span></td>
                                </tr>
                                </tbody>
                            </table>
                            <a href="javascript:void(0)" id="btn3" class="aa-cart-view-btn">前往結帳</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<form id="cart_form" action="index.php?url=pay_check" method="post">
    <input type="hidden" name="cart_pid" id="cart_pid">
    <input type="hidden" name="cart_price" id="cart_price">
    <input type="hidden" name="cart_qty" id="cart_qty">
    <input type="hidden" name="manager_id" value="<?php echo @$_SESSION['share_manager_no']; ?>">
    <input type="hidden" name="fb_no" value="<?php echo @$_SESSION['share_fb_no']; ?>">
    <input type="hidden" name="vip_id" value="<?php echo @$_SESSION['share_vip_id']; ?>">
</form>
<!-- / Cart view section -->

<script>

    (function () {
        refreshAmount();
    })();

    function getCartData() {
        let result = {};
        $('tr[id]').each(function (ind, ele) {
            let tmpData = {};
            let element = $(ele);
            tmpData.productID = String(element.find('input[name="productID"]').val());
            tmpData.Quantity = String(element.find('input[name="Quantity"]').val());
            tmpData.specCode = String(element.find('input[name="specCode"]').val());
            result[ind] = tmpData;
        });
        return result;
    }

    function refreshAmount() {
        let total = 0;
        $('tr[id]').each(function (ind, ele) {
            let element = $(ele);
            let Quantity = Number(element.find('input[name="Quantity"]').val());
            let UnitPrice = Number(element.find('td#price').text());
            let amount = Quantity * UnitPrice;
            element.find('td#amount').text(amount);
            total += amount;
        });
        $('span#price_total').text(total);
    }

    $(document).on('click', 'a.remove_cart', function () {
        let CartID = $(this).closest('tr').attr('id');
        ajax17mai('Consumer', 'RemoveFromCart', {}, {CID: CartID});
    });
    $(document).on('click', 'input#btn1', function () {
        location.href = "<?= $history ?>";
    });

    $(document).on('click', 'input#btn2', function () {
        ajax17mai('Consumer', 'UpdateCart', {}, {cartItem: getCartData()});
    });

    $(document).on('click', 'input#btn3', function () {
    });

    var pid;
    var price_ary = Array();

    $(function () {
        // 隱藏廣告欄
        $("#aa-slider").remove();
        $("html,body").scrollTop(70);
    });

    $("#btn").click(function () {
        var pid_arr = Array();
        $("span[name='pid']").each(function (i) {
            pid_arr[i] = $(this).text().trim();
        });

        var qty_arr = Array();
        $("input[name='qty']").each(function (i) {
            qty_arr[i] = $(this).val().trim();
        });

        var price_arr = Array();
        $("td[name='price']").each(function (i) {
            price_arr[i] = $(this).text().trim(price_arr);
        });

        $.ajax
        ({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "update_cart", pid_arr: pid_arr, qty_arr: qty_arr, price_arr: price_arr}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                if (i == 1) {
                    if ($("#device").text() == 'mobile') {
                        window.javatojs.showInfoFromJs('更新成功');
                        window.location.href = 'index.php?url=cart';
                    }
                    else {
                        alert('更新成功');
                        window.location.href = 'index.php?url=cart';
                    }
                }
            },
            error: function ()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    });

    $("a[name='remove_cart']").click(function () {
        pid = $(this).attr('pid');
        if ($("#device").text() == 'mobile') {
            window.javatojs.myconfirm('cart');
        }
        else {
            if (confirm('是否要從您的追蹤清單內移除?')) {
                var price = $(this).parent().siblings("td[name='price']").text().trim();
                var qty = $(this).parent().siblings('td').find('input[name="qty"]').val();
                var price_total = $("#price_total").text();
                var num = price * qty;
                $("#price_total").text(price_total - num);
                $.ajax
                ({
                    url: "ajax.php", //接收頁
                    type: "POST", //POST傳輸
                    data: {type: "remove_cart", pid: pid}, // key/value
                    dataType: "text", //回傳形態
                    success: function (i) //成功就....
                    {
                        if (i == 1) {
                            $("#tr" + pid).remove();
                        }
                        else {
                            alert('意外的錯誤，請重新操作');
                        }
                    },
                    error: function ()//失敗就...
                    {
                        //alert("ajax失敗");
                    }
                });
            }
        }
    });

    function dialod_res(t) {
        if (t == 'yes') {
            var price = $("a[name='remove_cart']").parent().siblings("td#td" + pid).text().trim();
            var qty = $("a[name='remove_cart']").parent().siblings("td#td" + pid).next().find('input[name="qty"]').val();
            var price_total = $("#price_total").text();
            var num = price * qty;
            $("#price_total").text(price_total - num);
            $.ajax
            ({
                url: "ajax.php", //接收頁
                type: "POST", //POST傳輸
                data: {type: "remove_cart", pid: pid}, // key/value
                dataType: "text", //回傳形態
                success: function (i) //成功就....
                {
                    if (i == 1) {
                        $("#tr" + pid).remove();
                        window.javatojs.showInfoFromJs('已移除購物車');
                    }
                },
                error: function ()//失敗就...
                {
                    //alert("ajax失敗");
                }
            });
        }
    }

    $("#to_pay").click(function () {
        var p_t = $("#price_total").text().trim();
        if (p_t <= 0) {
            if ($("#device").text() == 'mobile') {
                window.javatojs.showInfoFromJs('購物車內沒有商品');
            }
            else {
                alert('購物車內沒有商品');
            }
        }
        else {
            var pid_arr = Array();
            $("span[name='pid']").each(function (i) {
                pid_arr[i] = $(this).text().trim();
            });
            //pid_arr =>商品id陣列
            //price_ary =>單價陣列
            //qty_ary =>數量陣列
            $("#cart_pid").val(pid_arr);
            $("#cart_price").val(price_ary);
            $("#cart_qty").val(qty_ary);

            $("form#cart_form").submit();
        }
    });
</script>
