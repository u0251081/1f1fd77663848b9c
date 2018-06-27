<?php
@$id = $_GET['id'];
$sql = "SELECT * FROM fb WHERE id='$id'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
?>
<div class="widget">
    <h4 class="widgettitle">修改會員資料</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post">
            <p>
                <label>會員名稱</label>
                <span class="field"><input type="text" name="m_name" class="input-large" value="<?php echo $row['fb_name']; ?>" placeholder="請輸入會員名稱"/></span>
            </p>
            <p>
                <label>電子信箱(帳號)</label>
                <span class="field"><input type="text" name="email" class="input-large" disabled value="<?php echo $row['fb_email']; ?>" placeholder="請輸入電子信箱(帳號)"/></span>
            </p>
            <p>
                <label>身分證字號</label>
                <span class="field"><input type="text" name="id_card" class="input-large" value="<?php echo $row['id_card']; ?>" placeholder="請輸入身分證字號"/></span>
            </p>
            <p>
                <label>生日</label>
                <span class="field"><input type="date" name="birthday" class="input-large" value="<?php echo $row['birthday']; ?>" /></span>
            </p>
            <p>
                <label>性別</label>
                <span class="field">
                    <select name="sex" id="sex" class="uniformselect">
                        <option value="">請選擇性別</option>
                        <option <?php if($row['sex'] == 1){echo 'selected';} ?> value="1">男</option>
                        <option <?php if($row['sex'] == 0){echo 'selected';} ?> value="0">女</option>
                    </select>
                </span>
            </p>
            <p>
                <label>行動電話</label>
                <span class="field"><input type="text" name="cellphone" class="input-large" value="<?php echo $row['cellphone']; ?>" placeholder="請輸入行動電話"/></span>
            </p>
<!--            <p>-->
<!--                <label>LINE_ID</label>-->
<!--                <span class="field"><input type="text" name="line" class="input-large" value="--><?php //echo $row['line']; ?><!--" placeholder="請輸入LINE_ID"/></span>-->
<!--            </p>-->
            <p>
                <label>紅利點數</label>
                <span class="field"><input type="text" name="bonus" class="input-large" value="<?php echo $row['bonus'] == "" ? '0' : $row['bonus']; ?>" placeholder="請輸入紅利點數"/></span>
            </p>
            <p>
                <label>獲得分潤</label>
                <span class="field"><input type="text" name="profit" class="input-large" value="<?php echo $row['profit'] == "" ? '0' : $row['profit']; ?>" placeholder="獲得的行銷利潤"/></span>
            </p>
            <p>
                <label>地址</label>
                <span class="field">
                    <select name="city_id" id="city_id" class="uniformselect">
                        <option value="">請選擇市</option>
                        <?php
                        $city_sql = "SELECT * FROM city";
                        $city_res = mysql_query($city_sql);
                        while ($city_row = mysql_fetch_array($city_res))
                        {
                            if($row['city_id'] == $city_row['id'])
                            {
                                echo "<option selected value=".$city_row['id'].">".$city_row['city']."</option>";
                            }
                            else
                            {
                                echo "<option value=".$city_row['id'].">".$city_row['city']."</option>";
                            }
                        }
                        ?>
                    </select>
                    <select name='area_id' id="area_id" class="uniformselect">
                        <option value="">請選擇區</option>
                        <?php
                        $area_sql = "SELECT * FROM area";
                        $area_res = mysql_query($area_sql);
                        while ($area_row = mysql_fetch_array($area_res))
                        {
                            if($row['area_id'] == $area_row['id'])
                            {
                                echo "<option selected value=".$area_row['id'].">".$area_row['area']."</option>";
                            }
                            else
                            {
                                echo "<option value=".$area_row['id'].">".$area_row['area']."</option>";
                            }
                        }
                        ?>
                    </select><br>
                    <input type="text" name="address" value="<?php echo $row['address']; ?>" placeholder="請輸入地址" class="input-xxlarge"/>
                </span>
            </p>
<!--            <p>-->
<!--                <label>帳戶狀態</label>-->
<!--                <span class="field">-->
<!--                    <input type="radio" name="status" --><?php //if($row['status'] == 1){echo 'checked';} ?><!-- value="1">啟用&nbsp;&nbsp;-->
<!--                    <input type="radio" name="status" --><?php //if($row['status'] == 0){echo 'checked';} ?><!-- value="0">停用-->
<!--                </span>-->
<!--            </p>-->
            <p class="stdformbutton">
                <input type="submit" name="btn" class="btn btn-primary span1" value="修改">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" class="btn btn-default span1" value="返回" onclick="location.href='home.php?url=fb_member'">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
@$m_name = $_POST['m_name'];
@$id_card = $_POST['id_card'];
@$birthday = $_POST['birthday'];
@$sex = $_POST['sex'];
@$cellphone = $_POST['cellphone'];
@$bonus = $_POST['bonus'];
@$profit = $_POST['profit'];
@$city_id = $_POST['city_id'];
@$area_id = $_POST['area_id'];
@$address = $_POST['address'];
//@$status = $_POST['status'];

if(isset($_POST['btn']))
{
    $sql = "UPDATE fb SET fb_name='$m_name', id_card='$id_card', birthday='$birthday', 
 sex='$sex', cellphone='$cellphone', profit='$profit', bonus='$bonus', city_id='$city_id', area_id='$area_id', address='$address' WHERE id='$id'";
    mysql_query($sql);
    ?>
    <script>
        alert('修改成功');
        location.href='home.php?url=fb_member';
    </script>
    <?php
}
?>

<script type="text/javascript">
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