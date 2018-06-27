<?php
@$id = $_GET['id'];
@$arg = $_GET['arg'];
if(isset($arg) && $arg == 'n')
{
    $sql = "SELECT *,a.id AS aid FROM member AS a JOIN seller_manager AS b ON a.member_no = b.member_id WHERE a.id='".$_GET['id']."'";
    $res = mysql_query($sql);
    @$row123 = mysql_fetch_array($res);

    $bonus_sql = "SELECT m_name,profit FROM member WHERE member_no='".$row123['member_id']."'";

}
else if(isset($arg) && $arg == 'fb')
{
    $sql = "SELECT *,a.id AS aid FROM fb AS a JOIN seller_manager AS b ON a.fb_id = b.member_id WHERE a.id='".$_GET['id']."'";
    $res = mysql_query($sql);
    @$row123 = mysql_fetch_array($res);

    $bonus_sql = "SELECT fb_name,profit FROM fb WHERE fb_id='".$row123['fb_id']."'";

}

$bonus_res = mysql_query($bonus_sql);
$bonus_row = mysql_fetch_array($bonus_res);
$sql2 = "SELECT * FROM `share` AS a JOIN consumer_order AS b JOIN consumer_order2 AS c ON a.order2_id = b.id AND a.order2_id = c.order1_id AND a.is_effective='1' WHERE a.manager_id='".$row123['manager_no']."'";

$res2 = mysql_query($sql2);

?>
<div class="widget">
    <h4 class="widgettitle">行銷經理分享查看</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post">
            <p>
                <label>經理姓名</label>
                <span class="field" style="font-size: large;">
                    <?php
                    if($arg == 'fb')
                    {
                        echo $row123['fb_name'];
                    }
                    else
                    {
                        echo $row123['m_name'];
                    }
                    ?>
                </span>
            </p>
            <p>
                <label>經理代號</label>
                <span class="field" style="font-size: large;"><?php echo $row123['manager_no']; ?></span>
            </p>            
                   <table class="table table-bordered table-responsive table-condensed">
            <thead>
            <tr style="background: #DDDDDD;">
                <th>vip分享者</th>
                <th>購買者</th>
                <th>購買商品</th>
                <th>購買日期</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($row_share = mysql_fetch_array($res2))
            {
                if($row_share['manager_id'] == $row_share['member_id'])
                {
                    ?>
                    <tr>
                        <td><?php echo "-"; ?></td>
                        <td>
                            <?php
                            if(strlen($row_share['m_id']) > 10)
                            {
                                $sql3 = "SELECT fb_name FROM fb WHERE fb_id='".$row_share['m_id']."'";
                                $res3 = mysql_query($sql3);
                                $row3 = mysql_fetch_array($res3);
                                echo $row3['fb_name'] != "" ? $row3['fb_name'] : "-";
                            }
                            else
                            {
                                $sql3 = "SELECT m_name FROM member WHERE member_no='".$row_share['m_id']."'";
                                $res3 = mysql_query($sql3);
                                $row3 = mysql_fetch_array($res3);
                                echo $row3['m_name'] != "" ? $row3['m_name'] : "-";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $p_sql = "SELECT p_name,p_profit FROM product WHERE id='".$row_share['p_id']."'";
                            $p_res = mysql_query($p_sql);
                            $p_row = mysql_fetch_array($p_res);
                            echo $p_row['p_name'];
                            ?>
                        </td>
                        <td><?php echo $row_share['pay_time']; ?></td>
                    </tr>
                    <?php
                }
                else
                {
                    ?>
                    <tr>
                        <td>
                            <?php
                            if(strlen($row_share['vip_id']) > 10)
                            {
                                $sql4 = "SELECT fb_name FROM fb WHERE fb_id='".$row_share['vip_id']."'";
                                $res4 = mysql_query($sql4);
                                $row4 = mysql_fetch_array($res4);
                                echo $row4['fb_name'] != "" ? $row4['fb_name'] : "-";
                            }
                            else
                            {
                                $sql4 = "SELECT m_name FROM member WHERE member_no='".$row_share['vip_id']."'";
                                $res4 = mysql_query($sql4);
                                $row4 = mysql_fetch_array($res4);
                                echo $row4['m_name'] != "" ? $row4['m_name'] : "-";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if(strlen($row_share['member_id']) > 10)
                            {
                                $sql3 = "SELECT fb_name FROM fb WHERE fb_id='".$row_share['member_id']."'";
                                $res3 = mysql_query($sql3);
                                $row3 = mysql_fetch_array($res3);
                                echo $row3['fb_name'] != "" ? $row3['fb_name'] : "-";
                            }
                            else
                            {
                                $sql3 = "SELECT m_name FROM member WHERE member_no='".$row_share['member_id']."'";
                                $res3 = mysql_query($sql3);
                                $row3 = mysql_fetch_array($res3);
                                echo $row3['m_name'] != "" ? $row3['m_name'] : "-";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $p_sql = "SELECT p_name FROM product WHERE id='".$row_share['p_id']."'";
                            $p_res = mysql_query($p_sql);
                            $p_row = mysql_fetch_array($p_res);
                            echo $p_row['p_name'];
                            ?>
                        </td>
                        <td><?php echo $row_share['pay_time']; ?></td>
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
                            <center><input type="button" class="btn btn-default" value="返回" onclick="window.history.back(-1);">&nbsp;&nbsp;</center>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
             
           
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

