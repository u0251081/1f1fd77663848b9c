<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li><a href="index.php?url=member_center">會員專區</a></li>
                <li class="active">常用收件人</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->

<div class="container">
    <p><a href="index.php?url=add_addressee_set" class="btn btn-primary">新增</a></p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>編號</th>
                <th>收件人姓名</th>
                <th>連絡電話</th>
                <th>地址</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(@$_SESSION['member_no'] != "")
        {
            $sql = "SELECT *,a.id AS aid FROM addressee_set AS a JOIN city AS b JOIN area AS c ON a.city_id = b.id AND a.area_id = c.id WHERE a.m_id=".$_SESSION['member_no'];
        }
        else
        {
            $sql = "SELECT *,a.id AS aid FROM addressee_set AS a JOIN city AS b JOIN area AS c ON a.city_id = b.id AND a.area_id = c.id WHERE a.m_id=".$_SESSION["fb_id"];
        }
        $res = mysql_query($sql);
        while ($row = mysql_fetch_array($res))
        {
            ?>
            <tr>
                <td><?php echo $row['aid']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['cellphone']; ?></td>
                <td><?php echo $row['city'].$row['area'].$row['address']; ?></td>
                <td>
                    <a href="index.php?url=edit_addressee_set&id=<?php echo $row['aid']; ?>" class="btn btn-success">修改</a>&nbsp;&nbsp;
                    <a href="javascript:void(0);" name="delete_btn" delete_id="<?php echo $row['aid']; ?>" class="btn btn-danger">刪除</a>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>
<br><br><br><br><br>
<script>
    $(function ()
    {
        $("#aa-slider").hide();
        $("html,body").scrollTop(750);
    });

    $("a[name='delete_btn']").click(function()
    {
        var id = $(this).attr('delete_id');
        $(this).attr('href','index.php?url=delete_fun2&id='+id+'&pg=addressee_set');
    });
</script>