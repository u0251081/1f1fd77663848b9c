<?php
/**
 * Edited by PhpStorm.
 * User: Xin-an
 * Date: 2018/9/1
 * Time: 下午 06:31
 */

use Base17Mai\Consumer;

$Consumer = new Consumer($member_id);
$OrderList = $Consumer->ListOrder();
?>

<style>
    #pay_check_div {
        margin-top: 18%;
        margin-bottom: 26%;
    }

    @media max-width:

    768px

    ) {
        #pay_check_div {
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
                <th colspan="6"><h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">訂單查看</h3></th>
            </tr>
            <tr style="background: #DDDDDD;">
                <th style="text-align: center;">編號</th>
                <th style="text-align: center;">建立時間</th>
                <th style="text-align: center;">購買金額</th>
                <th style="text-align: center;">付款方式</th>
                <th style="text-align: center;">付款狀態</th>
                <th style="text-align: center;">詳細</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($OrderList as $item) {
                ?>
                <tr id="<?= $item['OrderNO'] ?>">
                    <td><?= $item['OrderNO'] ?></td>
                    <td><?= $item['OrderTime'] ?></td>
                    <td><?= $item['Total'] ?></td>
                    <td><?= $item['PayType'] ?></td>
                    <td><?= $item['OrderStatus'] ?></td>
                    <td width="180">
                        <a class="btn btn-primary" href="index.php?url=order_detail&id=<?= $item['OrderNO'] ?>">
                            查看
                        </a>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr align="right">
                <td colspan="6">
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

    function go_back() {
        location.href = 'index.php?url=member_center';
    }

    $("input[name='cancel_btn']").click(function () {
        var o_id = $(this).attr('o_id');
        if (confirm('是否取消這筆訂單')) {
            $.ajax
            ({
                url: "ajax.php", //接收頁
                type: "POST", //POST傳輸
                data: {type: "clean_order", o_id: o_id}, // key/value
                dataType: "text", //回傳形態
                success: function (i) //成功就....
                {
                    if (i == 1) {
                        alert('取消成功!');
                        location.href = 'index.php?url=order_search';
                    }
                    else {
                        alert('意外的錯誤，請檢查網路環境後再次操作');
                    }
                },
                error: function ()//失敗就...
                {
                }
            });
        }
    });
</script>