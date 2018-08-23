<?php
$total = 15; //預設每筆頁數
$page = 1; //預設初始頁數

//--------以上兩樣即為 limit 0,3 ==($page,$total)

if ($page < 1 || $page == "") {
    $page = 1; //初始頁數
}

if (isset($_GET['page'])) {
    $page = $_GET['page'];
}

$sql = "SELECT * FROM product ORDER BY id DESC";
$res = mysql_query($sql);
$row = mysql_num_rows($res); //透過mysql_num_rows()取得總頁數
$maxpage = ceil($row / $total); //*計算總頁數=(總筆數/每頁筆數)後無條件進位避免 總筆數與總頁數除不盡時剩餘頁無法顯示

if ($page > $maxpage) {
    $page = $maxpage; //如果初始頁數大於總頁數就等於總頁數
}

$start = $total * ($page - 1); // 初始頁數 =每筆頁數*(頁預設數-1)
$sql = "SELECT * FROM product ORDER BY id DESC LIMIT " . $start . "," . $total;
$res = mysql_query($sql); //將查詢資料存到 $result 中
$itemCount = mysql_num_rows($res);
while ($product = mysql_fetch_array($res)) {
    $ary[] = $product;
}
/*
for ($i = 1; $i <= $row; $i++) {
    $ary[$i] = mysql_fetch_array($res);
}
*/

?>
<a href="javascript:void(0);" class="btn btn-primary" onclick="insert_btn()">新增</a>

<table class="table responsive table-bordered">
    <thead>
    <tr>
        <h4 class="widgettitle" style="text-align: center;">商品列表</h4>
    </tr>
    <tr>
        <th align="center">編號</th>
        <th align="center">商品名稱</th>
        <th align="center">商品數量</th>
        <th align="center">剩餘數量</th>
        <th align="center">上架日期</th>
        <th align="center">狀態</th>
        <th align="center">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($ary as $key => $value) {
        $product_ID = $value['id'];
        $product_Name = $value['p_name'];
        $product_QuantityOrigin = $value['p_qty'];
        $product_QuantityRemain = $value['rem_qty'];
        $product_status = $value['added'];
        $product_UpDate = $value['add_day'];
        if ($product_status == 1) {
            $status = '上架';
        } else {
            $status = '下架';
        }
        ?>
        <tr align="center">
            <td width="2%" align="center"><?= $product_ID ?></td>
            <td width="8%" align="center"><?= $product_Name ?></td>
            <td width="5%"><?= $product_QuantityOrigin ?></td>
            <td width="5%"><?= $product_QuantityRemain ?></td>
            <td width="8%"><?= $product_UpDate ?></td>
            <td width="2%"><?= $status ?>
            </td>
            <td width="7%">
                <a href="javascript:void(0);" onclick="edit_fun(<?= $product_ID ?>)" class="btn"
                   style="color:#fff; background: green;">
                    <i class="iconsweets-bandaid iconsweets-white"></i>修改
                </a>
                <a href="javascript:void(0);" onclick="delete_fun(<?= $product_ID ?>)" class="btn btn-danger"
                   style="color:#fff;">
                    <i class="iconsweets-trashcan iconsweets-white"></i>刪除
                </a>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <table width="90%">
        <tr>
            <td align="center">
                <br/>
                <?php
                // previous page
                if ($page == 1) {
                    print '上一頁&nbsp;&nbsp;';
                } else {
                    $prev_page = $page - 1;
                    print "<a style=\"text-decoration:none;\" href=\"?url=product&page={$prev_page}\">上一頁&nbsp;&nbsp;</a>";
                }

                // current or optional page
                print "<select onChange='location = this.options[this.selectedIndex].value;' class='span1'>";
                for ($i = 1; $i <= $maxpage; $i++) {
                    if ($i == $page) {
                        print "<option selected=\"selected\" value=\"{$i}\">{$i}</option>";
                    } else {
                        echo "<option value=\"home.php?url=product&page={$i}\">{$i}</option>";
                    }
                }
                print "</select>";

                // next page
                if ($page == $maxpage) {
                    print "&nbsp;&nbsp;下一頁";
                } else {
                    $next_page = $page + 1;
                    print "<a style=\"text-decoration:none;\" href=\"?url=product&page={$next_page}\">下一頁&nbsp;&nbsp;</a>";
                } ?>
                <br/>
            </td>
        </tr>
    </table>
</table>


<script>
    function insert_btn() {
        location.href = 'home.php?url=add_product';
    }

    function edit_fun(id) {
        location.href = 'home.php?url=edit_product&id=' + id;
    }

    function delete_fun(id) {
        if (confirm("確定要刪除?")) {
            location.href = 'home.php?url=delete_fun&d_id=' + id + '&pg=product';
        }
    }
</script>