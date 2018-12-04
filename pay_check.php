<?php

use Base17Mai\Consumer;

$share_data = array(
    'fb_no' => isset($_POST['fb_no']) ? $_POST['fb_no'] : '',
    'manager_id' => isset($_POST['manager_id']) ? $_POST['manager_id'] : '', //代表收到行銷經理分享=>綁定SESSION的分享的行銷經理id
    'vip_id' => isset($_POST['vip_id']) ? $_POST['vip_id'] : ''
);

function SetAmount(&$product)
{
    if (is_array($product)) {
        foreach ($product as $key => $value) {
            $product[$key]['Amount'] = (int)$value['Quantity'] * $value['unitPrice'];
        }
    }
}

function is_first_login($member_id = FALSE)
{
    if ($member_id !== FALSE) {
        $sql = '';
        if (strlen($member_id) > 10) {
            $sql .= "select * from fb where fb_id = '{$member_id}';";
        } else {
            $sql .= "select * from member where member_no = '{$member_id}';";
        }
        $profile_arr = get_sql_data($sql);
        if (empty($profile_arr)) die('<script>alert("something is going wrong!!");location.href="index.php";</script>');
        $profile = $profile_arr[0];
        if ($profile['id_card'] === '' || $profile['address'] === '' || $profile['cellphone'] === '') {
            $javascript = '<script>';
            $javascript .= 'if ($("#device").text() === "mobile") { window.javatojs.showInfoFromJs("請先完成個人資料");}';
            $javascript .= 'else { alert("請先完成個人資料");}';
            $javascript .= 'location.href = "index.php?url=member_info";';
            $javascript .= '</script>';
            print $javascript;
        }
    } else {
        die('<script>alert("something is going wrong!!");location.href="index.php";</script>');
    }
}

function prepare_purchasing_data($product)
{
    $product_data = array();
    if (isset($_POST['cart_pid'])) {
        $pid_arr = explode(',', $_POST['cart_pid']); //pid_arr =>多筆商品的商品id陣列
        $price_arr = explode(',', $_POST['cart_price']); //price_ary =>多筆商品的單價陣列
        $qty_arr = explode(',', $_POST['cart_qty']); //qty_ary =>多筆商品的數量陣列
        $cid = count($pid_arr);
        $cpc = count($price_arr);
        $cqt = count($qty_arr);
        if ($cid !== $cpc || $cpc !== $cqt) {
            die('critical error');
        } else {
            foreach ($pid_arr as $k => $v) {
                $tmp_arr = get_product_info($pid_arr[$k]);
                $tmp_arr['qty'] = $qty_arr[$k];
                $tmp_arr['amount'] = (int)$tmp_arr['qty'] * (int)$tmp_arr['price'];
                $product_data[] = $tmp_arr;
            }
        }
    }
    if (isset($_POST['p_id'])) {
        $tmp = get_product_info($_POST['p_id']);
        $tmp['qty'] = $_POST['pay_qty'];
        $tmp['amount'] = (int)$tmp['qty'] * (int)$tmp['price'];
        $product_data[] = $tmp;
    }
    return $product_data;
}

function get_product_info($product_id = FALSE)
{
    $product = array();
    if ($product_id !== FALSE) {
        $sql = "select added, p_name from product where id = '{$product_id}'";
        $product_arr = get_sql_data($sql);
        $added = $product_arr[0]['added'] or die('critical error');
        if ($added !== '1') product_expired();
        $product_name = $product_arr[0]['p_name'] or die('critical error');
        $sql = "select web_price from price where p_id = '{$product_id}';";
        $product_price_arr = get_sql_data($sql);
        $product_price = $product_price_arr[0]['web_price'] or die('critical error');
        $product = array(
            'pid' => $product_id,
            'p_name' => $product_name,
            'price' => $product_price
        );
    }
    return $product;
}

function product_expired()
{
    ?>
    <script>
        if ($("#device").text() == 'mobile') {
            window.javatojs.showInfoFromJs('此交易含有已下架商品，請重新確認後再次交易!');
        }
        else {
            alert('此交易含有已下架商品，請重新確認後再次交易!');
        }
        window.history.back(-1);
    </script>
    <?php
    exit();
}

function price_amount($product = array())
{
    $total = 0;
    foreach ($product as $v) {
        $total += (int)$v['Amount'];
    }
    return $total;
}

function produce_tbody(array $product)
{
    $tbody = '';
    $total = price_amount($product);
    foreach ($product as $k => $v) {
        $tbody .= '<tr>';
        $tbody .= "<td align='center'>{$v['PName']}</td>";
        $tbody .= "<td align='right'>{$v['unitPrice']}</td>";
        $tbody .= "<td align='center'>{$v['specification']}</td>";
        $tbody .= "<td align='right'>{$v['Quantity']}</td>";
        $tbody .= "<td align='right' colspan='2'>{$v['Amount']}</td>";
        $tbody .= '</tr>';
    }
    if ($total < 30) {
        $tbody .= '<tr>';
        $tbody .= '<td colspan="5" align="right" style="color:red;">';
        $tbody .= '*訂單總額必須超過30元，才可以結帳';
        $tbody .= '</td>';
        $tbody .= '</tr>';
    } else {
        $tbody .= '<tr>';
        $tbody .= '<td colspan="5" align="right" style="color:blue;">';
        $tbody .= '合計：' . $total . '元';
        $tbody .= '</td>';
        $tbody .= '</tr>';
    }
    return $tbody;
}

