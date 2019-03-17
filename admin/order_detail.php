<?php

use Base17Mai\Consumer, Base17Mai\Manager, Base17Mai\Member;
use function Base17Mai\take;

$Manager = new Manager();
$Member = new Member();
$Consumer = new Consumer();
$OrderID = take('id', '');
$OrderNO = $Consumer->GetOrderInformation('OrderNO', ['id' => $OrderID]);
if (!isset($OrderNO[0])) exit('嚴重錯誤，請回上一頁重試');
else $OrderNO = $OrderNO[0]['OrderNO'];
$OrderDetail = $Consumer->OrderDetailByID($OrderID);
$Customer = $Member->GetMemberInformation(['m_name', 'email', 'parent_no'], ['member_no' => $OrderDetail['member_no']]);
if (!isset($Customer[0])) exit('嚴重錯誤，請回上一頁重試');
$OrderDetail['Customer'] = array(
    'name' => $Customer[0]['m_name'],
    'email' => $Customer[0]['email']
);
$ParentNO = $Manager->GetManagerInformation('member_id', ['manager_no' => $Customer[0]['parent_no']]);
if (isset($ParentNO[0])) {
    $Parent = $Member->GetMemberInformation(['m_name', 'email'], ['member_no' => $ParentNO[0]['member_id']]);
    if (!isset($Parent[0])) exit('嚴重錯誤，請回上一頁重試');
    $OrderDetail['Parent'] = array(
        'name' => $Parent[0]['m_name'],
        'email' => $Parent[0]['email']
    );
} else {
    $OrderDetail['Parent'] = array(
        'name' => '未加入團購家族',
        'email' => '未加入團購家族'
    );
}
/*
@$id = $_GET['id'];
$sql = "SELECT * FROM consumer_order AS a JOIN consumer_order2 AS b ON a.id = b.order1_id WHERE a.id='$id'";
$res = mysql_query($sql);
$num = mysql_num_rows($res); //sql結果筆數
while ($row = mysql_fetch_array($res)) {
    $order_no_ary[] = $row['order_no'];
    $m_id[] = $row['m_id'];
    $p_name[] = $row['p_name'];
    $qty[] = $row['qty'];
    $p_web_price[] = $row['p_web_price'];
    $o_price[] = $row['o_price'];
    $pay_type[] = $row['pay_type'];
    $order_time[] = $row['order_time'];
    $order_type_id[] = $row['order_type_id'];
    $is_effective[] = $row['is_effective'];
}
*/
?>
<div class="widget">
    <h4 class="widgettitle">訂單詳細資料</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post">
            <p>
                <label>訂單編號</label>
                <span id="order_no" m_id="<?php echo @$m_id[0]; ?>" class="field" style="font-size: 18px;">
                    <?= $OrderDetail['OrderNO'] ?>
                </span>
            </p>
            <p>
                <label>購買人</label>
                <span class="field" style="font-size: 18px;">
                    帳號：<?= $OrderDetail['Customer']['email'] ?>
                    <br>
                    姓名：<?= $OrderDetail['Customer']['name'] ?>
                </span>
            </p>
            <p>
                <label>購買人的家長</label>
                <span class="field" style="font-size: 18px;">
                    帳號：<?= $OrderDetail['Parent']['email'] ?>
                    <br>
                    姓名：<?= $OrderDetail['Parent']['name'] ?>
                </span>
            </p>
            <p>
                <label>商品明細</label>
                <span class="field" style="font-size: 18px;">
                    <?php foreach ($OrderDetail['Detail'] as $item): ?>
                        <?= $item['PName'] ?>[<?= $item['specification'] ?>] <?= $item['unitPrice'] ?>元 &#215; <?= $item['Quantity'] ?>個
                        <br>
                        <br>
                    <?php endforeach; ?>
                </span>
            </p>
            <p>
                <label>總價</label>
                <span class="field" style="font-size: 18px;"><?= $OrderDetail['Total'] ?>元</span>
            </p>
            <p>
                <label>付款方式</label>
                <span class="field" style="font-size: 18px;"><?= $OrderDetail['PayType'] ?></span>
            </p>
            <p>
                <label>購買日期</label>
                <span class="field" style="font-size: 18px;"><?= $OrderDetail['OrderTime'] ?></span>
            </p>
            <p>
                <label>收件人資訊</label>
                <?php $Recipient = $OrderDetail['Recipient']; ?>
                <span class="field" style="font-size: 18px;">
                    收件人：<span id="addressee_name"><?= $Recipient['Recipient'] ?></span>
                    <br>
                    <br>
                    收件電話：<span id="addressee_cellphone"><?= $Recipient['CellPhone'] ?></span>
                    <br>
                    <br>
                    收件地址：<span id="addressee_address"><?= $Recipient['Address'] ?></span>
                    <br>
                    <br>
                    宅配日期：<span id='addressee_date'><?= $Recipient['DateType'] ?></span>
                </span>
            </p>
            <p>
                <label>付款狀態</label>
                <span class="field" style="font-size: 18px;">
                    <?= $OrderDetail['OrderStatus']; ?>
                </span>
            </p>
            <p class="stdformbutton" align="right">
                <input type="button" class="btn btn-default span1" value="返回" onclick="window.history.back(-1);">&nbsp;&nbsp;&nbsp;
                <!-- <input type="button" class="btn btn-primary span1" value="修改" id="modify_btn"> -->
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->
<script>
    $("#modify_btn").click(function () {
        var addressee_name = $("#addressee_name").text();
        var input_name = $('<input type="text" name="addressee_name" />');

        var addressee_cellphone = $("#addressee_cellphone").text();
        var input_cellphone = $('<input type="text" name="addressee_cellphone" />');

        var addressee_address = $("#addressee_address").text();
        var input_address = $('<input type="text" name="addressee_address" />');

        var addressee_date = $("#addressee_date").text();
        var addressee_val = $("#addressee_date").attr('val');
        var input_date = $('<span>宅配日期：</span><select name="addressee_date" val="' + addressee_val + '"><option value="1">週一至週五</option><option value="2">週六</option><option value="3">不指定</option></select>');

        var is_effective = $("#is_effective").text();
        var is_effective_val = $("#is_effective").attr('val');
        var is_effective_date = $('<select name="is_effective" class="field" val="' + is_effective_val + '"><option value="0">未付款</option><option value="1">備貨中</option><option value="2">已取消</option><option value="3">已出貨</option></select>');

        var input_btn = $('<input type="button" class="btn btn-primary span1" value="儲存" onclick="save()">');
        $("#addressee_name").replaceWith(input_name.val(addressee_name));
        $("#addressee_cellphone").replaceWith(input_cellphone.val(addressee_cellphone));
        $("#addressee_address").replaceWith(input_address.val(addressee_address));
        $("#addressee_date").replaceWith(input_date.val(addressee_date));
        $("#is_effective").replaceWith(is_effective_date.val(is_effective));
        $(this).replaceWith(input_btn);

        $("select[name='addressee_date'] option").each(function () {
            if ($(this).val() == addressee_val) {
                $(this).attr('selected', true);
            }
        });

        $("select[name='is_effective'] option").each(function () {
            if ($(this).val() == is_effective_val) {
                $(this).attr('selected', true);
            }
        });
    });

    function save() {
        var addressee_name = $("input[name='addressee_name']").val();//收件人姓名
        var addressee_cellphone = $("input[name='addressee_cellphone']").val(); //收件人電話
        var addressee_address = $("input[name='addressee_address']").val(); //收件人地址
        var addressee_date = $("select[name='addressee_date']").val(); //宅配日期
        var is_effective = $("select[name='is_effective']").val(); //付款狀態
        var order_no = $("#order_no").text().trim(); //訂單編號
        var order_m_id = $("#order_no").attr('m_id'); //購買人
        if (is_effective == 2) {
            confirm('是否取消這筆訂單');
            if (addressee_name != "" && addressee_cellphone != "" && addressee_address != "" && addressee_date != "") {
                $.ajax
                ({
                    url: "sever_ajax.php",
                    type: "POST",
                    data: {
                        type: "admin_update_order",
                        order_no: order_no,
                        order_m_id: order_m_id,
                        is_effective: is_effective,
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
        if (addressee_name != "" && addressee_cellphone != "" && addressee_address != "" && addressee_date != "" && is_effective != 2) {
            $.ajax
            ({
                url: "sever_ajax.php",
                type: "POST",
                data: {
                    type: "admin_update_order",
                    order_no: order_no,
                    order_m_id: order_m_id,
                    is_effective: is_effective,
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