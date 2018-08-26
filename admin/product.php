<?php

use Base17Mai\Product;

$Product = new Product();
$list = $Product->listAllProducts();

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
<style>
    th {
        font-size: 120px;
    }
</style>
<table class="DataTable table responsive table-bordered">
    <thead>
    <tr>
        <h4 class="widgettitle" style="text-align: center;">商品列表</h4>
    </tr>
    <tr>
        <th align="center">編號</th>
        <th align="center">商品名稱</th>
        <th align="center">登記數量</th>
        <th align="center">剩餘數量</th>
        <th align="center">上架日期</th>
        <th align="center">狀態</th>
        <th align="center">操作</th>
    </tr>
    </thead>
    <tbody id="tableBody">
    </tbody>
</table>


<script>
    var productList = JSON.parse('<?= json_encode($list) ?>');

    $(function () {
        refreshTable(productList);
        $('.DataTable').DataTable();
    });

    function refreshTable(list) {
        let tbody = $('tbody#tableBody');
        let mainContent = document.createElement('tbody');
        $.each(list, function (ind, ele) {
            let tr = document.createElement('tr');
            $.each(ele, function (ind, ele) {
                let td = document.createElement('td');
                td.innerText = ele;
                td.style = "font-size: 2.5vh";
                tr.append(td);
            });
            let delbtn = generateDelBtn(ele.productID);
            let modify = generateModBtn(ele.productID);
            let td = document.createElement('td');
            td.append(modify);
            td.append(delbtn);
            tr.append(td);
            mainContent.append(tr);
            // mainContent += tr.outerHTML;
            // tbody.append(tr);
        });
        // tbody.html(mainContent);
        tbody.replaceWith(mainContent);
    }

    function generateDelBtn(id) {
        let icon = document.createElement('i');
        icon.className = "iconsweets-trashcan iconsweets-white";
        let link = document.createElement('a');
        link.href = "javascript:void(0);";
        let action = function () {
            delete_fun(id);
        };
        link.addEventListener('click', action);
        link.className = "btn btn-danger";
        link.style = "color:#fff;";
        link.append(icon);
        link.innerHTML += '&nbsp;&nbsp;&nbsp;';
        link.append("刪除");
        return link;
    }

    function generateModBtn(id) {
        let icon = document.createElement('i');
        icon.className = "iconsweets-bandaid iconsweets-white";
        let link = document.createElement('a');
        link.href = "javascript:void(0);";
        let action = function () {
            edit_fun(id);
        };
        link.addEventListener('click', action);
        link.className = "btn";
        link.style = "color:#fff; background: green;";
        link.append(icon);
        link.innerHTML += '&nbsp;&nbsp;&nbsp;';
        link.append("修改");
        return link;
    }

    function insert_btn() {
        location.href = 'home.php?url=editProduct';
    }

    function edit_fun(id) {
        location.href = 'home.php?url=editProduct&id=' + id;
    }

    function delete_fun(id) {
        if (confirm("確定要刪除?")) {
            showMessage('execute delete ' + id);
            ajax17mai('Product','DeleteProduct',{},{productID:id});
        }
    }
</script>