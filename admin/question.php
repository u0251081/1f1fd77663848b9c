<a href="javascript:void(0);" class="btn btn-primary" onclick="insert_btn()">新增</a>

<table class="table responsive table-bordered">
    <thead>
    <tr>
        <h4 class="widgettitle" style="text-align: center;">Q&A列表</h4>
    </tr>
    <tr>
        <th align="center">編號</th>
        <th align="center">標題</th>
        <th align="center">內容</th>
        <th align="center">建立日期</th>
        <th align="center">置頂</th>
        <th align="center">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sql = "SELECT * FROM question";
    $res = mysql_query($sql);
    while ($row = mysql_fetch_array($res))
    {
        ?>
        <tr align="center">
            <td width="3%" align="center"><?php echo $row['id']; ?></td>
            <td width="10%" align="center"><?php echo $row['title']; ?></td>
            <td width="15%"><?php echo $row['content']; ?></td>
            <td width="10%"><?php echo $row['day']; ?></td>
            <td width="10%">
                <?php switch( $row['focus'])
                    {
                        case '0':
                             echo "否";
                            break;
                        case '1':
                            echo "是";
                            break;
                        
                    }
 ?></td>
            <td width="8%">
                <a href="javascript:void(0);" onclick="edit_fun(<?php echo $row['id']; ?>)" class="btn"
                   style="color:#fff; background: green;">
                    <i class="iconsweets-bandaid iconsweets-white"></i>修改
                </a>
                <a href="javascript:void(0);" onclick="delete_fun(<?php echo $row['id']; ?>)" class="btn btn-danger"
                   style="color:#fff;">
                    <i class="iconsweets-trashcan iconsweets-white"></i>刪除
                </a>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>


<script>
    function insert_btn()
    {
        location.href='home.php?url=add_qusetion';
    }

    function edit_fun(id)
    {
        location.href='home.php?url=edit_question&id='+id;
    }

    function delete_fun(id)
    {
        if(confirm("確定要刪除?"))
        {
            location.href='home.php?url=delete_fun&q_id='+id+'&pg=question';
        }
    }
</script>
