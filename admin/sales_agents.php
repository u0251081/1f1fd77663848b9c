<table class="table responsive table-bordered">
    <thead>
    <tr>
        <h4 class="widgettitle" style="text-align: center;">行銷經理代銷列表</h4>
    </tr>
    <tr>
        <th width="5%" align="center">編號</th>
        <th width="25%" align="center">行銷經理編號</th>
        <th width="35%" align="center">商品名稱</th>
        <th width="15%" align="center">目前數量</th>
        <th align="center">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sql = "SELECT *,a.id AS aid FROM sales_agents AS a JOIN seller_manager AS b JOIN product AS c ON a.manager_id = b.manager_no AND a.p_id = c.id";
    $res = mysql_query($sql);
    while ($row = mysql_fetch_array($res))
    {
        ?>
        <tr align="center">
            <td align="center"><?php echo $row['aid']; ?></td>
            <td align="center">
                <?php
                if(strlen($row['member_id']) > 10)
                {
                    $member_id_sql = "SELECT * FROM fb WHERE fb_id='".$row['member_id']."'";
                    $member_id_res = mysql_query($member_id_sql);
                    $member_id_row = mysql_fetch_array($member_id_res);
                    echo "<a href='home.php?url=edit_fb_member&id=".$member_id_row['id']."' target=\"_blank\">".$row['manager_id']."</a>";
                }
                else
                {
                    $member_id_sql = "SELECT * FROM member WHERE member_no='".$row['member_id']."'";
                    $member_id_res = mysql_query($member_id_sql);
                    $member_id_row = mysql_fetch_array($member_id_res);
                    echo "<a href='home.php?url=edit_member&id=".$member_id_row['id']."' target=\"_blank\">".$row['manager_id']."</a>";
                }
                ?>
            </td>
            <td><?php echo $row['p_name']; ?></td>
            <td><?php echo $row['sales_qty']; ?></td>
            <td>
                <a href="javascript:void(0);" onclick="edit_fun(<?php echo $row['id'].",".$row['member_id'].",".$row['manager_no']; ?>)" class="btn btn-primary">配置代銷數量</a>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
分頁在這

<script>
    function edit_fun(id,m_id,mg_id)
    {
        location.href='home.php?url=set_number&p_id='+id + "&m_id=" + m_id + "&mg_id=" + mg_id;
    }
</script>