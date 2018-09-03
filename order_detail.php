<?php
/**
 * Edited by PhpStorm.
 * User: Xin-an
 * Date: 2018/9/1
 * Time: 下午 06:31
 */

use Base17Mai\Consumer;
use function Base17Mai\take;

$OrderNO = take('id');
$Consumer = new Consumer($member_id);
$OrderDetail = $Consumer->OrderDetail($OrderNO);
?>
<style>
    #pay_check_div {
        margin-top: 20%;
        margin-bottom: 10%;
    }

    @media (max-width: 768px) {
        #pay_check_div {
            margin-bottom: 15%;
            font-size: 3.8vw;
        }
    }
</style>
<div class="container" style="margin-top: 14%;">
    <div class="row">
        <table class="table table-bordered table-responsive table-condensed">
            <tr>
                <th colspan="4">
                    <h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">
                        訂單詳細&nbsp;&nbsp;&nbsp;<?= $OrderDetail['OrderNO'] ?></h3>
                </th>
            </tr>
            <tr>
                <td style="text-align: center; width: 20%;">購買日期</td>
                <td style="width: 30%;"><?= $OrderDetail['OrderTime'] ?></td>
                <td style="text-align: center; width: 20%;">總金額</td>
                <td style="width: 30%;"><?= $OrderDetail['Total']; ?></td>
            </tr>
            <tr>
                <td style="text-align: center;">截止日期</td>
                <td><?= $OrderDetail['OrderOver'] ?></td>
                <td style="text-align: center;">訂單狀態</td>
                <td><?= $OrderDetail['OrderStatus']; ?></td>
            </tr>
            <tr>
                <td colspan="4"><h4>收件人資訊</h4></td>
                <?php
                $Recipient = $OrderDetail['Recipient'];
                switch ($Recipient['DateType']) {
                    case 1:
                        $Recipient['DateType'] = '週一至週五';
                        break;
                    case 2:
                        $Recipient['DateType'] = '週六';
                        break;
                    case 3:
                        $Recipient['DateType'] = '不指定';
                        break;
                    default:
                        $Recipient['DateType'] = '沒填寫';
                        break;
                }
                ?>
            </tr>
            <tr>
                <td style="text-align: center;">收件人：</td>
                <td><span id="address_name"><?= $Recipient['Recipient'] ?></span></td>
                <td style="text-align: center;">收件電話：</td>
                <td><span id="addressee_cellphone"><?= $Recipient['CellPhone'] ?></span></td>
            </tr>
            <tr>
                <td style="text-align: center;">付款方式：</td>
                <td><span id="addressee_address"><?= $OrderDetail['PayType'] ?></span></td>
                <td style="text-align: center;">宅配日期：</td>
                <td><span id="addressee_date"><?= $Recipient['DateType'] ?></span></td>
            </tr>
            <tr>
                <td style="text-align: center;">收件地址：</td>
                <td colspan="3"><span id="addressee_address"><?= $Recipient['Address'] ?></span></td>
            </tr>
            <tr>
                <td colspan="4"><h4>訂單明細</h4></td>
            </tr>
            <tr>
                <td style="text-align: center;">商品名稱</td>
                <td style="text-align: center;">金額</td>
                <td style="text-align: center;">規格</td>
                <td style="text-align: center;">數量</td>
            </tr>
            <?php foreach ($OrderDetail['Detail'] as $product) : ?>
                <tr>
                    <td style="text-align: center;"><?= $product['PName'] ?></td>
                    <td style="text-align: right;"><?= $product['unitPrice'] ?></td>
                    <td style="text-align: center;"><?= $product['specification'] ?></td>
                    <td style="text-align: right;"><?= $product['Quantity'] ?></td>
                </tr>
            <?php endforeach; ?>
            <?php
            if ($OrderDetail['PayType'] === 'CVS') {
                ?>
                <tr>
                    <td style="text-align: center;">超商繳費代碼</td>
                    <td><?= $OrderDetail['PaymentInfo'] ?></td>
                </tr>
                <?php
            }
            ?>
            <tr align="right">
                <td colspan="4">
                    <div class="container">
                        <div class="row">
                            <input type="button" class="btn btn-default" value="返回" onclick="go_back();">&nbsp;&nbsp;
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<br><br>
<script>
    $("html,body").scrollTop(750);
    $("#aa-slider").remove();

    function go_back() {
        location.href = 'index.php?url=order_search';
    }

    $("#modify_btn").click(function () {
        var addressee_name = $("#addressee_name").text();
        var input_name = $('<input type="text" name="addressee_name" />');

        var addressee_cellphone = $("#addressee_cellphone").text();
        var input_cellphone = $('<input type="text" name="addressee_cellphone" />');

        var addressee_address = $("#addressee_address").text();
        var input_address = $('<input type="text" name="addressee_address" />');

        var addressee_date = $("#addressee_date").text();
        var addressee_val = $("#addressee_date").attr('val');
        var input_date = $('<label>宅配日期：</label><select name="addressee_date" val="' + addressee_val + '"><option value="1">週一至週五</option><option value="2">週六</option><option value="3">不指定</option></select>');

        var input_btn = $('<input type="button" class="btn btn-primary" value="儲存" onclick="save()">');
        $("#addressee_name").replaceWith(input_name.val(addressee_name));
        $("#addressee_cellphone").replaceWith(input_cellphone.val(addressee_cellphone));
        $("#addressee_address").replaceWith(input_address.val(addressee_address));
        $("#addressee_date").replaceWith(input_date.val(addressee_date));
        $(this).replaceWith(input_btn);

        $("select[name='addressee_date'] option").each(function () {
            if ($(this).val() == addressee_val) {
                $(this).attr('selected', true);
            }
        });
    });

    function save() {
        var addressee_name = $("input[name='addressee_name']").val();
        var addressee_cellphone = $("input[name='addressee_cellphone']").val();
        var addressee_address = $("input[name='addressee_address']").val();
        var addressee_date = $("select[name='addressee_date']").val();
        var order_no = $("input[name='order_no']").val();
        if (addressee_name != "" && addressee_cellphone != "" && addressee_address != "" && addressee_date != "") {
            $.ajax
            ({
                url: "ajax.php",
                type: "POST",
                data: {
                    type: "member_update_order",
                    order_no: order_no,
                    addressee_name: addressee_name,
                    addressee_cellphone: addressee_cellphone,
                    addressee_address: addressee_address,
                    addressee_date: addressee_date
                },
                dataType: "text",
                success: function (i) {
                    if (i == 1) {
                        alert('修改成功');
                        location.reload();
                    }
                },
                error: function () {
                }
            });
        }
    }
</script>