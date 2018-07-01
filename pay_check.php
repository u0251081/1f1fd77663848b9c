<style>
    #pay_check_div
    {
        margin-top: 13%;
        margin-bottom: 3%;
    }

    @media (max-width: 768px)
    {
        #pay_check_div
        {
            margin-bottom: 30%;
        }
    }
</style>
<form action="testpay.php" method="post" id="send_oder">
    <div class="container" id="pay_check_div">
        <div class="row">
            <table class="table table-bordered table-responsive table-condensed">
                <thead>
                <tr>
                    <th colspan="5"><h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">再次確認購買!</h3></th>
                </tr>
                <tr style="background: #DDDDDD;">
                    <th>商品名稱</th>
                    <th>價格</th>
                    <th>數量</th>
                    <th colspan="4">小計</th>
                </tr>
                </thead>
                <tbody>
<?php
if(strlen(@$member_id) > 10)
{
    $check_is_first_login = "SELECT * FROM fb WHERE fb_id='$member_id'";
    $check_is_first_res = mysql_query($check_is_first_login);
    $check_is_first_row = mysql_fetch_array($check_is_first_res);
    if($check_is_first_row['id_card'] == '' || $check_is_first_row['address'] == '' || $check_is_first_row['cellphone'] == '')
    {
        ?>
        <script>
            if($("#device").text() == 'mobile')
            {
                window.javatojs.showInfoFromJs('請先完成個人資料');
            }
            else
            {
                alert('請先完成個人資料');
            }
            location.href='index.php?url=member_info';
        </script>
        <?php
    }
}
else
{
    @$check_is_first_login = "SELECT * FROM member WHERE member_no='$member_id'";
    $check_is_first_res = mysql_query($check_is_first_login);
    @$check_is_first_row = mysql_fetch_array($check_is_first_res);
    if($check_is_first_row['id_card'] == '' || $check_is_first_row['address'] == '' || $check_is_first_row['cellphone'] == '')
    {
        ?>
        <script>
            if($("#device").text() == 'mobile')
            {
                window.javatojs.showInfoFromJs('請先完成個人資料');
            }
            else
            {
                alert('請先完成個人資料');
            }
            location.href='index.php?url=member_info';
        </script>
        <?php
    }
}


@$cart_pid = $_POST['cart_pid']; //pid_arr =>多筆商品的商品id陣列
@$cart_price = $_POST['cart_price']; //price_ary =>多筆商品的單價陣列
@$cart_qty = $_POST['cart_qty']; //qty_ary =>多筆商品的數量陣列
@$cart_qty_ary = explode(',',$_POST['cart_qty']); //qty_ary =>多筆商品的數量陣列
$p_name_ary = ""; //多筆商品的名稱
$i = 0;

$p_name = ""; //單獨一個商品的名稱
$pay_pirce = ""; //單筆商品的價格
@$p_id = $_POST['p_id']; //商品id
@$pay_qty = $_POST['pay_qty']; //買家購買數量
@$fb_no = $_POST['fb_no'];
@$total = ""; //合計計算單筆或多筆共用此變數

@$manager_id = $_POST['manager_id']; //代表收到行銷經理分享=>綁定SESSION的分享的行銷經理id
@$vip_id = $_POST['vip_id']; //代表收到VIP會員分享=>綁定SESSION的分享的VIPid

if($p_id != "")
{
    $sql = "SELECT * FROM product AS a JOIN(SELECT p_id,web_price,sell_id FROM price) AS b ON a.id = b.p_id AND b.sell_id='1' WHERE a.id='$p_id'";
    $res = mysql_query($sql);
}
else
{
    $sql = "SELECT * FROM product AS a JOIN(SELECT p_id,web_price,sell_id FROM price) AS b ON a.id = b.p_id AND b.sell_id='1' WHERE a.id IN($cart_pid) ORDER BY find_in_set(a.id ,'{$cart_pid}')";
    $res = mysql_query($sql);
}

while(@$row = mysql_fetch_array($res))
{
    if($row['added'] == 1)
    {
        if($p_id != "")
        {
            $p_name = $row['p_name'];
            $pay_pirce = $row['web_price'];
            @$total = $row['web_price']*$pay_qty;
            ?>
                <tr>
                    <td><?php echo $row['p_name']; ?></td>
                    <td><?php echo $row['web_price']; ?></td>
                    <td><?php echo $pay_qty; ?></td>
                    <td colspan="4"><?php echo $row['web_price']*$pay_qty; ?></td>
                </tr>
            <?php
        }
        else
        {
            $p_name_ary[] = $row['p_name'];
            @$total += $row['web_price']*$cart_qty_ary[$i];
            ?>
                <tr>
                    <td><?php echo $row['p_name']; ?></td>
                    <td><?php echo $row['web_price']; ?></td>
                    <td><?php echo $cart_qty_ary[$i]; ?></td>
                    <td colspan="4"><?php echo $row['web_price']*$cart_qty_ary[$i]; ?></td>
                </tr>
            <?php
            $i++;
        }
    }
    else
    {
        ?>
        <script>
            if($("#device").text() == 'mobile')
            {
                window.javatojs.showInfoFromJs('此交易含有已下架商品，請重新確認後再次交易!');
            }
            else
            {
                alert('此交易含有已下架商品，請重新確認後再次交易!');
            }
            window.history.back(-1);
        </script>
        <?php
    }
}
if (is_array($p_name_ary)) {
    $p_name_ary = implode(',',$p_name_ary);
} else {
}

