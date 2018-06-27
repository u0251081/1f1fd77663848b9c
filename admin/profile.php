<form class="editprofileform" method="post" enctype="multipart/form-data">
    <div class="row-fluid">
        <div class="span8">
            <div class="widgetbox">
                <h4 class="widgettitle">登入資料</h4>
                <div class="widgetcontent" id="login">
                    <?php
                    if($_SESSION['identity'] == 'admin')
                    {
                        $sql = "SELECT * FROM admin WHERE id='".$_SESSION['id']."'";
                        $res = mysql_query($sql);
                        $row = mysql_fetch_array($res);
                    }
                    ?>
                    <label>帳號</label>
                    <input type='text' name='account' class='input-xlarge' value='<?php echo $row['account']; ?>' disabled /><br />
                    <label>密碼</label>
                    <input type='password' name='password' class='input-xlarge' value='<?php echo $row['password']; ?>' />
                    <input type="checkbox" id="show"><font color="red" size="-5">查看密碼</font>
                </div>
            </div>

            <div class="widgetbox">
                <h4 class="widgettitle">個人資料</h4>
                <div class="widgetcontent" id="login">
                    <label>姓名</label>
                    <input type='text' name='name' class='input-xlarge' value='<?php echo $row['name']; ?>' /><br />
                    <label>身份</label>
                    <?php
                    if($_SESSION['identity'] == 'admin')
                    {
                        ?>
                        <span id='admin' style='font-size:22px; padding:10px; height:15px;' class='label label-success'>管理員</span>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <!--span8-->
        <div class="span4 profile-right">
            <div class="widgetbox profile-photo">
                <div class="headtitle">
                    <div class="btn-group">
                        <button data-toggle="dropdown" class="btn dropdown-toggle">設定<span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:void(0)" id="edit_pic">更新</a></li>
                        </ul>
                    </div>
                    <h4 class="widgettitle">個人頭像設置</h4>
                </div>
                <div class="widgetcontent">
                    <div class="profilethumb">
                        <img id="profile_img" src="<?php echo $row['img']; ?>" width="200" height="200"/>
                    </div>
                </div>
            </div>
            <input type="file" style="display: none;" name="upload" id="fileinput"/>
        </div>
        <!--span4-->
    </div>
    <p>
       <input type="submit" class="btn btn-primary" name="btn" value="修改" />
    </p>
    <input type="hidden" name="old_img" value="<?php echo $row['img']; ?>">
</form>

<?php
@$logo_tmp = $_FILES['upload']['tmp_name'];
@$logo = $_FILES['upload']['name'];
@$filedir="images/photos/".$logo;//指定上傳資料
@$old_img = $_POST['old_img'];
@$password = $_POST['password'];
@$name = $_POST['name'];
if(isset($password) || isset($name))
{
    if($logo != "")
    {
        unlink($old_img);
        $sql = "UPDATE admin SET password='$password', `name`='$name', img='$filedir' WHERE id='".$_SESSION['id']."'";
        mysql_query($sql);
        move_uploaded_file($logo_tmp,$filedir);
    }
    else
    {
        $sql = "UPDATE admin SET password='$password', `name`='$name' WHERE id='".$_SESSION['id']."'";
        mysql_query($sql);
    }
    ?>
    <script>
        alert('修改成功');
        location.href='home.php';
    </script>
    <?php
}
?>

<script>
    $("#edit_pic").click(function ()
    {
        $('#fileinput').click();
    });

    //----------------顯示密碼------------------//
    var password = $("input[name='password']");
    var showpassword = $('#show');
    var inputpassword = $('<input type="text" name="password" class="input-xlarge" />');
    showpassword.click(function(){
        if(this.checked)
        {
            password.replaceWith(inputpassword.val(password.val()));
        }
        else
        {
            inputpassword.replaceWith(password.val(inputpassword.val()));
        }
    });
</script>
