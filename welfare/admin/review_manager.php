<?php
@$id = $_GET['id'];
@$arg = $_GET['arg'];
if(isset($arg) && $arg == 'n')
{
    $sql = "SELECT *,a.id AS aid FROM member AS a JOIN seller_manager AS b ON a.member_no = b.member_id WHERE a.id='".$_GET['id']."'";

}
else if(isset($arg) && $arg == 'fb')
{
    $sql = "SELECT *,a.id AS aid FROM fb AS a JOIN seller_manager AS b ON a.fb_id = b.member_id WHERE a.id='".$_GET['id']."'";

}
$res = mysql_query($sql);
@$row = mysql_fetch_array($res);
?>
<div class="widget">
    <h4 class="widgettitle">審核行銷經理</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post">
            <p>
                <label>經理姓名</label>
                <span class="field" style="font-size: large;">
                    <?php
                    if($arg == 'fb')
                    {
                        echo $row['fb_name'];
                    }
                    else
                    {
                        echo $row['m_name'];
                    }
                    ?>
                </span>
            </p>
            <p>
                <label>經理代號</label>
                <span class="field" style="font-size: large;"><?php echo $row['manager_no']; ?></span>
            </p>
<!--            <p>-->
<!--                <label>擅長行銷商品類別</label>-->
<!--                <span class="field" style="font-size: large;">-->
<!--                    --><?php
//                    $str = '';
//                    $class_sql = "SELECT `name` FROM `class` WHERE id = parent_id AND id IN(".$row['class_id'].")";
//                    $class_res = mysql_query($class_sql);
//                    while ($class_row = mysql_fetch_array($class_res))
//                    {
//                        @$str .= $class_row['name']." 、 ";
//                    }
//                    echo $str;
//                    ?>
<!--                </span>-->
<!--            </p>-->
            <p>
                <label>申請原因</label>
                <span class="field" style="font-size: large;">
                    <?php
                    echo $row['self_introduction'];
                    ?>
                </span>
            </p>
            <p>
                <label>申請審核</label>
                <span class="field">
                    <select name="apply_status" id="apply_status" class="uniformselect">
                        <option value="">請選擇</option>
                        <option value="0" <?php if($row['apply_status'] == 0){echo 'selected';} ?>>不通過</option>
                        <option value="1" <?php if($row['apply_status'] == 1){echo 'selected';} ?>>通過</option>
                        <option value="2" <?php if($row['apply_status'] == 2){echo 'selected';} ?>>待審核</option>
                    </select>
                </span>
            </p>
            <p id="error_info" style="display: none;">
                <label>失敗原因</label>
                <span class="field" style="font-size: large;">
                    <textarea cols="80" rows="5" class="span5" name="error_info"></textarea>
                </span>
            </p>
            <p>
                <label>開通權限</label>
                <span class="field" id="m_status">
                    <input type="radio" name="manager_status" value="1" <?php if($row['manager_status'] == 1){echo 'checked';} ?>>啟用&nbsp;&nbsp;
                    <input type="radio" name="manager_status" value="0" <?php if($row['manager_status'] == 0){echo 'checked';} ?>>停用
                </span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="span1 btn btn-primary" value="提交">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" class="btn span1" value="返回" onclick="location.href='home.php?url=seller_manager'">
            </p>
            <input type="hidden" value="<?php echo $row['id']; ?>" name="s_id">
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
@$s_id = $_POST['s_id'];
@$apply_status = $_POST['apply_status'];
@$error_info = $_POST['error_info'];
@$manager_status = $_POST['manager_status'];

if(isset($_POST['btn']))
{
    $sql = "UPDATE seller_manager SET apply_status='$apply_status', fail_info='$error_info', check_people='admin', manager_status='$manager_status', start_time='".date('Y-m-d H:i:s')."' WHERE id='$s_id'";
    mysql_query($sql);
    ?>
    <script>
        alert('審核完畢');
        location.href='home.php?url=seller_manager';
    </script>
    <?php
}
?>
<script>
    $("#apply_status").change(function()
    {
        if($(this).val() == 0)
        {
            //$("input[name='manager_status']:radio").eq(1).prop("checked",true);
            $("#error_info").show();
        }
        else
        {
            $("#error_info").hide();
        }
    });
</script>