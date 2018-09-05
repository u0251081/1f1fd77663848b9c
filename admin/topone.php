<?php
$SQL = 'select * from consumer_order where datediff(now(), OrderTime) = 0;';
$rst = pdo_select_sql($SQL);
$num = count($rst);
?>
<style>
    ul, li {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .abgne_tab {
        clear: left;
        width: 400px;
        margin: 10px 0;
    }

    ul.tabs {
        width: 100%;
        height: 32px;
        border-bottom: 1px solid #999;
        border-left: 1px solid #999;
    }

    ul.tabs li {
        float: left;
        height: 31px;
        line-height: 31px;
        overflow: hidden;
        position: relative;
        margin-bottom: -1px; /* 讓 li 往下移來遮住 ul 的部份 border-bottom */
        border: 1px solid #999;
        border-left: none;
        background: #e1e1e1;
    }

    ul.tabs li a {
        display: block;
        padding: 0 30px;
        height: 31px;
        color: #000;
        border: 1px solid #fff;
        text-decoration: none;
    }

    ul.tabs li a:hover {
        background: #ccc;
    }

    ul.tabs li.active {
        background: #fff;
        border-bottom: 1px solid #fff;
    }

    ul.tabs li.active a:hover {
        background: #fff;
    }

    div.tab_container {
        clear: left;
        width: 100%;
        border: 1px solid #999;
        border-top: none;
        background: #fff;
    }

    div.tab_container .tab_content {
        padding: 20px;
    }

    div.tab_container .tab_content h2 {
        margin: 0 0 20px;
    }

</style>
<form class="editprofileform" method="post" enctype="multipart/form-data">
    <div class="row-fluid">
        <div class="span12">
            <div class="widgetbox">
                <?php
                // $sql = "SELECT * FROM consumer_order WHERE datediff(now(),`order_time`)=0";
                // $res = mysql_query($sql);
                // $row = mysql_fetch_array($res);
                // $num = mysql_num_rows($res);
                ?>
                <h4 class="widgettitle">今日訂單數量：<?= $num ?></h4>
                <div class="widgetcontent" id="check">
                    <ul class="tabs" style="font-size: 15px;">
                        <li><a href="#tab1">已付款</a></li>
                        <li><a href="#tab2">未付款</a></li>
                        <li><a href="#tab3">暢銷排行榜</a></li>
                    </ul>

                    <div class="tab_container">
                        <div id="tab1" class="tab_content">
                            <table class="table responsive table-bordered">
                                <thead>
                                <tr>
                                    <th align="center">訂單編號</th>
                                    <th align="center">收件人資料</th>
                                    <th align="center">收貨時間</th>
                                    <th align="center">商品明細</th>
                                    <th align="center">訂單狀態</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sql = "SELECT * FROM consumer_order,addressee_set WHERE datediff(now(),`order_time`)=0 AND addressee_set.m_id=consumer_order.m_id AND addressee_set.order_no=consumer_order.order_no ORDER BY consumer_order.id";
                                $sql = "SELECT * FROM consumer_order left join order_address on consumer_order.id = OrderID WHERE datediff(now(), OrderTime)=0 ORDER BY consumer_order.id";
                                $result = pdo_select_sql($sql);//將查詢資料存到 $result 中
                                foreach ($result as $key => $item) {
                                    if ($item['OrderStatus'] === '1') {
                                        ?>

                                        <tr align="center">
                                            <td width="1%" align="center"><?= $item['OrderNO'] ?></td>
                                            <td width="5%" align="center">
                                                <p>姓名：<?= $item['Recipient']; ?></p>
                                                <p>電話：<?= $item['CellPhone']; ?></p>
                                                <p>地址：<?= $item['Address']; ?></p>
                                            </td>
                                            <td width="1%">
                                                <?php
                                                switch ($item['DateType']) {
                                                    case '1':
                                                        echo '週一至週五';
                                                        break;
                                                    case '2':
                                                        echo '周六';
                                                        break;
                                                    case '3':
                                                        echo '不指定';
                                                        break;
                                                    default:
                                                        echo '未選取';
                                                        break;
                                                }
                                                ?>
                                            </td>
                                            <td width="1%">
                                                <?php
                                                $sql2 = "SELECT PName, specification, Quantity FROM consumer_order_detail WHERE OrderID = {$item['id']};";
                                                $res2 = mysql_query($sql2);
                                                while ($row2 = mysql_fetch_array($res2)) {
                                                    echo $row2['PName'] . '/', $row2['specification'] . '&nbsp;&nbsp;:&nbsp;&nbsp;' . $row2['Quantity'];
                                                    echo '</br>';
                                                }
                                                ?>
                                            </td>
                                            <td width="1%">
                                                <?php
                                                switch ($item['OrderStatus']) {
                                                    case null:
                                                        echo '訂單無效';
                                                        break;
                                                    case '0':
                                                        echo '未付款';
                                                        break;
                                                    case '1':
                                                        echo '已付款';
                                                        break;
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>

                            </table>
                        </div>
                        <div id="tab2" class="tab_content">
                            <table class="table responsive table-bordered">
                                <thead>
                                <tr>

                                    <th align="center">訂單編號</th>
                                    <th align="center">收件人資料</th>
                                    <th align="center">收貨時間</th>
                                    <th align="center">商品明細</th>
                                    <th align="center">訂單狀態</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($result as $key => $item) {
                                    if ($item['OrderStatus'] === '0') {
                                        ?>

                                        <tr align="center">
                                            <td width="1%" align="center"><?= $item['OrderNO'] ?></td>
                                            <td width="5%" align="center">
                                                <p>姓名：<?= $item['Recipient']; ?></p>
                                                <p>電話：<?= $item['CellPhone']; ?></p>
                                                <p>地址：<?= $item['Address']; ?></p>
                                            </td>
                                            <td width="1%">
                                                <?php
                                                switch ($item['DateType']) {
                                                    case '1':
                                                        echo '週一至週五';
                                                        break;
                                                    case '2':
                                                        echo '周六';
                                                        break;
                                                    case '3':
                                                        echo '不指定';
                                                        break;
                                                    default:
                                                        echo '未選取';
                                                        break;
                                                }
                                                ?>
                                            </td>
                                            <td width="1%">
                                                <?php
                                                $sql2 = "SELECT PName, specification, Quantity FROM consumer_order_detail WHERE OrderID = {$item['id']};";
                                                $res2 = mysql_query($sql2);
                                                while ($row2 = mysql_fetch_array($res2)) {
                                                    echo $row2['PName'] . '/', $row2['specification'] . '&nbsp;&nbsp;:&nbsp;&nbsp;' . $row2['Quantity'];
                                                    echo '</br>';
                                                }
                                                ?>
                                            </td>
                                            <td width="1%">
                                                <?php
                                                switch ($item['OrderStatus']) {
                                                    case null:
                                                        echo '訂單無效';
                                                        break;
                                                    case '0':
                                                        echo '未付款';
                                                        break;
                                                    case '1':
                                                        echo '已付款';
                                                        break;
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>

                        </div>
                        <div id="tab3" class="tab_content">
                            <table class="table responsive table-bordered">
                                <thead>
                                <tr>
                                    <th align="center">編號</th>
                                    <th align="center">商品名稱</th>
                                    <th align="center">已賣數量</th>
                                    <th align="center">剩餘數量</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sql3 = "SELECT *, (QuantityOrigin - QuantityRemain) as selled FROM product WHERE Prelease = 1 ORDER BY selled DESC";
                                $rst = pdo_select_sql($sql3);
                                foreach ($rst as $key => $item) {


                                    ?>
                                    <tr align="center">
                                        <td width="1%" align="center"><?=$item['id']; ?></td>
                                        <td width="1%" align="center"><?=$item['PName']; ?></td>
                                        <td width="1%" align="center"><?=$item['selled']; ?></td>
                                        <td width="1%" align="center"><?=$item['QuantityRemain']; ?></td>

                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
            </div>


        </div>

        <!--span8-->
        <!-- <div class="span4 profile-right">
                            <div class="widgetbox profile-photo">
                                <div class="headtitle">
                                    
                                    <h4 class="widgettitle">銷售排行榜</h4>
                                </div>
                                <div class="widgetcontent" >
                                    <div class="profilethumb">
                                        <img id="profile_img" src="<?php echo $row['img']; ?>" width="200" height="200"/>
                                    </div>
                                </div>
                            </div>
                            <input type="file" style="display: none;" name="upload" id="fileinput"/>
                        </div> -->
        <!--span4-->
    </div>

</form>

<?php
@$logo_tmp = $_FILES['upload']['tmp_name'];
@$logo = $_FILES['upload']['name'];
@$filedir = "images/photos/" . $logo;//指定上傳資料
@$old_img = $_POST['old_img'];
@$password = $_POST['password'];
@$name = $_POST['name'];
if (isset($password) || isset($name)) {
    if ($logo != "") {
        unlink($old_img);
        $sql = "UPDATE admin SET password='$password', `name`='$name', img='$filedir' WHERE id='" . $_SESSION['id'] . "'";
        mysql_query($sql);
        move_uploaded_file($logo_tmp, $filedir);
    } else {
        $sql = "UPDATE admin SET password='$password', `name`='$name' WHERE id='" . $_SESSION['id'] . "'";
        mysql_query($sql);
    }
    ?>
    <script>
        alert('修改成功');
        location.href = 'home.php';
    </script>
    <?php
}
?>

<script>
    $("#edit_pic").click(function () {
        $('#fileinput').click();
    });

    //----------------顯示密碼------------------//
    var password = $("input[name='password']");
    var showpassword = $('#show');
    var inputpassword = $('<input type="text" name="password" class="input-xlarge" />');
    showpassword.click(function () {
        if (this.checked) {
            password.replaceWith(inputpassword.val(password.val()));
        }
        else {
            inputpassword.replaceWith(password.val(inputpassword.val()));
        }
    });
    $(function () {
        // 預設顯示第一個 Tab
        var _showTab = 0;
        $('#check').each(function () {
            // 目前的頁籤區塊
            var $tab = $(this);

            var $defaultLi = $('ul.tabs li', $tab).eq(_showTab).addClass('active');
            $($defaultLi.find('a').attr('href')).siblings().hide();

            // 當 li 頁籤被點擊時...
            // 若要改成滑鼠移到 li 頁籤就切換時, 把 click 改成 mouseover
            $('ul.tabs li', $tab).click(function () {
                // 找出 li 中的超連結 href(#id)
                var $this = $(this),
                    _clickTab = $this.find('a').attr('href');
                // 把目前點擊到的 li 頁籤加上 .active
                // 並把兄弟元素中有 .active 的都移除 class
                $this.addClass('active').siblings('.active').removeClass('active');
                // 淡入相對應的內容並隱藏兄弟元素
                $(_clickTab).stop(false, true).fadeIn().siblings().hide();

                return false;
            }).find('a').focus(function () {
                this.blur();
            });
        });
    });
</script>
