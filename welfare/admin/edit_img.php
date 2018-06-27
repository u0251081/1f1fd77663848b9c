<?php
@$id = $_GET['id'];
$sql = "SELECT p_name FROM product WHERE id='$id'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);

$img_ary = array();
$img_id_ary = array();
$sql2 = "SELECT * FROM product_img WHERE p_id='".$id."'";
$res2 = mysql_query($sql2);
while($row2 = mysql_fetch_array($res2))
{
    $img_id_ary[] = $row2['id'];
    $img_ary[] = $row2['picture'];
}
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
                    <input type="file" name="is_main"><br><br>
                    <input type="hidden" name="is_main_old" value="<?php echo $img_ary[0]; ?>">
                </span>
                <label>目前圖片</label>
                <span class="field">
                    <img src="<?php echo $img_ary[0]; ?>" width="150" height="150">
                </span>
            </p>
            <p>
                <label>圖1</label>
                <span class="field">
                    <input type="file" name="picture[]">
                    <input type="hidden" name="picture1" value="<?php echo $img_id_ary[1]; ?>">
                </span>
                <label>目前圖片</label>
                <span class="field">
                    <img src="<?php echo $img_ary[1]; ?>" width="150" height="150">
                </span>
            </p>
            <p>
                <label>圖2</label>
                <span class="field">
                    <input type="file" name="picture[]">
                    <input type="hidden" name="picture2" value="<?php echo $img_id_ary[2]; ?>">
                </span>
                <label>目前圖片</label>
                <span class="field">
                    <img src="<?php echo $img_ary[2]; ?>" width="150" height="150">
                </span>
            </p>
            <p>
                <label>圖3</label>
                <span class="field">
                    <input type="file" name="picture[]">
                    <input type="hidden" name="picture3" value="<?php echo $img_id_ary[3]; ?>">
                </span>
                <label>目前圖片</label>
                <span class="field">
                    <img src="<?php echo $img_ary[3]; ?>" width="150" height="150">
                </span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="btn btn-primary span1" value="修改">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" class="btn btn-default span1" value="返回" onclick="location.href='home.php?url=product_img'">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
@$is_main_tmp = $_FILES['is_main']['tmp_name']; //商品封面圖暫存
@$is_main = $_FILES['is_main']['name']; //商品圖
@$is_main_old = $_POST['is_main_old'];

@$picture_tmp = $_FILES['picture']['tmp_name']; //圖片暫存
@$picture = $_FILES['picture']['name']; //圖片
@$picture1 = $_POST['picture1'];
@$picture2 = $_POST['picture2'];
@$picture3 = $_POST['picture3'];

$filedir = "images/product/";//指定上傳資料

if(isset($_POST['btn']))
{
    if($is_main != "")
    {
        unlink($is_main_old);
        $is_main = date("YmdHis").date("s").".jpg";
        move_uploaded_file($is_main_tmp,$filedir.$is_main);
        $sql = "UPDATE product_img SET picture='".$filedir.$is_main."' WHERE p_id='$id' AND is_main='1'";
        mysql_query($sql);
    }

    if($picture != "")
    {
        if($picture[0] != "")
        {
            unlink($img_ary[1]);
            $picture[0] = date("YmdHis")."0.jpg";
            move_uploaded_file($picture_tmp[0],$filedir.$picture[0]);
            $sql = "UPDATE product_img SET picture='".$filedir.$picture[0]."' WHERE p_id='$id' AND id='$picture1'";
            mysql_query($sql);
        }

        if($picture[1] != "")
        {
            unlink($img_ary[2]);
            $picture[1] = date("YmdHis")."1.jpg";
            move_uploaded_file($picture_tmp[1],$filedir.$picture[1]);
            $sql = "UPDATE product_img SET picture='".$filedir.$picture[1]."' WHERE p_id='$id' AND id='$picture2'";
            mysql_query($sql);
        }

        if($picture[2] != "")
        {
            unlink($img_ary[3]);
            $picture[2] = date("YmdHis")."2.jpg";
            move_uploaded_file($picture_tmp[2],$filedir.$picture[2]);
            $sql = "UPDATE product_img SET picture='".$filedir.$picture[2]."' WHERE p_id='$id' AND id='$picture3'";
            mysql_query($sql);
        }
    }
    ?>
    <script>
        alert('修改成功');
        location.href='home.php?url=product_img';
    </script>
    <?php
}
?>