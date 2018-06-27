<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<?php
@$id = $_GET['id'];
$sql = "SELECT * FROM supplier WHERE id='$id'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
?>
<div class="widget">
    <h4 class="widgettitle">供應商</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post" enctype="multipart/form-data">
            <p>
                <label>標題</label>
                <span class="field"><input type="text" name="supplier_title" class="input-large" value="<?php echo $row['supplier_title']; ?>" placeholder="請輸入供應商標題"/></span>
            </p>
            <p>
                <label>供應商</label>
                <span class="field"><input type="text" name="supplier_name" class="input-large" value="<?php echo $row['supplier_name']; ?>" placeholder="請輸入供應商名稱"/></span>
            </p>
            <p>
                <label>負責人</label>
                <span class="field"><input type="text" name="ceo" class="input-large" value="<?php echo $row['ceo']; ?>" placeholder="請輸入負責人名稱"/></span>
            </p>
            <p>
                <label>連絡電話1</label>
                <span class="field"><input type="text" name="tele" class="input-large" value="<?php echo $row['tele']; ?>" placeholder="請輸入公司電話"/></span>
            </p>
            <p>
                <label>連絡電話2</label>
                <span class="field"><input type="text" name="cellphone" class="input-large" value="<?php echo $row['cellphone']; ?>" placeholder="請輸入公司聯絡人手機"/></span>
            </p>
            <p>
                <label>短介</label>
                <span class="field">
                    <textarea name="info" cols="50" rows="5" class="span4" maxlength="24" placeholder="輸入供應商簡介"><?php echo $row['info']; ?></textarea>
                </span>
            </p>
            <p>
                <label>大圖</label>
                <span class="field">
                    <input type="file" name="big_img">
                    <input type="hidden" name="big_img_old" value="<?php echo $row['big_img']; ?>">
                </span>
                <label>大圖目前圖片</label>
                <span class="field">
                    <img src="<?php echo $row['big_img']; ?>" width="150" height="150">
                </span>
            </p>
            <p>
                <label>小圖</label>
                <span class="field">
                    <input type="file" name="small_img">
                    <input type="hidden" name="small_img_old" value="<?php echo $row['small_img']; ?>">
                </span>
                <label>小圖目前圖片</label>
                <span class="field">
                    <img src="<?php echo $row['small_img']; ?>" width="150" height="150">
                </span>
            </p>
            <p>
                <label>詳細介紹</label>
                <span class="field">
                    <textarea name="info2" id="textbox" cols="50" rows="5" class="span4" placeholder="輸入詳細介紹"><?php echo $row['info2']; ?></textarea>
                </span>
            </p>
            <p>
                <label>地址</label>
                <span class="field">
                    <select name="city_id" id="city_id"><!--左邊加上=>style="width: 100px;即可變小"-->
                        <option value="">請選擇市</option>
                        <?php
                        $city_sql = "SELECT * FROM city";
                        $city_res = mysql_query($city_sql);
                        while ($city_row = mysql_fetch_array($city_res))
                        {
                            if($row['city_id'] == $city_row['id'])
                            {
                                echo "<option value=".$city_row['id']." selected>".$city_row['city']."</option>";
                            }
                            else
                            {
                                echo "<option value=".$city_row['id'].">".$city_row['city']."</option>";
                            }
                        }
                        ?>
                    </select>
                    <select name='area_id' id="area_id">
                        <option value="">請選擇區</option>
                        <?php
                        $area_sql = "SELECT * FROM area";
                        $area_res = mysql_query($area_sql);
                        while ($area_row = mysql_fetch_array($area_res))
                        {
                            if($row['area_id'] == $area_row['id'])
                            {
                                echo "<option value=".$area_row['id']." selected>".$area_row['area']."</option>";
                            }
                            else
                            {
                                echo "<option value=".$area_row['id'].">".$area_row['area']."</option>";
                            }
                        }
                        ?>
                    </select><br>
                    <input type="text" name="address" placeholder="請輸入地址" value="<?php echo $row['address']; ?>" class="input-xxlarge"/>
                </span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="btn btn-primary span1" value="修改">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" class="btn btn-default span1" value="返回" onclick="location.href='home.php?url=supplier'">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
@$supplier_title = $_POST['supplier_title'];
@$supplier_name = $_POST['supplier_name'];
@$ceo = $_POST['ceo'];
@$tele = $_POST['tele'];
@$cellphone = $_POST['cellphone'];
@$info = $_POST['info'];
@$info2 = $_POST['info2'];
@$city_id = $_POST['city_id'];
@$area_id = $_POST['area_id'];
@$address = $_POST['address'];

@$big_img_tmp = $_FILES['big_img']['tmp_name']; //大圖暫存
@$big_img = $_FILES['big_img']['name']; //大圖
@$big_img_old = $_POST['big_img_old'];
$filedir = "images/supplier_img/big/";//指定大圖上傳路徑

@$small_img_tmp = $_FILES['small_img']['tmp_name']; //小圖暫存
@$small_img = $_FILES['small_img']['name']; //小圖
@$small_img_old = $_POST['small_img_old'];
$filedir2 = "images/supplier_img/small/";//指定小圖上傳路徑

if(isset($_POST['btn']))
{
    if($big_img != "")
    {
        unlink($big_img_old);
        $big_img = date("YmdHis").time().".jpg";
        move_uploaded_file($big_img_tmp,$filedir.$big_img);
        $sql = "UPDATE supplier SET big_img='".$filedir.$big_img."' WHERE id='$id'";
        mysql_query($sql);
    }

    if($small_img != "")
    {
        unlink($small_img_old);
        $small_img = date("YmdHis").time().".jpg";
        move_uploaded_file($small_img_tmp,$filedir2.$small_img);
        $sql = "UPDATE supplier SET small_img='".$filedir2.$small_img."' WHERE id='$id'";
        mysql_query($sql);
    }

    $sql = "UPDATE supplier SET supplier_title='$supplier_title', supplier_name='$supplier_name', ceo='$ceo', tele='$tele', 
cellphone='$cellphone', info='$info', info2='$info2', city_id='$city_id', area_id='$area_id', address='$address' WHERE id='$id'";
    mysql_query($sql);
    ?>
    <script>
        alert('修改成功');
        location.href='home.php?url=supplier';
    </script>
    <?php
}
?>

<script type="text/javascript">
    $(function ()
    {
        CKEDITOR.replace('textbox');
    });

    $("#city_id").change(function()
    {
        $("#area_id").find("option").not(":first").remove();

        var id = $(this).val();
        $.ajax
        ({
            url:"sever_ajax.php", //接收頁
            type:"POST", //POST傳輸
            data:{type:'get_area', city_id:id}, // key/value
            dataType:"text", //回傳形態
            success:function(i) //成功就....
            {
                $("#area_id").append(i);
            },
            error:function()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    });
</script>