<?php
@$id = $_GET['id'];
$sql = "SELECT p_name FROM product WHERE id='$id'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
?>
<div class="widget">
    <h4 class="widgettitle">供應商</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post" enctype="multipart/form-data">
            <p>
                <label>商品名稱</label>
                <span class="field"><input type="text" name="supplier_name" class="input-large" value="<?php echo $row['p_name']; ?>" placeholder="請輸入供應商名稱"/></span>
            </p>
            <p>
                <label>封面圖</label>
                <span class="field">
                    <input type="file" name="is_main">
                </span>
            </p>
            <p>
                <label>圖1</label>
                <span class="field">
                    <input type="file" name="picture[]">
                </span>
            </p>
            <p>
                <label>圖2</label>
                <span class="field">
                    <input type="file" name="picture[]">
                </span>
            </p>
            <p>
                <label>圖3</label>
                <span class="field">
                    <input type="file" name="picture[]">
                </span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="btn btn-primary" value="新增">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" class="btn" value="清除">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
@$is_main_tmp = $_FILES['is_main']['tmp_name']; //商品封面圖暫存
@$is_main = $_FILES['is_main']['name']; //商品圖

@$picture_tmp = $_FILES['picture']['tmp_name']; //圖片暫存
@$picture = $_FILES['picture']['name']; //圖片

$filedir = "images/product/";//指定上傳資料

if(isset($_POST['btn']))
{
    if($is_main != "")
    {
        $is_main = date("YmdHis").date("s").".jpg";
        move_uploaded_file($is_main_tmp,$filedir.$is_main);
        $sql = "INSERT INTO product_img SET p_id='$id', picture='".$filedir.$is_main."', added_day='".date('Y-m-d H:i:s')."', is_main='1'";
        mysql_query($sql);
    }

    if($picture != "")
    {
        for($i=0; $i<count($picture); $i++)
        {
            $picture[$i] = date("YmdHis").$i.".jpg";
            move_uploaded_file($picture_tmp[$i],$filedir.$picture[$i]);
            $sql = "INSERT INTO product_img SET p_id='$id', picture='".$filedir.$picture[$i]."', added_day='".date('Y-m-d H:i:s')."', is_main='0'";
            mysql_query($sql);
        }
    }
    ?>
    <script>
        alert('新增成功');
        location.href='home.php?url=product_img';
    </script>
    <?php
}
?>