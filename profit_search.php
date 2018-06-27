<style>
    #pay_check_div
    {
        margin-top: 13%;
        margin-bottom: 15%;
    }

    @media max-width: 768px
    {
        #pay_check_div
        {
            margin-bottom: 30%;
        }
    }
</style>

<?php
if(strlen($member_id) > 10)
{
    $bonus_sql = "SELECT fb_name,profit FROM fb WHERE fb_id='$member_id'";
}
else
{
    $bonus_sql = "SELECT m_name,profit FROM member WHERE member_no='$member_id'";
}
$bonus_res = mysql_query($bonus_sql);
$bonus_row = mysql_fetch_array($bonus_res);
$sql = "SELECT * FROM `share` AS a JOIN consumer_order AS b JOIN consumer_order2 AS c ON a.order2_id = b.id AND a.order2_id = c.order1_id AND a.is_effective='1' WHERE a.manager_id='".$_SESSION['manager_no']."'";
//echo $sql;
$res = mysql_query($sql);

?>
<div class="container" id="pay_check_div">
    <div class="row">
        <table class="table table-bordered table-responsive table-condensed">
            <thead>
            <tr>
                <th colspan="5"><h4 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">分潤查詢<br>您目前累積分潤為：<?php if($bonus_row['profit']!=""){ echo $bonus_row['profit']; }else{echo 0;} ?>元</h4></th>
            </tr>
            <tr style="background: #DDDDDD;">
                <th>vip分享者</th>
                <th>購買者</th>
                <th>購買商品</th>
                <th>購買日期</th>
            </tr>
            </thead>
            <tbody>
            <?php

            while ($row = mysql_fetch_array($res))
            {
 
                if($row['manager_id'] == $row['member_id'])
                {
                    ?>
                    <tr>
                        <td><?php echo "-"; ?></td>
                        <td>
                            <?php
                            if(strlen($row['m_id']) > 10)
                            {
                                $sql3 = "SELECT fb_name FROM fb WHERE fb_id='".$row['m_id']."'";
                                $res3 = mysql_query($sql3);
                                $row3 = mysql_fetch_array($res3);
                                echo $row3['fb_name'] != "" ? $row3['fb_name'] : "-";
                            }
                            else
                            {
                                $sql3 = "SELECT m_name FROM member WHERE member_no='".$row['m_id']."'";
                                $res3 = mysql_query($sql3);
                                $row3 = mysql_fetch_array($res3);
                                echo $row3['m_name'] != "" ? $row3['m_name'] : "-";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $p_sql = "SELECT p_name,p_profit FROM product WHERE id='".$row['p_id']."'";
                            $p_res = mysql_query($p_sql);
                            $p_row = mysql_fetch_array($p_res);
                            echo $p_row['p_name'];
                            ?>
                        </td>
                        <td><?php echo $row['pay_time']; ?></td>
                    </tr>
                    <?php
                }
                else
                {
                    ?>
                    <tr>
                        <td>
                            <?php
                            if(strlen($row['vip_id']) > 10)
                            {
                                $sql2 = "SELECT fb_name FROM fb WHERE fb_id='".$row['vip_id']."'";
                                $res2 = mysql_query($sql2);
                                $row2 = mysql_fetch_array($res2);
                                echo $row2['fb_name'] != "" ? $row2['fb_name'] : "-";
                            }
                            else
                            {
                                $sql2 = "SELECT m_name FROM member WHERE member_no='".$row['vip_id']."'";
                                $res2 = mysql_query($sql2);
                                $row2 = mysql_fetch_array($res2);
                                echo $row2['m_name'] != "" ? $row2['m_name'] : "-";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if(strlen($row['member_id']) > 10)
                            {
                                $sql3 = "SELECT fb_name FROM fb WHERE fb_id='".$row['member_id']."'";
                                $res3 = mysql_query($sql3);
                                $row3 = mysql_fetch_array($res3);
                                echo $row3['fb_name'] != "" ? $row3['fb_name'] : "-";
                            }
                            else
                            {
                                $sql3 = "SELECT m_name FROM member WHERE member_no='".$row['member_id']."'";
                                $res3 = mysql_query($sql3);
                                $row3 = mysql_fetch_array($res3);
                                echo $row3['m_name'] != "" ? $row3['m_name'] : "-";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $p_sql = "SELECT p_name FROM product WHERE id='".$row['p_id']."'";
                            $p_res = mysql_query($p_sql);
                            $p_row = mysql_fetch_array($p_res);
                            echo $p_row['p_name'];
                            ?>
                        </td>
                        <td><?php echo $row['pay_time']; ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
            <tr align="right">
                <td colspan="5">
                    <div class="container">
                        <div class="row">
                            <input type="button" class="btn btn-default" value="返回" onclick="window.history.back(-1);">&nbsp;&nbsp;
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
    $("#aa-slider").remove();
</script>