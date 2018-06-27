<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<div class="widget">
    <h4 class="widgettitle">新增供應商資料</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post" enctype="multipart/form-data">
            <p>
                <label>標題</label>
                <span class="field"><input type="text" name="supplier_title" class="input-large" placeholder="請輸入供應商標題"/></span>
            </p>
            <p>
                <label>供應商</label>
                <span class="field"><input type="text" name="supplier_name" class="input-large" placeholder="請輸入供應商名稱"/></span>
            </p>
            <p>
                <label>負責人</label>
                <span class="field"><input type="text" name="ceo" class="input-large" placeholder="請輸入負責人名稱"/></span>
            </p>
            <p>
                <label>連絡電話1</label>
                <span class="field"><input type="text" name="tele" class="input-large" placeholder="請輸入公司電話"/></span>
            </p>
            <p>
                <label>連絡電話2</label>
                <span class="field"><input type="text" name="cellphone" class="input-large" placeholder="請輸入公司聯絡人手機"/></span>
            </p>
            <p>
                <label>短介</label>
                <span class="field">
                    <textarea name="info" cols="50" rows="5" class="span4" maxlength="24" placeholder="輸入供應商短介"></textarea>
                </span>
            </p>
            <p>
                <label>大圖</label>
                <span class="field">
                    <input type="file" name="big_img">
                </span>
            </p>
            <p>
                <label>小圖</label>
                <span class="field">
                    <input type="file" name="small_img">
                </span>
            </p>
            <p>
                <label>詳細介紹</label>
                <span class="field">
                    <textarea name="info2" id="textbox" cols="50" rows="5" class="span4" placeholder="輸入詳細介紹"></textarea>
                </span>
            </p>
            <p>
                <label>地址</label>
                <span class="field">
                    <select name="city_id" id="city_id" class="uniformselect">
                        <option value="">請選擇市</option>
                        <?php
                        $sql = "SELECT * FROM city";
                        $res = mysql_query($sql);
                        while ($row = mysql_fetch_array($res))
                        {
                            echo "<option value=".$row['id'].">".$row['city']."</option>";
                        }
                        ?>
                    </select>
                    <select name='area_id' id="area_id" class="uniformselect">
                        <option value="">請選擇區</option>
                    </select><br>
                    <input type="text" name="address" placeholder="請輸入地址" class="input-xxlarge"/>
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
$filedir = "images/supplier_img/big/";//指定大圖上傳路徑

@$small_img_tmp = $_FILES['small_img']['tmp_name']; //小圖暫存
@$small_img = $_FILES['small_img']['name']; //小圖
$filedir2 = "images/supplier_img/small/";//指定小圖上傳路徑

if(isset($_POST['btn']))
{
    $big_img = date("YmdHis").time().".jpg";
    $small_img = date("YmdHis").time().".jpg";
    move_uploaded_file($big_img_tmp,$filedir.$big_img);
    move_uploaded_file($small_img_tmp,$filedir2.$small_img);
    $sql = "INSERT INTO supplier SET supplier_title='$supplier_title', supplier_name='$supplier_name', ceo='$ceo', tele='$tele', cellphone='$cellphone', info='$info', 
 city_id='$city_id', area_id='$area_id', address='$address', info2='$info2', big_img='".$filedir.$big_img."', small_img='".$filedir2.$small_img."', registration_time='".date('Y-m-d H:i:s')."'";
    mysql_query($sql);
    ?>
    <script>
        alert('新增成功');
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
                alert("ajax失敗");
            }
        });
    });
</script>