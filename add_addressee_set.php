<?php
if (isset($_SESSION['member_no']) && $_SESSION['member_no'] != '') {
    $member_id = $_SESSION['member_no'];
} else {
    $member_id = $_SESSION["fb_id"];
}
// 從資料庫選擇城市
function select_city()
{
    $city_sql = "SELECT * FROM city";
    $city_res = mysql_query($city_sql);
    $city_row = array();
    while ($cur_row = mysql_fetch_array($city_res)) {
        $city_row[] = $cur_row;
    }
    return $city_row;
}

// 產生 html option
function gen_city_list()
{
    $city_list = select_city();
    foreach ($city_list as $v) {
        $value = $v['id'];
        $index = $v['city'];
        echo "<option value=\"$value\">$index</option>";
    }
}

?>

<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li><a href="index.php?url=addressee_set">常用收件人列表</a></li>
                <li class="active">新增常用收件人</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->

<div style="margin-top: 5%;">
    <div class="container panel">
        <h1 align="center">新增常用收件人資料</h1>
        <hr>
        <div class="row">
            <!-- edit form column -->
            <div class="col-md-11 personal-info">
                <form class="form-horizontal" role="form" method="post">
                    <div class="form-group">
                        <label class="col-lg-3 control-label">姓名</label>
                        <div class="col-lg-8">
                            <input class="form-control" name="name" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">行動電話</label>
                        <div class="col-md-8">
                            <input class="form-control" name="cellphone" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">性別</label>
                        <div class="col-lg-8">
                            <div class="ui-select">
                                <select name="sex" class="form-control">
                                    <option value="1">男</option>
                                    <option value="0">女</option>
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
                                    <?php gen_city_list(); ?>
                                </select>
                            </div>
                        </div>&nbsp;&nbsp;
                        <div class="col-lg-4">
                            <div class="ui-select">
                                <select name="area" id="area_id" class="form-control">
                                    <option value="">請選擇區</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">地址</label>
                        <div class="col-md-8">
                            <input class="form-control" name="address" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-8" style="bottom:8px;">
                            <input type="submit" name="btn" class="btn btn-primary btn-block" value="儲存">
                            <input type="button" class="btn btn-default btn-block"
                                   onclick="location.href='index.php?url=addressee_set'" value="返回收件人列表">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <hr>
</div>
<br><br>
<?php
@$name = $_POST['name'];
@$cellphone = $_POST['cellphone'];
@$sex = $_POST['sex'];
@$city = $_POST['city'];
@$area = $_POST['area'];
@$address = $_POST['address'];

if (@$_POST['btn']) {
    $sql = "INSERT INTO addressee_set SET `name`='$name', cellphone='$cellphone', sex='$sex', city_id='$city', area_id='$area', address='$address', m_id='$member_id'";
    $res = mysql_query($sql);
    ?>
    <script>
        alert('儲存完畢');
        location.href = 'index.php?url=addressee_set';
    </script>
    <?php
}
?>
<script>
    $(function () {
        $("#aa-slider").hide();
        $("html,body").scrollTop(70);
    });

    $("#city_id").change(function () {
        $("#area_id").find("option").not(":first").remove();

        var id = $(this).val();
        $.ajax
        ({
            url: "admin/sever_ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: 'get_area', city_id: id}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                $("#area_id").append(i);
            },
            error: function ()//失敗就...
            {
                alert("ajax失敗");
            }
        });
    });
</script>