<?php
$id = $_GET['id'];
$sql = "SELECT * FROM rule_type WHERE id='$id'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
?>
<div class="widget">
    <h4 class="widgettitle">修改條例類型</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post">
            <p>
                <label>類型名稱</label>
                <span class="field"><input type="text" name="type_name" class="input-xxlarge" value="<?php echo $row['content']; ?>" placeholder="請輸入類型名稱"/></span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="btn btn-primary span1" value="修改">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" class="btn span1" value="返回" onclick="window.history.back(-1);">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
@$type_name = $_POST['type_name'];

if(isset($_POST['btn']))
{
    $sql = "UPDATE rule_type SET content='$type_name' WHERE id='$id'";
    mysql_query($sql);
    ?>
    <script>
        alert('修改成功');
        location.href='home.php?url=rule_type';
    </script>
    <?php
}
?>