function bottom_btn($total = 0)
{
    $html = '';
    if ($total < 30) {
        $html .= '<input type="button" class="btn btn-default" value="返回" onclick="window.history.back(-1);">&nbsp;&nbsp;';
    } else {
        $html .= '<label for="for_member_info"><input type="checkbox" id="for_member_info">同會員資料</label>&nbsp;&nbsp;';
        $html .= '<input type="button" class="btn btn-default" value="返回" onclick="window.history.back(-1);">&nbsp;&nbsp;';
        $html .= '<input type="button" class="btn btn-primary" value="結帳" id="pay_btn">';
    }
    return $html;
}

function checkExpired($products = [])
{
    if (!is_array($products)) return false;
    foreach ($products as $key => $value) :
        if ($value['Prelease'] !== '1') product_expired();
    endforeach;
}

is_first_login($member_id);
$Consumer = new Consumer();
$product = $Consumer->ListProductInCart();
checkExpired($product);
SetAmount($product);
$total = price_amount($product);
$tbody = produce_tbody($product);
$bottom_button = bottom_btn($total);
?>
<style>
    #pay_check_div {
        margin-top: 13%;
        margin-bottom: 3%;
    }

    @media (max-width: 768px) {
        #pay_check_div {
            margin-bottom: 30%;
        }
    }
</style>
<form action="ECPay.php" method="post" id="send_order">
    <div class="container" id="pay_check_div">
        <div class="row">
            <table class="table table-bordered table-responsive table-condensed">
                <thead>
                <tr>
                    <th colspan="5"><h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">再次確認購買!</h3>
                    </th>
                </tr>
                <tr style="background: #DDDDDD;">
                    <th style="text-align: center;">商品名稱</th>
                    <th style="text-align: center;">價格</th>
                    <th style="text-align: center;">規格</th>
                    <th style="text-align: center;">數量</th>
                    <th colspan="4" style="text-align: center;">小計</th>
                </tr>
                </thead>
                <tbody>
                <?= $tbody ?>
                <tr>
                    <th colspan="5"><h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">收件人資料</h3></th>
                </tr>
                <tr style="background: #DDDDDD;">
                    <th>姓名</th>
                    <th>電話</th>
                    <th>地址</th>
                    <th>宅配日期</th>
                    <th>付款方式</th>
                </tr>
                <tr>
                    <td><input type="text" name="Recipient" class="form-control"></td>
                    <td><input type="text" name="CellPhone" class="form-control"></td>
                    <td><input type="text" name="Address" class="form-control"></td>
                    <td>
                        <select name="DateType" class="form-control">
                            <option value="1">周一至周五</option>
                            <option value="2">周六</option>
                            <option value="3">不指定</option>
                        </select>
                    </td>
                    <td>
                        <select name="paymentChose" class="form-control">
                            <option value="Credit">信用卡</option>
                            <option value="CVS">超商代碼繳費</option>
                        </select>
                    </td>
                </tr>
                <tr align="right">
                    <td colspan="5">
                        <div class="container">
                            <div class="row">
                                <?= $bottom_button ?>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>
<script>

    $(function () {
        $("#aa-slider").hide();
    });

    $("#pay_btn").click(function () {
        if (checkInput()) $("form#send_order").submit(/*test_submit()*/);
    });

    function checkInput() {
        let addressee_name = $("input[name='Recipient']").val();
        let cellphone = $("input[name='CellPhone']").val();
        let address = $("input[name='Address']").val();
        let addressee_date = $("input[name='DateType']").val();
        let mobile_num_reg = /^09[0-9]{8}$/;
        if (addressee_name !== '' && cellphone !== "" && addressee_date !== "" && address !== "") {
            if (!mobile_num_reg.test(cellphone)) {
                alert('手機號碼格式錯誤');
                return false;
            } else {
                return true;
            }
        }
        else {
            alert('請確認收件人資料是否填寫完整');
            return false;
        }
    }

    function test_submit() {
        var e = $("form#send_order");
        var url = e.attr('action');
        var method = e.attr('method');
        var formdata = e.serialize();
        $.ajax({
            url: url,
            method: method,
            data: formdata,
            success: function (msg) {
                console.log(msg);
            }
        });
        return false;
    }

    $("#for_member_info").click(function () {
        let Recipient = $("input[name='Recipient']");
        let CellPhone = $("input[name='CellPhone']");
        let Address = $("input[name='Address']");
        var m_id = $("#m_id").text();
        if ($(this).is(':checked')) {
            if (m_id) {
                $.ajax
                ({
                    url: "ajax.php",
                    type: "POST",
                    data: {type: "search_member_info", m_id: m_id},
                    dataType: "json",
                    success: function (i) {
                        $.each(i, function (key, item) {
                            Recipient.val(item['m_name']);
                            CellPhone.val(item['cellphone']);
                            Address.val(item['address']);
                        });
                    },
                    error: function () {
                        console.log('資料有誤');
                    }
                });
            }
        }
        else {
            Recipient.val('');
            CellPhone.val('');
            Address.val('');
        }
    });
</script>