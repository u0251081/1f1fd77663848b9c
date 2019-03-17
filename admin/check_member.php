<?php

use Base17Mai\Consumer;
use function Base17Mai\take;

$member_no = take('member_no', '');
$Consumer = new Consumer($member_no);
$OrderList = $Consumer->ListOrder();
?>

<table class="DataTable table table-bordered table-responsive table-condensed" id="pay_check_div">
    <thead>
    <tr style="background: #DDDDDD;">
        <th>編號</th>
        <th>下單時間</th>
        <th>購買金額</th>
        <th>付款狀態</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($OrderList as $item): ?>
        <tr>
            <td><?= $item['OrderNO'] ?></td>
            <td><?= $item['OrderTime'] ?></td>
            <td><?= $item['Total'] ?></td>
            <td><?= $item['OrderStatus'] ?></td>
            <td>
                <input type="button" class="btn btn-primary" value="查看"
                       onclick="location.href='home.php?url=order_detail&id=<?= $item['id']; ?>'">
            </td>
        </tr>
    <?php endforeach; ?>
    <?php
    /*
    @$id = $_GET['id'];
    @$member_no = $_GET['member_no'];
    if (isset($member_no)) {
        $sql = "SELECT *,a.id AS aid FROM consumer_order AS a JOIN consumer_order2 AS b ON a.id = b.order1_id WHERE a.m_id='$member_no' GROUP BY order_no";
        //echo $sql;
        $res = mysql_query($sql);
        while ($row = mysql_fetch_array($res)) {
            if ($row['order_over'] != "") {
                ?>
                <tr id="order_list_<?php echo @$row['aid']; ?>">
                    <td><?php echo $row['order_no']; ?></td>
                    <td><?php echo $row['p_name']; ?></td>
                    <td><?php echo $row['o_price']; ?></td>
                    <td>
                        <?php
                        switch (@$row['is_effective']) {
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
                        <input type="button" class="btn btn-primary" value="查看"
                               onclick="location.href='home.php?url=order_detail&id=<?php echo $row['aid']; ?>'">
                    </td>
                </tr>
                <?php
            }
        }
    }
    */
    ?>
    </tbody>
</table>
<div class="container">
    <div class="row">
        <center>
            <input type="button" class="btn btn-default" value="返回"
                   onclick="window.history.back(-1);">&nbsp;&nbsp;
        </center>
    </div>
    <div class="row">
        <div class="container">
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        DTable.order([0, 'desc']).draw();
    });
</script>



