<?php
/**
 * Created by PhpStorm.
 * User: andychen
 * Date: 8/22/18
 * Time: 9:12 PM
 */
?>
<body class="loginpage">

<div class="loginpanel">
    <div class="loginpanelinner">
        <div class="logo animate0 bounceIn"><p class="mylogo">總管理登入</p></div>
        <form id="login" method="post">

            <div class="inputwrapper animate1 bounceIn">
                <input type="text" name="account" placeholder="帳號"/>
            </div>
            <div class="inputwrapper animate2 bounceIn">
                <input type="password" name="password" placeholder="密碼"/>
            </div>
            <div class="inputwrapper animate3 bounceIn">
                <input type="submit" class="login_btn" value="登入" name="btn" style="width:100%;"/>
            </div>
        </form>
    </div>
</div>

<div class="loginfooter">
    <p><h4><!--版權宣告--></h4></p>
</div>
<script>
    $(document).on('submit','form',function() {
        let inputValue = getFormData($(this));
        ajax17mai('Administrator','Login',{},inputValue);
        return false;
    });
</script>