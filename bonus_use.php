<style>
    #pay_check_div
    {
        margin-top: 13%;
        margin-bottom: 15%;
    }
</style>

<?php
if(strlen($member_id) > 10)
{
    $bonus_sql = "SELECT * FROM fb WHERE fb_id='$member_id'";
}
else
{
    $bonus_sql = "SELECT * FROM member WHERE member_no='$member_id'";
}
$bonus_res = mysql_query($bonus_sql);
$bonus_row = mysql_fetch_array($bonus_res);

$sql = "SELECT * FROM convert_info WHERE people='$member_id'";
$res = mysql_query($sql);
?>
<div class="container" id="pay_check_div">
    <div class="row">
        <table class="table table-bordered table-responsive table-condensed">
            <thead>
            <tr>
                <th colspan="4">
                    <h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">兌換紀錄</h3>
                </th>
            </tr>
            <tr style="background: #DDDDDD;">
                <th>商品名稱</th>
                <th width='50'>點數</th>
                <th>兌換時間</th>
                <th>狀態</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while($row = mysql_fetch_array($res))
            {
                ?>
                <tr>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['bonus_price']; ?></td>
                    <td><?php echo $row['convert_date']; ?></td>
                    <td>
                        <?php
                        if($row['is_use'] == 0)
                        {
                            ?>
                            <input type="button" class="btn btn-primary" value="使用" onclick="creat_qr(<?php echo $row['bonus_pid'].",".$row['id'];?>)">
                            <?php
                        }
                        else
                        {
                            ?>
                            <input type="button" class="btn btn-default" value="已使用" disabled>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
            <tr align="right">
                <td colspan="5">
                    <div class="container">
                        <div class="row">
                            <input type="button" class="btn btn-default" value="返回" onclick="location.href='index.php?url=bonus_search'">&nbsp;&nbsp;
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
    $("#aa-slider").hide();

    function creat_qr(bid,id)
    {
        var b_id = bid;
        var id = id;
        location.href='index.php?url=show_qr2&bid='+b_id+'&id='+id;
    }

    $(function()
    {
		if($(window).width() < 768)
        {
			var pay_check_div = $("#pay_check_div").height();
            var my_nav = $(".my_nav").height();
            if(my_nav < pay_check_div)
            {
                $("#pay_check_div").css('height',pay_check_div+my_nav+30+'px');
            }
        }
    })
</script>