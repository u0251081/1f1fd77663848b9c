<style>
    #pay_check_div
    {
        margin-top: 20%;
        margin-bottom: 10%;
    }

    @media (max-width: 768px)
    {
        #pay_check_div {
            margin-bottom: 15%;
            font-size: 3.8vw;
        }
    }
</style>
<?php
@$id = $_GET['id'];
if(isset($member_id) && isset($id))
{
    $sql = "SELECT *,a.id AS aid FROM consumer_order AS a JOIN consumer_order2 AS b ON a.id = b.order1_id WHERE a.m_id='$member_id' AND a.id='$id'";
    //echo $sql;
    $res = mysql_query($sql);
    $num = mysql_num_rows($res);
}
$order_type_id = '';
$order_is_effective = '';
$order_m_id = '';
?>
<div class="container" style="margin-top: 14%;">
    <div class="row">
            <?php
            $i = 1;
            while ($row = mysql_fetch_array($res))
            {
                $order_is_effective = $row['is_effective'];
                $order_m_id = $row['m_id'];
                ?>
        <table class="table table-bordered table-responsive table-condensed">
            <tr>
                <th colspan="2">
                    <h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">訂單詳細<?php echo $i; ?></h3>
                    <input type="hidden" name="order_no" value="<?php echo $row['order_no']; ?>">
                </th>
            </tr>
                <tr>
                    <td>商品名稱</td>
                    <td><?php echo $row['p_name']; ?></td>
                </tr>
                <tr>
                    <?php
                    if($num > 1)
                    {
                        ?>
                        <td>金額</td>
                        <td><?php echo $row['p_web_price']."元"; ?></td>
                        <?php
                    }
                    else
                    {
                        ?>
                        <td>總金額</td>
                        <td><?php echo $row['o_price']."元"; ?></td>
                        <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td>購買日期</td>
                    <td><?php echo $row['pay_time']; ?></td>
                </tr>
                <tr>
                    <td>數量</td>
                    <td><?php echo $row['qty']; ?></td>
                </tr>
                <tr>
                    <td>收件人資訊</td>
                    <td>
                        <?php
                        $addressee_set = "SELECT * FROM addressee_set WHERE order_no='".$row['order_no']."'";
                        $addressee_res = mysql_query($addressee_set);
                        $addressee_row = mysql_fetch_array($addressee_res);
                        echo '收件人：<span id="addressee_name">'.@$addressee_row['name']."</span><br>";
                        echo '收件電話：<span id="addressee_cellphone">'.@$addressee_row['cellphone']."</span><br>";
                        echo '收件地址：<span id="addressee_address">'.@$addressee_row['address']."</span><br>";
                        echo "<span id='addressee_date' val='".$addressee_row["addressee_date"]."'>";
                        switch ($addressee_row['addressee_date'])
                        {
                            case 1:
                                echo '宅配日期：週一至週五';
                                break;
                            case 2:
                                echo '宅配日期：週六';
                                break;
                            case 3:
                                echo '宅配日期：不指定';
                                break;
                            default:
                                echo '宅配日期：沒填寫';
                                break;
                        }
                        echo '</span>';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>付款日期</td>
                    <td><?php echo $row['order_over']." 截止"; ?></td>
                </tr>
                 <?php
                if($row['pay_type']!='Credit')
                {
                ?>
                <tr>
                    <td>超商繳費代碼</td>
                    <td><?php echo $row['tradeno']; ?></td>
                </tr>
                 <?php
                }
                ?>
                <?php
                $order_type_id = $row['order_type_id'];
                $i++;
            }
            ?>
            <tr align="right">
                <td colspan="3">
                    <div class="container">
                        <div class="row">
                            <input type="button" class="btn btn-default" value="返回" onclick="go_back();">&nbsp;&nbsp;
                            <?php
                            if($order_is_effective != 3 && $order_is_effective != 2)
                            {
                                ?>
                                <input type="button" class="btn btn-primary" value="修改" id="modify_btn">
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
<?php
if($order_m_id != $member_id)
{
    ?>
    <script>
        alert('此訂單已取消');
        location.href='index.php?url=order_search';
    </script>
    <?php
}
?>

<br><br>
<script>
    $("html,body").scrollTop(750);
    $("#aa-slider").remove();

    function go_back()
    {
        location.href='index.php?url=order_search';
    }

    $("#modify_btn").click(function ()
    {
        var addressee_name = $("#addressee_name").text();
        var input_name = $('<input type="text" name="addressee_name" />');

        var addressee_cellphone = $("#addressee_cellphone").text();
        var input_cellphone = $('<input type="text" name="addressee_cellphone" />');

        var addressee_address = $("#addressee_address").text();
        var input_address = $('<input type="text" name="addressee_address" />');

        var addressee_date = $("#addressee_date").text();
        var addressee_val = $("#addressee_date").attr('val');
        var input_date = $('<label>宅配日期：</label><select name="addressee_date" val="'+addressee_val+'"><option value="1">週一至週五</option><option value="2">週六</option><option value="3">不指定</option></select>');

        var input_btn = $('<input type="button" class="btn btn-primary" value="儲存" onclick="save()">');
        $("#addressee_name").replaceWith(input_name.val(addressee_name));
        $("#addressee_cellphone").replaceWith(input_cellphone.val(addressee_cellphone));
        $("#addressee_address").replaceWith(input_address.val(addressee_address));
        $("#addressee_date").replaceWith(input_date.val(addressee_date));
        $(this).replaceWith(input_btn);

        $("select[name='addressee_date'] option").each(function()
        {
            if($(this).val() == addressee_val)
            {
                $(this).attr('selected',true);
            }
        });
    });

    function save()
    {
        var addressee_name = $("input[name='addressee_name']").val();
        var addressee_cellphone = $("input[name='addressee_cellphone']").val();
        var addressee_address = $("input[name='addressee_address']").val();
        var addressee_date = $("select[name='addressee_date']").val();
        var order_no = $("input[name='order_no']").val();
        if(addressee_name != "" && addressee_cellphone != "" && addressee_address != "" && addressee_date != "")
        {
            $.ajax
            ({
                url:"ajax.php",
                type:"POST",
                data:{type:"member_update_order", order_no:order_no, addressee_name:addressee_name, addressee_cellphone:addressee_cellphone, addressee_address:addressee_address, addressee_date:addressee_date},
                dataType:"text",
                success:function(i)
                {
                    if(i == 1)
                    {
                        alert('修改成功');
                        location.reload();
                    }
                },
                error:function()
                {
                }
            });
        }
    }
</script>