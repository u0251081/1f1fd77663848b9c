<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2019/3/17
 * Time: 下午 09:36
 */

use Base17Mai\Manager, Base17Mai\Member;

$Manager = new Manager();
$Member = new Member();

$manager = $Manager->GetManagerInformation(['manager_no', 'member_id'], ['id' => $_GET['id']]);
if (!isset($manager[0])) exit('嚴重錯誤，請回上一頁');
$manager = $manager[0];
$ManagerNO = $manager['manager_no'];
$MemberInfo = $Member->GetMemberInformation(['m_name', 'email'], ['member_no' => $manager['member_id']]);
if (!isset($MemberInfo[0])) exit('嚴重錯誤，請回上一頁再試一次');
$MemberInfo = $MemberInfo[0];
$Crews = $Manager->ListCrewMemberNO($ManagerNO);
foreach ($Crews as $key => $crew) {
    $crewInfo = $Member->GetMemberInformation(['id', 'email', 'm_name', 'cellphone'], ['member_no' => $crew]);
    if (isset($crewInfo[0])) {
        $crewInfo = $crewInfo[0];
        $Crews[$key] = $crewInfo;
    } else unset($Crews[$key]);
}
?>
<div class="widget">
    <h4 class="widgettitle"><?= $MemberInfo['email'] ?>&nbsp;&nbsp;&nbsp;的家族成員</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post">
            <p>
                <label>家長姓名</label>
                <span class="field" style="font-size: large;"><?= $MemberInfo['m_name'] ?></span>
            </p>
            <p>
                <label>家長代號</label>
                <span class="field" style="font-size: large;"><?= $MemberInfo['email'] ?></span>
            </p>
            <table class="table table-bordered table-responsive table-condensed">
                <thead>
                <tr style="background: #DDDDDD;">
                    <th>編號</th>
                    <th>成員帳號</th>
                    <th>成員姓名</th>
                    <th>連絡電話</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($Crews as $crew): ?>
                    <tr>
                        <td><?= $crew['id'] ?></td>
                        <td><?= $crew['email'] ?></td>
                        <td><?= $crew['m_name'] ?></td>
                        <td><?= $crew['cellphone'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php
                /*
                while ($row_share = mysql_fetch_array($res2)) {
                    if ($row_share['manager_id'] == $row_share['member_id']) {
                        ?>
                        <tr>
                            <td><?php echo "-"; ?></td>
                            <td>
                                <?php
                                if (strlen($row_share['m_id']) > 10) {
                                    $sql3 = "SELECT fb_name FROM fb WHERE fb_id='" . $row_share['m_id'] . "'";
                                    $res3 = mysql_query($sql3);
                                    $row3 = mysql_fetch_array($res3);
                                    echo $row3['fb_name'] != "" ? $row3['fb_name'] : "-";
                                } else {
                                    $sql3 = "SELECT m_name FROM member WHERE member_no='" . $row_share['m_id'] . "'";
                                    $res3 = mysql_query($sql3);
                                    $row3 = mysql_fetch_array($res3);
                                    echo $row3['m_name'] != "" ? $row3['m_name'] : "-";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $p_sql = "SELECT p_name,p_profit FROM product WHERE id='" . $row_share['p_id'] . "'";
                                $p_res = mysql_query($p_sql);
                                $p_row = mysql_fetch_array($p_res);
                                echo $p_row['p_name'];
                                ?>
                            </td>
                            <td><?php echo $row_share['pay_time']; ?></td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <td>
                                <?php
                                if (strlen($row_share['vip_id']) > 10) {
                                    $sql4 = "SELECT fb_name FROM fb WHERE fb_id='" . $row_share['vip_id'] . "'";
                                    $res4 = mysql_query($sql4);
                                    $row4 = mysql_fetch_array($res4);
                                    echo $row4['fb_name'] != "" ? $row4['fb_name'] : "-";
                                } else {
                                    $sql4 = "SELECT m_name FROM member WHERE member_no='" . $row_share['vip_id'] . "'";
                                    $res4 = mysql_query($sql4);
                                    $row4 = mysql_fetch_array($res4);
                                    echo $row4['m_name'] != "" ? $row4['m_name'] : "-";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if (strlen($row_share['member_id']) > 10) {
                                    $sql3 = "SELECT fb_name FROM fb WHERE fb_id='" . $row_share['member_id'] . "'";
                                    $res3 = mysql_query($sql3);
                                    $row3 = mysql_fetch_array($res3);
                                    echo $row3['fb_name'] != "" ? $row3['fb_name'] : "-";
                                } else {
                                    $sql3 = "SELECT m_name FROM member WHERE member_no='" . $row_share['member_id'] . "'";
                                    $res3 = mysql_query($sql3);
                                    $row3 = mysql_fetch_array($res3);
                                    echo $row3['m_name'] != "" ? $row3['m_name'] : "-";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $p_sql = "SELECT p_name FROM product WHERE id='" . $row_share['p_id'] . "'";
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
                */
                ?>
                </tbody>
            </table>
            <div class="container">
                <div class="row">
                    <center><input type="button" class="btn btn-default" value="返回"
                                   onclick="window.history.back(-1);">&nbsp;&nbsp;
                    </center>
                </div>
            </div>


        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

