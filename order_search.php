<style>
    #pay_check_div
    {
        margin-top: 18%;
        margin-bottom: 26%;
    }

    @media max-width: 768px)
    {
        #pay_check_div
        {
            margin-bottom: 15%;
            font-size: 3.8vw;
        }
    }
</style>
<div class="container">
    <div class="row">
        <table class="table table-bordered table-responsive table-condensed" id="pay_check_div">
            <thead>
            <tr>
                <th colspan="5"><h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">訂單查看</h3></th>
            </tr>
            <tr style="background: #DDDDDD;">
                <th>編號</th>
                <th>商品名稱</th>
                <th>購買金額</th>
                <th>付款狀態</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(isset($member_id))
            {
                $sql = "SELECT *,a.id AS aid FROM consumer_order AS a JOIN consumer_order2 AS b ON a.id = b.order1_id WHERE a.m_id='$member_id' GROUP BY order_no";
                //echo $sql;
                $res = mysql_query($sql);
                while ($row = mysql_fetch_array($res))
                {
                    if($row['order_over'] != "")
                    {
                        ?>
                        <tr id="order_list_<?php echo @$row['aid']; ?>">
                            <td><?php echo $row['order_no']; ?></td>
                            <td><?php echo $row['p_name']; ?></td>
                            <td><?php echo $row['o_price']; ?></td>
                            <td>
                                <?php
                                switch(@$row['is_effective'])
                                {
                                    case 0 || "":
                                        echo '未付款';
                                        break;
                                    case 1:
                                        echo '備貨中';
                                        break;
                                    case 2:
                                        echo '已取消';
                                        break;
                                    case 3:
                                        echo '已出貨';
                                        break;
                                }
                                ?>
                            </td>
                            <td width="180">
                                <input type="button" class="btn btn-primary" value="查看" onclick="location.href='index.php?url=order_detail&id=<?php echo $row['aid']; ?>'">
                                <?php
                                if($row['is_effective'] != 3 && $row['is_effective'] != 2)
                                {
                                    ?>
                                    <input type="button" class="btn btn-danger" value="取消訂單" name="cancel_btn" o_id="<?php echo $row['aid']; ?>">
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            <tr align="right">
                <td colspan="5">
                    <div class="container">
                        <div class="row">
                            <input type="button" class="btn btn-default" value="返回" onclick="go_back();">&nbsp;&nbsp;
<!--                            <input type="button" class="btn btn-primary" value="兌換" id="pay_btn">-->
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    $("html,body").scrollTop(750);
    $("#aa-slider").hide();

    function go_back()
    {
        location.href='index.php?url=member_center';
    }

    $("input[name='cancel_btn']").click(function ()
    {
        var o_id = $(this).attr('o_id');
        if(confirm('是否取消這筆訂單'))
        {
            $.ajax
            ({
                url:"ajax.php", //接收頁
                type:"POST", //POST傳輸
                data:{type:"clean_order", o_id:o_id}, // key/value
                dataType:"text", //回傳形態
                success:function(i) //成功就....
                {
                    if(i == 1)
                    {
                       alert('取消成功!');
                        location.href='index.php?url=order_search';
                    }
                    else
                    {
                        alert('意外的錯誤，請檢查網路環境後再次操作');
                    }
                },
                error:function()//失敗就...
                {
                }
            });
        }
    });
</script>