    <?php
    @$id = $_GET['id'];
    $sql = "SELECT * FROM banner WHERE id='$id'";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    ?>
    <div class="widget">
        <h4 class="widgettitle">修改輪播廣告圖</h4>
        <div class="widgetcontent">
            <form class="stdform stdform2" method="post" enctype="multipart/form-data">
                <p>
                    <label>廣告商品</label>
                    <span class="field">
                        <select name="p_id">
                            <option value="0">請選擇</option>
                            <?php
                            $product_sql = "SELECT * FROM product WHERE added='1'";
                            $product_res = mysql_query($product_sql);
                            while($product_row = mysql_fetch_array($product_res))
                            {
                                if($row['p_id'] == $product_row['id'])
                                {
                                    ?>
                                    <option value="<?php echo $product_row['id']; ?>" selected><?php echo $product_row['p_name']; ?></option>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <option value="<?php echo $product_row['id']; ?>"><?php echo $product_row['p_name']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </span>
                </p>

                <p>
                    <label>圖片名稱</label>
                    <span class="field"><input type="text" name="banner_title" class="input-large" value="<?php echo $row['title']; ?>" placeholder="請輸入標題"/></span>
                </p>
                 <p>
                    <label>封面連結url</label>
                    <span class="field"><input type="text" name="banner_url" class="input-large" value="<?php echo $row['url']; ?>" placeholder="請輸入url"/></span>
                </p>
                <p>
                    <label>電腦版封面圖</label>
                    <span class="field">
                        <input type="file" name="banner_img"><br><br>
                        <input type="hidden" name="banner_img_old" value="<?php echo $row['img']; ?>">
                    </span>
                    <label>電腦版目前圖片</label>
                    <span class="field">
                        <img src="<?php echo $row['img']; ?>" width="150" height="150">
                    </span>
                </p>
                <p>
                    <label>手機版封面圖</label>
                    <span class="field">
                        <input type="file" name="mobile_banner_img"><br><br>
                        <input type="hidden" name="mobile_banner_img_old" value="<?php echo $row['mobile_img']; ?>">
                    </span>
                    <label>手機版目前圖片</label>
                    <span class="field">
                        <img src="<?php echo $row['mobile_img']; ?>" width="150" height="150">
                    </span>
                </p>
                <p>
                    <label>是否顯示</label>
                    <span class="field">
                        <input type="radio" name="status" <?php if($row['status'] == 1){echo'checked'; }?> value="1">是&nbsp;&nbsp;
                        <input type="radio" name="status" <?php if($row['status'] == 0){echo'checked'; }?> value="0">否
                    </span>
                </p>
                <p class="stdformbutton">
                    <input type="submit" name="btn" class="btn btn-primary span1" value="修改">&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" class="btn btn-default span1" value="返回" onclick="location.href='home.php?url=banner'">
                </p>
            </form>
        </div><!--widgetcontent-->
    </div><!--widget-->

    <?php
    @$p_id = $_POST['p_id'];
    @$banner_title = $_POST['banner_title'];
    @$banner_img_tmp = $_FILES['banner_img']['tmp_name']; //輪播圖暫存
    @$banner_img = $_FILES['banner_img']['name']; //輪播圖
    @$banner_img_old = $_POST['banner_img_old'];
    @$banner_url = $_POST['banner_url'];
    @$mobile_banner_img_tmp = $_FILES['mobile_banner_img']['tmp_name']; //商品封面圖暫存
    @$mobile_banner_img = $_FILES['mobile_banner_img']['name']; //商品圖
    @$mobile_banner_img_old = $_POST['mobile_banner_img_old'];

    @$status = $_POST['status'];

    $filedir = "images/banner/pc/";//指定上傳資料
    $filedir2 = "images/banner/mobile/";//指定上傳資料

    if(isset($_POST['btn']))
    {
        if($banner_img != "" || $mobile_banner_img != "")
        {
            if($banner_img != "")
            {
                unlink($banner_img_old);
                $banner_img = date("YmdHis").time().".jpg";
                move_uploaded_file($banner_img_tmp,$filedir.$banner_img);
                $sql = "UPDATE banner SET p_id='$p_id', title='$banner_title',url='$banner_url',img='".$filedir.$banner_img."', `status`='$status' WHERE id='$id'";
                mysql_query($sql);
            }

            if($mobile_banner_img != "")
            {
                unlink($mobile_banner_img_old);
                $mobile_banner_img = date("YmdHis").time().".jpg";
                move_uploaded_file($mobile_banner_img_tmp,$filedir2.$mobile_banner_img);
                $sql = "UPDATE banner SET p_id='$p_id', title='$banner_title',url='$banner_url', mobile_img='".$filedir2.$mobile_banner_img."', `status`='$status' WHERE id='$id'";
                mysql_query($sql);
            }
        }
        else
        {
            $banner_img = $banner_img_old;
            $mobile_banner_img = $mobile_banner_img_old;
            $sql = "UPDATE banner SET p_id='$p_id', title='$banner_title',url='$banner_url', img='".$banner_img."', mobile_img='".$mobile_banner_img."', `status`='$status' WHERE id='$id'";
            mysql_query($sql);
        }
        ?>
        <script>
            alert('修改成功');
            location.href='home.php?url=banner';
        </script>
        <?php
    }
    ?>