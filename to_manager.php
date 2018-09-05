<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li><a href="index.php?url=member_center">會員專區</a></li>
                <li class="active">行銷經理申請</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->
<script>
    function dis_msg(txt) {
        if (typeof(txt) === 'string') {
            if ($('#device').text() === 'mobile') {
                window.javatojs.showInfoFromJs(txt);
            }
            if ($('#device').text() === 'desktop') {
                alert(txt);
            }
        }
    }
</script>
<?php

if(isset($_SESSION["member_no"]))
{
    $member_id = $_SESSION["member_no"];
}
else
{
    $member_id = $_SESSION["fb_id"];
}
$sql = "SELECT * FROM member WHERE member_no='$member_id'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);

$check_sql = "SELECT * FROM seller_manager left join member on member_no = member_id WHERE member_id='$member_id'";
$check_res = mysql_query($check_sql);
@$check_row = mysql_fetch_array($check_res);
?>
<div style="margin-top: 5%;">
    <div class="container panel">
        <h1 align="center">團購家族-申請</h1>
        <hr>
        <div class="row">
            <!-- edit form column -->
            <div class="col-md-12 personal-info">
                <form class="form-horizontal" onSubmit="return form_stop()" role="form" method="post">

<!--                    <div class="form-group">-->
<!--                        <label class="col-lg-3 control-label">擅長行銷商品類別</label>-->
<!---->
<!--                        <div class="col-lg-8">-->
<!--                            <div class="ui-select">-->
<!--                                <select name="class_id[]" id="class_id" class="form-control" multiple>-->
<!--                                    --><?php
//                                    if($check_row['class_id'])
//                                    {
//                                        $class_sql = "SELECT * FROM `class` WHERE id = parent_id AND id IN(".$check_row['class_id'].")";
//                                        echo $class_sql;
//                                    }
//                                    else
//                                    {
//                                        $class_sql = "SELECT * FROM `class` WHERE id = parent_id";
//                                    }
//                                    $class_res = mysql_query($class_sql);
//                                    while ($class_row = mysql_fetch_array($class_res))
//                                    {
//                                        ?>
<!--                                        <option value="--><?php //echo $class_row['id']; ?><!--">--><?php //echo $class_row['name']; ?><!--</option>-->
<!--                                        --><?php
//                                    }
//                                    ?>
<!--                                </select>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
                    <?php
                    if($check_row['apply_status'] != 1)
                    {
                        ?>
                        <!--
                        <div class="form-group">
                            <label class="col-lg-3 control-label">申請原因</label>
                            <div class="col-lg-8">
                                <textarea name="self_introduction" id="self_introduction" class="form-control" rows="5" placeholder="ex:我想成為行銷經理因為.....">
                                <?php
                                    if($check_row['self_introduction'] != "") {
                                        echo trim($check_row['self_introduction']);
                                    }
                                ?>
                                </textarea>
                            </div>
                        </div>
                        -->
                        <?php
                    }
                    ?>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">申請狀態</label>
                        <div class="col-lg-8" style="top:8px;">
                            <span id="add_status">
                                <?php
                                switch ($check_row['apply_status'])
                                {
                                    case '':
                                        echo '尚未申請';
                                        break;
                                    case 0:
                                        echo '申請失敗';
                                        break;
                                    case 1:
                                        echo '通過';
                                        break;
                                    case 2:
                                        echo '待審核';
                                        break;
                                }
                                ?>
                            </span>
                        </div>
                    </div>

                    <?php
                    if($check_row['fail_info'] != "" && $check_row['apply_status'] == 0)
                    {
                        ?>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">不通過原因</label>
                            <div class="col-lg-8" style="top:8px;">
                            <span id="add_status">
                                <?php
                                echo $check_row['fail_info'];
                                ?>
                            </span>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">專屬代號</label>
                        <div class="col-lg-8" style="top:8px;">
                            <span id="manager_no">
                                <?php
                                if($check_row['email'])
                                {
                                    echo $check_row['email'];
                                }
                                else
                                {
                                    echo '暫無';
                                }
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">
                            團購家族條款
                        </label>
                        <div class="col-lg-8" style="top:3px;">
                            <span>
                                <input type="button" class="btn btn-success" value="查看詳細內容" id="manager_rule" data-toggle="modal" data-target="#manager_modal" role_status="0">
                                <br>
                                <span>
                                    <input type="checkbox" name="contract" value="0">&nbsp;&nbsp;同意遵守團購家族條款
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-8" style="bottom:5px;">
                            <input type="submit" id="btn" name="btn" class="btn btn-primary btn-block" value="提交申請" <?php if($check_row['id'] && $check_row['apply_status'] != 0){echo 'disabled';} ?>>
                            <input type="button" class="btn btn-default btn-block" onclick="location.href='index.php?url=member_center'" value="返回會員專區">
                        </div>
                    </div>
                    <br><br>
                </form>
            </div>
        </div>
    </div>
    <hr>
</div>

<!-- 彈出視窗 -->
<div class="modal fade" id="manager_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4>團購家族家長條例</h4>
                <div>
                    <?php
                    $rule_sql = "SELECT * FROM rule WHERE id='1'";
                    $rule_res = mysql_query($rule_sql);
                    $rule_row = mysql_fetch_array($rule_res);
                    echo $rule_row['content'];
                    ?>
                </div>
            </div>
        </div><!-- /.彈出視窗內容 -->
    </div><!-- /.彈出視窗結束 -->
</div>


<?php
    //@$class_id = $_POST['class_id'];
    $self_introduction = isset($_POST['self_introduction'])? $_POST['self_introduction']:false;
    $manager_no = "";
    for($i=1;$i<=9;$i++) {
        $num = rand(1,9);
        $manager_no .= $num;
    }

    if (isset($_POST['btn'])) {
        $sql = '';
        if ($self_introduction !== false && $self_introduction !== '') {
            if ($check_row['id'] !== '') {
                $sql .= "update seller_manager set self_introduction='".trim($self_introduction)."' apply_status='2'";
                $sql .= " where member_id='$member_id';";
            } else {
                $sql .= "insert into seller_manager set";
                $sql .= " member_id = '$member_id', manager_no='$manager_no',";
                $sql .= " self_introduction='".trim($self_introduction)."',";
                $sql .= " identity='manager', apply_status='2',";
                $sql .= " manager_status='0', apply_time='".date('Y-m-d H:i:s')."';";
            }
        } else {
            $sql .= "insert into seller_manager set";
            $sql .= " member_id = '$member_id', manager_no='$manager_no',";
            $sql .= " self_introduction='".trim($self_introduction)."',";
            $sql .= " identity='manager', apply_status='2',";
            $sql .= " manager_status='0', apply_time='".date('Y-m-d H:i:s')."';";
        }
        mysql_query($sql);
        if (mysql_affected_rows()) {
            echo "<script> dis_msg('申請成功，請等待管理員審核');</script>";
            echo '<script>location.href=\'index.php?url=to_manager\';</script>';
        }
    }

/*
    if(@$_POST['btn'] && $_POST['self_introduction'] != "") {
        if($check_row['id'] != "") {
            $sql = "UPDATE seller_manager SET self_introduction='".trim($self_introduction)."', apply_status='2' WHERE member_id='".$member_id."'";
//            mysql_query($sql);
        } else {
            $sql = "INSERT INTO seller_manager SET member_id='" . $member_id . "', manager_no='$manager_no', self_introduction='" . trim($self_introduction) . "', `identity`='manager',
            apply_status='2', manager_status='0', apply_time='" . date('Y-m-d H:i:s') . "'";
//            mysql_query($sql);
        }
?>
    <script>
        if($("#device").text() == 'mobile')
        {
            window.javatojs.showInfoFromJs('申請成功，請等待管理員審核');
        }
        else
        {
            alert('申請成功，請等待管理員審核');
        }
        location.href='index.php?url=to_manager';
    </script>
    <?php
}*/
?>
<script>
    var apply_status = '<?=$check_row['apply_status']?>';
    if (apply_status !== '') {
        $('input[type="submit"][name="btn"]').hide();
        $($('input[type="checkbox"][name="contract"]').parent().get(0)).hide();
    }
    /*
    function dis_msg(txt) {
        if (typeof(txt) === 'string') {
            if ($('#device').text() === 'mobile') {
                window.javatojs.showInfoFromJs(txt);
            }
            if ($('#device').text() === 'desktop') {
                alert(txt);
            }
        }
    }
    */
    $(function ()
    {
        $("#aa-slider").hide();
        $("html,body").scrollTop(70);
    });

    function form_stop()
    {
        if ($('#manager_rule').attr('role_status') === '0') {
            dis_msg('請點選查看行銷經理規範後，才可提交資料');
            $('#manager_rule').focus();
            return false;
        }

        if (!$('input[type="checkbox"][name="contract"]').prop('checked')) {
            dis_msg("請同意遵守團購家族條款後，才可提交資料");
            $('input[type="checkbox"][name="contract"]').focus();
            return false;
        }

        if($("#self_introduction").val().trim() != "")
        {
            if($("#manager_rule").attr('role_status') == 0)
            {
                if($("#device").text() == 'mobile')
                {
                    window.javatojs.showInfoFromJs('請點選查看行銷經理規範後，才可提交資料');
                }
                else
                {
                    alert('請點選查看行銷經理規範後，才可提交資料');
                }
                $("#manager_rule").focus();
                return false;
            }
        }
        else
        {
            if($("#device").text() == 'mobile')
            {
                window.javatojs.showInfoFromJs('請填寫申請原因');
            }
            else
            {
                alert('請填寫申請原因');
            }
            return false;
        }
    }

    $("#manager_rule").click(function()
    {
        $(this).attr('role_status','1');
    });
</script>