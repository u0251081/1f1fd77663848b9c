<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<div class="widget">
    <h4 class="widgettitle">新增條例</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post">
            <p>
                <label>標題</label>
                <span class="field"><input type="text" name="title" class="input-xxlarge" placeholder="請輸入標題"/></span>
            </p>
            <p>
                <label>內容</label>
                <span class="field"><textarea name="content" id="rule_content" cols="30" rows="5" placeholder="請輸入內容"></textarea></span>
            </p>
            <p>
                <label>條例類型</label>
                <span class="field">
                    <select name="rule_type">
                        <option value="0">請選擇</option>
                        <?php
                        $rule_type_sql = "SELECT * FROM rule_type";
                        $rule_type_res = mysql_query($rule_type_sql);
                        while($rule_type_row = mysql_fetch_array($rule_type_res))
                        {
                            ?>
                            <option value="<?php echo $rule_type_row['id']; ?>"><?php echo $rule_type_row['content']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="btn btn-primary span1" value="提交">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" class="btn span1" value="清除">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
@$title = $_POST['title'];
@$content = $_POST['content'];
@$rule_type = $_POST['rule_type'];
if(isset($_POST['btn']))
{
    $sql = "INSERT INTO rule SET title='$title', content='$content', `day`='".date('Y-m-d H:i:s')."', rule_type='$rule_type'";
    mysql_query($sql);
    ?>
    <script>
        alert('新增成功');
        location.href='home.php?url=rule';
    </script>
    <?php
}
?>
<script>
    jQuery(function ()
    {
        CKEDITOR.replace('rule_content');
    });
</script>