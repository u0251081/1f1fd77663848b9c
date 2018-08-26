<?php

use Base17Mai\Product;
use function Base17Mai\take;

$Product = new Product(take('id'));
$Product->setEditorData();
$PName = $Product->getPName();
$image = $Product->getImage();
$imageC = $image['Cover'];
$image1 = isset($image[0]) ? $image[0] : '';
$image2 = isset($image[1]) ? $image[1] : '';
$image3 = isset($image[2]) ? $image[2] : '';
?>
    <div class="widget">
        <h4 class="widgettitle">供應商</h4>
        <div class="widgetcontent">
            <form class="stdform stdform2" method="post" enctype="multipart/form-data">
                <p>
                    <label>商品名稱</label>
                    <span class="field">
                        <?= $PName ?>
                    </span>
                </p>
                <p>
                    <label>封面圖</label>
                    <span class="field">
                        <input type="file" name="Cover">
                    </span>
                    <label>目前圖片</label>
                    <span class="field">
                    <img src="<?= $imageC ?>" width="150" height="150">
                </span>
                </p>
                <p>
                    <label>圖1</label>
                    <span class="field">
                        <input type="file" name="picture1">
                    </span>
                    <label>目前圖片</label>
                    <span class="field">
                        <img src="<?= $image1 ?>" width="150" height="150">
                    </span>
                </p>
                <p>
                    <label>圖2</label>
                    <span class="field">
                        <input type="file" name="picture2">
                    </span>
                    <label>目前圖片</label>
                    <span class="field">
                        <img src="<?= $image2 ?>" width="150" height="150">
                    </span>
                </p>
                <p>
                    <label>圖3</label>
                    <span class="field">
                        <input type="file" name="picture3">
                    </span>
                    <label>目前圖片</label>
                    <span class="field">
                        <img src="<?= $image3 ?>" width="150" height="150">
                    </span>
                </p>
                <p class="stdformbutton">
                    <input type="submit" name="btn" class="btn btn-primary span1" value="修改">&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="reset" class="btn btn-default span1" value="返回"
                           onclick="location.href='home.php?url=product_img'">
                </p>
            </form>
        </div><!--widgetcontent-->
    </div><!--widget-->
    <script>
        $(document).on('submit', 'form', function () {
            ajax17maiFile('Product', 'UpdateImage', {}, {productID: '<?= take('id') ?>'}, this);
            return false;
        });
    </script>
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

if (isset($_POST['btn'])) {
    if ($is_main != "") {
        unlink($is_main_old);
        $is_main = date("YmdHis") . date("s") . ".jpg";
        move_uploaded_file($is_main_tmp, $filedir . $is_main);
        $sql = "UPDATE product_img SET picture='" . $filedir . $is_main . "' WHERE p_id='$id' AND is_main='1'";
        mysql_query($sql);
    }

    if ($picture != "") {
        if ($picture[0] != "") {
            unlink($img_ary[1]);
            $picture[0] = date("YmdHis") . "0.jpg";
            move_uploaded_file($picture_tmp[0], $filedir . $picture[0]);
            $sql = "UPDATE product_img SET picture='" . $filedir . $picture[0] . "' WHERE p_id='$id' AND id='$picture1'";
            mysql_query($sql);
        }

        if ($picture[1] != "") {
            unlink($img_ary[2]);
            $picture[1] = date("YmdHis") . "1.jpg";
            move_uploaded_file($picture_tmp[1], $filedir . $picture[1]);
            $sql = "UPDATE product_img SET picture='" . $filedir . $picture[1] . "' WHERE p_id='$id' AND id='$picture2'";
            mysql_query($sql);
        }

        if ($picture[2] != "") {
            unlink($img_ary[3]);
            $picture[2] = date("YmdHis") . "2.jpg";
            move_uploaded_file($picture_tmp[2], $filedir . $picture[2]);
            $sql = "UPDATE product_img SET picture='" . $filedir . $picture[2] . "' WHERE p_id='$id' AND id='$picture3'";
            mysql_query($sql);
        }
    }
    ?>
    <script>
        alert('修改成功');
        location.href = 'home.php?url=product_img';
    </script>
    <?php
}
?>