if(@$total <30)
{
    ?>
    <tr>
        <td colspan="4" align="right" style="color:red;">*訂單總額必須超過30元，才可以結帳</td>
    </tr>
    <?php
}
else
{
    ?>
    <tr>
        <td colspan="8" align="right">合計：<?php echo $total.'元'; ?></td>
    </tr>
    <?php
}
?>

<tr>
    <th colspan="5"><h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">收件人資料</h3></th>
</tr>
<tr style="background: #DDDDDD;">
    <th>姓名</th>
    <th>電話</th>
    <th>地址</th>
    <th >宅配日期</th>
    <th>付款方式</th>
</tr>
<tr>
    <td><input type="text" name="addressee_name" class="form-control"></td>
    <td><input type="text" name="cellphone" class="form-control"></td>
    <td><input type="text" name="address" class="form-control"></td>
    <td >
        <select name="addressee_date" class="form-control">
            <option value="1">周一至周五</option>
            <option value="2">周六</option>
            <option value="3">不指定</option>
        </select>
    </td>
    <td>
        <select name="paymentchose" class="form-control">
            <option value="Credit">信用卡</option>
            <option value="CVS">超商代碼繳費</option>
        </select>
    </td>
</tr>

<tr align="right">
    <td colspan="5">
        <div class="container">
            <div class="row">
                <?php
                if(@$total <30)
                {
                    ?>
                    <input type="button" class="btn btn-default" value="返回" onclick="window.history.back(-1);">&nbsp;&nbsp;
                    <?php
                }
                else
                {
                    ?>
                    <input type="checkbox" id="for_member_info"><label for="for_member_info">同會員資料</label>&nbsp;&nbsp;
                    <input type="button" class="btn btn-default" value="返回" onclick="window.history.back(-1);">&nbsp;&nbsp;
                    <input type="button" class="btn btn-primary" value="結帳" id="pay_btn">
                    <?php
                }
                ?>
            </div>
        </div>
    </td>
</tr>
                </tbody>

            </table>
        </div>
    </div>

    <input type="hidden" name="fb_no" value="<?php echo $fb_no; ?>">
    <input type="hidden" name="manager_id" value="<?php echo @$manager_id; ?>">
    <input type="hidden" name="vip_id" value="<?php echo @$vip_id; ?>">
    <!--
    <input type="hidden" name="p_id" value="<?php echo $p_id; ?>">
    <input type="hidden" name="p_name" value="<?php echo $p_name; ?>">
    <input type="hidden" name="web_price" value="<?php echo $pay_pirce; ?>">
    <input type="hidden" name="pay_qty" value="<?php echo $pay_qty; ?>">
    -->
    <?php
    if (!empty($p_id)) {
        ?>
        <input type="hidden" name="cart_pid" value="<?php echo $p_id; ?>">
        <input type="hidden" name="p_name_ary" value="<?php echo $p_name; ?>">
        <input type="hidden" name="cart_price" value="<?php echo $pay_pirce; ?>">
        <input type="hidden" name="cart_qty" value="<?php echo $pay_qty; ?>">
        <?php
    }
    if (!empty($cart_pid)) {
        ?>
        <input type="hidden" name="cart_pid" value="<?php echo $cart_pid; ?>">
        <input type="hidden" name="p_name_ary" value="<?php echo $p_name_ary; ?>">
        <input type="hidden" name="cart_price" value="<?php echo $cart_price; ?>">
        <input type="hidden" name="cart_qty" value="<?php echo $cart_qty; ?>">
        <?php
    }
    ?>
    <input type="hidden" name="TotalAmount" value="<?php echo $total; ?>">
</form>
<script>

    $(function () {
        $("#aa-slider").hide();
    });

    $("#pay_btn").click(function()
    {
        var addressee_name = $("input[name='addressee_name']").val();
        var cellphone = $("input[name='cellphone']").val();
        var address = $("input[name='address']").val();
        var addressee_date = $("input[name='addressee_date']").val();
        var mobile_num_reg = /^[09]{2}[0-9]{8}$/;
        if(addressee_name != '' && cellphone != "" && addressee_date != "" && address!="")
        {
            if(!mobile_num_reg.test(cellphone))
            {
                alert('手機號碼格式錯誤');
            }
            else
            {
                $("form#send_oder").submit( /*test_submit()*/ );
            }
        }
        else
        {
            alert('請確認收件人資料是否填寫完整');
        }
    });
    function test_submit() {
        var e = $("form#send_oder");
        var url = e.attr('action');
        var method = e.attr('method');
        var formdata = e.serialize();
        $.ajax({
            url: url,
            method: method,
            data: formdata,
            success: function(msg) {
                console.log(msg);
            }
        });
        return false;
    }
    $("#for_member_info").click(function()
    {
        var m_id = $("#m_id").text();
        if($(this).is(':checked'))
        {
            if(m_id)
            {
                $.ajax
                ({
                    url:"ajax.php",
                    type:"POST",
                    data:{type:"search_member_info", m_id:m_id},
                    dataType:"json",
                    success:function(i)
                    {
                        $.each(i,function(key, item)
                        {
                            $("input[name='addressee_name']").val(item['m_name']);
                            $("input[name='cellphone']").val(item['cellphone']);
                            $("input[name='address']").val(item['address']);
                        });
                    },
                    error:function()
                    {
                        console.log('資料有誤');
                    }
                });
            }
        }
        else
        {
            $("input[name='addressee_name']").val('');
            $("input[name='cellphone']").val('');
            $("input[name='address']").val('');
        }
    });
</script>