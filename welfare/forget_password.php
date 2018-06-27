<section id="aa-myaccount">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="aa-myaccount-area">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-2">
                            <div class="aa-myaccount-login">
                                <h4>忘記密碼</h4>
                                <form method="post" class="aa-login-form">
                                    <label for="">E-mail(帳號)</label>
                                    <input type="text" name="account" placeholder="E-mail(帳號)">
                                    <label for="">新密碼</label>
                                    <input type="password" name="password" placeholder="新密碼">
                                    <label for="">出生年月日</label>
                                    <input type="text" name="last_id_card" placeholder="請輸入出生年月日ex:19920101">
                                    <input type="submit" name="reg_btn" class="aa-browse-btn" value="提交">
                                    <!--<label class="rememberme" for="rememberme"><input type="checkbox" id="rememberme"> Remember me </label>-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
@$account = $_POST['account'];
@$password = $_POST['password'];
@$last_id_card = $_POST['last_id_card'];
if(isset($_POST['reg_btn']))
{
    $sql = "SELECT * FROM member WHERE email='$account'";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    if($row['id'] != "")
    {
        if($last_id_card == $row['id_card'])
        {
            $sql = "UPDATE member SET password='$password' WHERE id='".$row['id']."'";
            mysql_query($sql);
            ?>
            <script>
                alert('密碼重設成功，請用新密碼重新登入');
                location.href='index.php';
            </script>
            <?php
        }
        else
        {
            ?>
            <script>
                alert('出生年月日錯誤，請重新輸入');
                location.href='index.php?url=forget_password';
            </script>
            <?php
        }
    }
    else
    {
        ?>
        <script>
            alert('查無此帳號，請檢查後重新輸入');
        </script>
        <?php
    }
}
?>
<script>
    $("html,body").scrollTop(700);
</script>
