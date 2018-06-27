<?php
@session_start();
include_once "mysql.php";
sql();

@$type = $_GET['pg'];
if($type == "suppleir")
{
    d_suppleir();
}
else if($type == "product")
{
    d_product();
}
else if($type == 'member')
{
    d_member();
}
else if($type == 'fb_member')
{
    d_fb_member();
}
else if($type == 'seller_manager')
{
    d_seller_manager();
}

function d_suppleir()
{
    $id = $_GET['d_id'];
    $sql = "DELETE FROM supplier WHERE id='$id'";
    mysql_query($sql);
    ?>
    <script>
        alert('刪除成功');
        location.href='home.php?url=supplier';
    </script>
    <?php
}

function d_product()
{
    $id = $_GET['d_id'];
    $sql = "DELETE FROM product WHERE id='$id'";
    mysql_query($sql);
    $sql = "DELETE FROM price WHERE p_id='$id'";
    mysql_query($sql);
    $sql = "DELETE FROM standard WHERE p_id='$id'";
    mysql_query($sql);
    $sql = "DELETE FROM product_class WHERE product_id='$id'";
    mysql_query($sql);
    $sql = "SELECT * FROM product_img WHERE p_id='$id'";
    $res = mysql_query($sql);
    while ($row = mysql_fetch_array($res))
    {
        unlink($row['picture']);
    }
    $sql = "DELETE FROM product_img WHERE p_id='$id'";
    mysql_query($sql);
    ?>
    <script>
        alert('刪除成功');
        location.href='home.php?url=product';
    </script>
    <?php
}

function d_member()
{
    @$d_id = $_GET['d_id'];
    $sql = "DELETE FROM member WHERE id='$d_id'";
    mysql_query($sql);
    ?>
    <script>
        alert('刪除成功');
        location.href='home.php?url=member';
    </script>
    <?php
}

function d_fb_member()
{
    @$d_id = $_GET['d_id'];
    $sql = "DELETE FROM fb WHERE id='$d_id'";
    mysql_query($sql);
    ?>
    <script>
        alert('刪除成功');
        location.href='home.php?url=fb_member';
    </script>
    <?php
}

function d_seller_manager()
{
    @$d_id = $_GET['d_id'];
    $sql = "DELETE FROM seller_manager WHERE id='$d_id'";
    mysql_query($sql);
    ?>
    <script>
        alert('刪除成功');
        location.href='home.php?url=seller_manager';
    </script>
    <?php
}