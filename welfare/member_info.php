<style>
    @media (max-width: 768px)
    {
        #btn_panel
        {
            margin-top: -25px;
            margin-bottom: 35px;
        }
    }
</style>

<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li><a href="index.php?url=member_center">會員專區</a></li>
                <li class="active">個人資料</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->

<?php
$member_id = $_SESSION['front_id'];
if(@$_SESSION["member_no"] != "")
{
    $sql = "SELECT * FROM member WHERE id='$member_id'";
}
else if($_SESSION["fb_id"] != "")
{
    $sql = "SELECT * FROM fb WHERE fb_id='".$_SESSION["fb_id"]."'";
}

$res = mysql_query($sql);
$row = mysql_fetch_array($res);
?>

<div style="margin-top: 5%;">
    <div class="container">
        <h1 align="center">個人資料</h1>
        <hr>
        <div class="row">
            <!-- edit form column -->
            <div class="col-md-12 personal-info">
                <form class="form-horizontal" onSubmit="return form_stop()" role="form" method="post">
                    <div class="form-group">
                        <label class="col-lg-3 control-label">E-mail(帳號)</label>
                        <div class="col-lg-8">
                            <input class="form-control" name="email" type="text" value="<?php echo @$_SESSION["member_no"] != "" ? $row['email'] : $row['fb_email']; ?>" disabled>
                        </div>
                    </div>
                    <?php
                    if(@$_SESSION["member_no"] != "" && @$_SESSION["device"] != 'mobile' && $row['fb_id']=="")
                    {
                        ?>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><span style="color:red;">*</span>密碼</label>
                            <div class="col-lg-8">
                                <input class="form-control" name="password" type="password" value="<?php echo $row['password']; ?>">
                                <span style="display:none; color: red; font-size: 14px;" id="password-hint">密碼為必填</span>
                            </div>
                        </div>
                        <?php
                    }
                   if(@$_SESSION["member_no"] != "" && @$_SESSION["device"] != 'mobile' && $row['fb_id']!="")
                   {    
                        ?>
                        <div class="form-group">
                            <label class="col-lg-3 control-label" style="display: none;"><span style="color:red; display:none;">*</span>密碼</label>
                            <div class="col-lg-8">
                                <input class="form-control" type='hidden' name="password" type="password" value="<?php echo $row['password']; ?>">
                                <span style="display:none; color: red; font-size: 14px;" id="password-hint">密碼為必填</span>
                            </div>
                        </div>
                        <?php
                   }

                    ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><span style="color:red;">*</span>會員姓名</label>
                        <div class="col-lg-8">
                            <input class="form-control" name="m_name" type="text" <?php if(@$_SESSION["member_no"] == ""){echo 'disabled';} ?> value="<?php echo @$_SESSION["member_no"] != "" ? $row['m_name'] : $row['fb_name']; ?>">
                            <span style="display: none; color: red; font-size: 14px;" id="name-hint">姓名為必填</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><span style="color:red;">*</span>出生年月日</label>
                        <div class="col-lg-8">
                            <input class="form-control" name="id_card" type="text" value="<?php echo $row['id_card']; ?>" placeholder="ex：19920101">
                            <span style="display: none; color: red; font-size: 14px;" id="id_card-hint">出生年月日為必填</span>
                        </div>
                    </div>
