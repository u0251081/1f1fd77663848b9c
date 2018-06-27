<!--<a href="javascript:void(0);" class="btn btn-primary" onclick="insert_btn()">新增</a>-->

<table class="table responsive table-bordered">
    <thead>
    <tr>
        <h4 class="widgettitle" style="text-align: center;">FB註冊會員列表</h4>
    </tr>
    <tr>
        <th align="center">編號</th>
        <th align="center">會員帳號</th>
        <th align="center">會員姓名</th>
        <th align="center">聯絡電話</th>
        <th align="center">地址</th>
        <th align="center">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sql = "SELECT * FROM member";
    //echo $sql;
    $res = mysql_query($sql);
    while ($row = mysql_fetch_array($res))
    {
        if($row['fb_id'])
        {
        ?>
        <tr align="center">
            <td width="5%" align="center"><?php echo $row['id']; ?></td>
            <td width="5%" align="center"><?php echo $row['email']; ?></td>
            <td width="5%" align="center"><?php echo $row['m_name']; ?></td>
            <td width="8%"><?php echo $row['cellphone']; ?></td>
            <td width="15%">
                <?php
                $city_sql = "SELECT city FROM city WHERE id='".$row['city_id']."'";
                $city_res = mysql_query($city_sql);
                $city_row = mysql_fetch_array($city_res);

                $area_sql = "SELECT area FROM area WHERE id='".$row['area_id']."'";
                $area_res = mysql_query($area_sql);
                $area_row = mysql_fetch_array($area_res);

                echo $city_row['city'].$area_row['area'].$row['address'];
                ?>
            </td>
            <td width="13%">
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
    }
    ?>
    </tbody>
</table>


<script>
    function insert_btn()
    {
        location.href='home.php?url=add_supplier';
    }

    function edit_fun(id)
    {
        location.href='home.php?url=edit_fb_member&id='+id;
    }

    function delete_fun(id)
    {
        if(confirm("確定要刪除?"))
        {
            location.href='home.php?url=delete_fun&d_id='+id+'&pg=fb_member';
        }
    }
</script>
