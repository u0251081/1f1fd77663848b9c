<?php
@session_start();
include_once "admin/mysql.php";
sql();

@$type = $_GET['pg'];
if($type == "addressee_set")
{
    d_addressee_set();
}

function d_addressee_set()
{
    $id = $_GET['id'];
    $sql ="DELETE FROM addressee_set WHERE id='$id'";
    mysql_query($sql);
    ?>
    <script>
        alert('刪除成功');
        location.href='index.php?url=addressee_set';
    </script>
    <?php
}