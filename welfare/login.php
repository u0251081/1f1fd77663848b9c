<section id="aa-myaccount" style="margin-top: -80px;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="aa-myaccount-area">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-2">
                            <div class="aa-myaccount-login">
                                <h4>註冊</h4>
                                <form method="post" id="reg_form" class="aa-login-form">
                                    <label for="">E-mail(帳號)<span>*</span></label>
                                    <input type="text" name="account" placeholder="E-mail(帳號)" >
                                    <span style="position:absolute; top: 150px; color: red; font-size: 14px;" id="email-hint"></span>
                                    <br><br>
                                    <label for="">密碼<span>*</span></label>
                                    <input type="password" name="password" placeholder="密碼">
                                    <input type="button" name="reg_btn" class="aa-browse-btn" value="註冊">&nbsp;&nbsp;&nbsp;&nbsp;
                                   
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
$member_no = "";
for($i=1;$i<=10;$i++)
{
    $num = rand(1,9);
    $member_no .= $num;
}

if(@$_SESSION['front_id'] != "")
{
    ?>
    <script>
        location.href='index.php';
    </script>
    <?php
}

if($account != "")
{
    $sql = "INSERT INTO member SET email='$account', password='$password', member_no='$member_no', `status`='1', `identity`='member', registration_time='".date('Y-m-d H:i:s')."'";
    mysql_query($sql);
    ?>
    <script>
        var account = '<?php echo $_POST['account']; ?>';
        var password = '<?php echo $_POST['password']; ?>';
        $.ajax
        ({
            url:"ajax.php", //接收頁
            type:"POST", //POST傳輸
            data:{type:"login", account:account, password:password}, // key/value
            dataType:"text", //回傳形態
            success:function(i) //成功就....
            {
                if(i == 1)
                {
                    alert('註冊成功');
                    location.href='index.php';
                }
            },
            error:function()//失敗就...
            {
            }
        });
    </script>
    <?php
}
?>
<script>
    $("html,body").scrollTop(700);
    $(function ()
    {
        $("#email-hint").hide();
    });

    $("input[name='reg_btn']").click(function()
    {
        var email = $("input[name='account']").val();
        var email_reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/;
        if(email != "")
        {
            if(!email_reg.test(email))
            {
                $("#email-hint").hide();
                $("#email-hint").text('請填正確的email格式').slideDown(1000).show();
            }
            else
            {
                $("form#reg_form").submit();
            }
        }
        else
        {
            $("#email-hint").hide();
            $("#email-hint").text('email必填').slideDown(1000).show();
        }
    });

    $("input[name='account']").blur(function()
    {
        var user_input = $(this).val();
        if(user_input)
        {
            $.ajax
            ({
                url:"ajax.php", //接收頁
                type:"POST", //POST傳輸
                data:{type:"check_is_reg", account:user_input}, // key/value
                dataType:"text", //回傳形態
                success:function(i) //成功就....
                {
                    if(i == 1)
                    {
                        $("input[name='account']").val('');
                        $("#email-hint").hide();
                        $("#email-hint").text('此帳號已有人註冊過').slideDown(1000).show();
                    }
                    else
                    {
                        $("#email-hint").hide();
                    }
                },
                error:function()//失敗就...
                {
                }
            });
        }
    })
</script>