<!--                    <div class="form-group">-->
<!--                        <label class="col-lg-3 control-label">生日</label>-->
<!--                        <div class="col-lg-8">-->
<!--                            <input class="form-control" name="birthday" type="date" value="--><?php //echo $row['birthday']; ?><!--">-->
<!--                        </div>-->
<!--                    </div>-->
                    <div class="form-group">
                        <label class="col-lg-3 control-label">性別</label>
                        <div class="col-lg-8">
                            <div class="ui-select">
                                <select name="sex" class="form-control">
                                    <option <?php if($row['sex'] == 1){echo 'selected';} ?> value="1">男</option>
                                    <option <?php if($row['sex'] == 0){echo 'selected';} ?> value="0">女</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">市 / 區</label>

                        <div class="col-lg-4">
                            <div class="ui-select">
                                <select name="city" id="city_id" class="form-control">
                                    <option value="">請選擇市</option>
                                    <?php
                                    $city_sql = "SELECT * FROM city";
                                    $city_res = mysql_query($city_sql);
                                    while ($city_row = mysql_fetch_array($city_res))
                                    {
                                        if($row['city_id'] == $city_row['id'])
                                        {
                                            ?>
                                            <option selected value="<?php echo $city_row['id']; ?>"><?php echo $city_row['city']; ?></option>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <option value="<?php echo $city_row['id']; ?>"><?php echo $city_row['city']; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>&nbsp;&nbsp;
                        <div class="col-lg-4">
                            <div class="ui-select">
                                <select name="area" id="area_id" class="form-control">
                                    <option value="">請選擇區</option>
                                    <?php
                                    $area_sql = "SELECT * FROM area WHERE city_id='".$row['city_id']."'";
                                    $area_res = mysql_query($area_sql);
                                    while ($area_row = mysql_fetch_array($area_res))
                                    {
                                        if($row['area_id'] == $area_row['id'])
                                        {
                                            ?>
                                            <option selected value="<?php echo $area_row['id']; ?>"><?php echo $area_row['area']; ?></option>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <option value="<?php echo $area_row['id']; ?>"><?php echo $area_row['area']; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><span style="color:red;">*</span>地址</label>
                        <div class="col-md-8">
                            <input class="form-control" name="address" type="text" value="<?php echo $row['address']; ?>">
                            <span style="display: none; color: red; font-size: 14px;" id="address-hint">地址為必填</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><span style="color:red;">*</span>手機</label>
                        <div class="col-md-8">
                            <input class="form-control" name="cellphone" type="text" value="<?php echo $row['cellphone']; ?>">
                            <span style="display: none; color: red; font-size: 14px;" id="phone-hint">手機為必填</span>
                        </div>
                    </div>
                    <div class="form-group" id="btn_panel">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-8">
                            <input type="submit" name="btn" class="btn btn-primary btn-block" value="儲存">
                            <input type="button" class="btn btn-default btn-block" onclick="location.href='index.php?url=member_center'" value="返回會員專區">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <hr>
</div>
<?php
@$password = $_POST['password'];
@$m_name = $_POST['m_name'];
@$id_card = $_POST['id_card']; //出生年月日ex:19910101
//@$birthday = $_POST['birthday'];
@$sex = $_POST['sex'];
@$city = $_POST['city'];
@$area = $_POST['area'];
@$address = $_POST['address'];
@$cellphone = $_POST['cellphone'];
//@$line = $_POST['line'];

if(@$_POST['btn'] && @$_SESSION["member_no"] != "")
{
    if($_SESSION['device'] == 'desktop')
    {
        $sql = "UPDATE member SET password='$password', m_name='$m_name', id_card='$id_card', sex='$sex', city_id='$city', area_id='$area', address='$address', cellphone='$cellphone' WHERE id='$member_id'";
    }
    else
    {
        $sql = "UPDATE member SET m_name='$m_name', id_card='$id_card', sex='$sex', city_id='$city', area_id='$area', address='$address', cellphone='$cellphone' WHERE id='$member_id'";
    }
    mysql_query($sql);
    ?>
    <script>
		if($("#device").text() == 'mobile')
		{
			window.javatojs.showInfoFromJs('儲存完畢');
		}
		else
		{
			alert('儲存完畢');
		}
		location.href='index.php?url=member_info';
    </script>
    <?php
}
else if(@$_POST['btn'] && @$_SESSION["fb_id"] != "")
{
    $sql = "UPDATE fb SET id_card='$id_card', sex='$sex', city_id='$city', area_id='$area', address='$address', cellphone='$cellphone' WHERE fb_id='".$_SESSION["fb_id"]."'";
    mysql_query($sql);
    ?>
    <script>
        if($("#device").text() == 'mobile')
		{
			window.javatojs.showInfoFromJs('儲存完畢');
		}
		else
		{
			alert('儲存完畢');
		}
		location.href='index.php?url=member_info';
    </script>
    <?php
}
?>
<script>
    $(function ()
    {
        $("#aa-slider").hide();
        $("html,body").scrollTop(70);
    });

    function form_stop()
    {
        if($("#device").text() != 'mobile' && $("input[name='password']").val() == '')
        {
            $("input[name='password']").focus();
            $("#password-hint").show();
            return false;
        }

        if($("input[name='m_name']").val() == '')
        {
            $("input[name='m_name']").focus();
            $("#name-hint").show();
            return false;
        }

        if($("input[name='id_card']").val() == '')
        {
            $("input[name='id_card']").focus();
            $("#id_card-hint").text('出生年月日必填');
            $("#id_card-hint").show();
            return false;
        }

        if($("input[name='id_card']").val() != '')
        {
            var id_card = $("input[name='id_card']").val();
            var id_card_reg = /^[0-9]{8}$/;
            if(!id_card_reg.test(id_card))
            {
                $("input[name='id_card']").focus();
                $("input[name='id_card']").val('');
                $("#id_card-hint").text('請填寫正確格式');
                $("#id_card-hint").show();
                return false;
            }
        }

        if($("input[name='address']").val() == '')
        {
            $("input[name='address']").focus();
            $("#address-hint").show();
            return false;
        }

        if($("input[name='cellphone']").val() == '')
        {
            $("input[name='cellphone']").focus();
            $("#phone-hint").text('手機為必填');
            $("#phone-hint").show();
            return false;
        }

        if($("input[name='cellphone']").val() != '')
        {
            var phone = $("input[name='cellphone']").val();
            var cellphone_reg = /^09[0-9]{8}$/;
            if(!cellphone_reg.test(phone))
            {
                $("input[name='cellphone']").focus();
                $("input[name='cellphone']").val('');
                $("#phone-hint").text('請填寫正確格式');
                $("#phone-hint").show();
                return false;
            }
        }
    }

    $("#city_id").change(function()
    {
        $("#area_id").find("option").not(":first").remove();

        var id = $(this).val();
        $.ajax
        ({
            url:"admin/sever_ajax.php", //接收頁
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