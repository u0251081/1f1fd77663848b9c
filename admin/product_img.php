<?php

use Base17Mai\Product;

$Product = new Product();
$pageList = $Product->listAllProductsWithImage();
?>
<table class="DataTable table responsive table-bordered table-responsive table-condensed">
    <thead>
    <tr>
        <h4 class="widgettitle" style="text-align: center;">商品圖片列表</h4>
    </tr>
    <tr>
        <th align="center">編號</th>
        <th align="center">商品名稱</th>
        <th align="center">封面圖</th>
        <th align="center">新增日期</th>
        <th align="center">操作</th>
    </tr>
    </thead>
    <tbody></tbody>
    <tfoot></tfoot>
</table>

<script>
    var pageData = JSON.parse('<?= json_encode($pageList) ?>');

    $(function () {
        generateTable(pageData);
        $('.DataTable').DataTable();
    });

    function generateTable(List) {
        let mainContent = document.createElement('tbody');
        $.each(List,function(ind,ele){
            let tr = document.createElement('tr');
            $.each(ele,function(ind,ele) {
                let td = document.createElement('td');
                if (ind === 'Image') {
                    let img = document.createElement('img');
                    img.src = ele.Cover;
                    img.style = "height: 12vw;";
                    if (ele.Cover === '') td.append('圖片尚未設置');
                    else td.append(img);
                } else {
                    td.append(ele);
                }
                tr.append(td);
            });
            let td = document.createElement('td');
            let modBtn = generateModBtn(ele.productID);
            td.append(modBtn);
            tr.append(td);
            mainContent.append(tr);
        });
        $('.DataTable').find('tbody').replaceWith(mainContent);
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
        link.append("編輯");
        return link;
    }

    function insert_fun(id) {
        location.href = 'home.php?url=add_img&id=' + id;
    }

    function edit_fun(id) {
        location.href = 'home.php?url=edit_img&id=' + id;
    }
</script>