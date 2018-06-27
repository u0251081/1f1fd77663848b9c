<div class="widget">
    <h4 class="widgettitle">新增輪播廣告圖</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post" enctype="multipart/form-data">
            <p>
                <label>廣告商品</label>
                <span class="field">
                    <select name="p_id">
                        <option value="0">請選擇</option>
                        <?php
                        $sql = "SELECT * FROM product WHERE added='1'";
                        $res = mysql_query($sql);
                        while($row = mysql_fetch_array($res))
                        {
                            ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['p_name']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </span>
            </p>
            <p>
                <label>圖片名稱</label>
                <span class="field"><input type="text" name="banner_title" class="input-large" value="<?php echo @$_POST['banner_title']; ?>" placeholder="請輸入標題"/></span>
            </p>
            <p>
                <label>電腦版 banner</label>
                <span class="field">
                    <input type="file" name="banner_img">
                </span>
            </p>
            <p>
                <label>手機版 banner</label>
                <span class="field">
                    <input type="file" name="mobile_banner_img">
                </span>
            </p>
            <p>
                <label>是否顯示</label>
                <span class="field">
                    <input type="radio" name="status" checked value="1">是&nbsp;&nbsp;<input type="radio" name="status" value="0">否
                </span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="btn btn-primary span1" value="新增">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" class="btn span1" value="清除">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
@$banner_img_tmp = $_FILES['banner_img']['tmp_name']; //商品封面圖暫存
@$banner_img = $_FILES['banner_img']['name']; //商品圖

@$mobile_banner_img_tmp = $_FILES['mobile_banner_img']['tmp_name']; //商品封面圖暫存
@$mobile_banner_img = $_FILES['mobile_banner_img']['name']; //商品圖
@$p_id = $_POST['p_id'];
@$banner_title = $_POST['banner_title'];
$filedir = "images/banner/pc/";//指定上傳資料
$filedir2 = "images/banner/mobile/";//指定上傳資料
@$status = $_POST['status'];

if(isset($_POST['btn']))
{
    if($banner_img != "" && $mobile_banner_img != "")
    {
        $banner_img = date("YmdHis").time().".jpg";
        $mobile_banner_img = date("YmdHis").time().".jpg";
        move_uploaded_file($banner_img_tmp,$filedir.$banner_img);
        move_uploaded_file($mobile_banner_img_tmp,$filedir2.$mobile_banner_img);
        $sql = "INSERT INTO banner SET p_id='$p_id', title='$banner_title', img='".$filedir.$banner_img."', mobile_img='".$filedir2.$mobile_banner_img."', add_time='".date('Y-m-d H:i:s')."', `status`='$status'";
        mysql_query($sql);
        ?>
        <script>
            alert('新增成功');
            location.href='home.php?url=banner';
        </script>
        <?php
    }
    else
    {
        ?>
        <script>
            alert('請完整的新增兩種圖片');
        </script>
        <?php
    }
}
?>