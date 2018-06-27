<div class="widget">
    <h4 class="widgettitle">新增條例類型</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post">
            <p>
                <label>類型名稱</label>
                <span class="field"><input type="text" name="type_name" class="input-xxlarge" placeholder="請輸入類型名稱"/></span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="btn btn-primary span1" value="提交">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" class="btn span1" value="清除">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
@$type_name = $_POST['type_name'];

if(isset($_POST['btn']))
{
    $sql = "INSERT INTO rule_type SET content='$type_name'";
    mysql_query($sql);
    ?>
    <script>
        alert('新增成功');
        location.href='home.php?url=rule_type';
    </script>
    <?php
}
?>