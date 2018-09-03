<?php
use Base17Mai\Manager;

$Manager = new Manager();
$CrewList = $Manager->ListCrewMember();
?>
<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li><a href="index.php?url=member_center">會員專區</a></li>
                <li class="active">家族成員</li>
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

<div class="container">
    <div class="row">
        <h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">家族成員</h3>
        <table class="dataTable table table-bordered table-responsive table-condensed" id="pay_check_div">
            <thead>
            <tr style="background: #DDDDDD;">
                <th style="text-align: center;">姓名</th>
                <th style="text-align: center;">統一編號</th>
                <th style="text-align: center;">手機</th>
                <th style="text-align: center;">電子信箱</th>
                <th style="text-align: center;">住址</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($CrewList as $item) {
                ?>
                <tr>
                    <td><?= $item['m_name'] ?></td>
                    <td><?= $item['id_card'] ?></td>
                    <td><?= $item['cellphone'] ?></td>
                    <td><?= $item['email'] ?></td>
                    <td><?= $item['address'] ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <div class="container">
            <div class="row" style="text-align: right;">
                <input type="button" class="btn btn-default" value="返回" onclick="history.go(-1);">&nbsp;&nbsp;
                <!--                            <input type="button" class="btn btn-primary" value="兌換" id="pay_btn">-->
            </div>
        </div>
    </div>
</div>

<!-- 彈出視窗 -->
<!--<input type="button" class="btn btn-success" value="查看詳細內容" id="manager_rule" data-toggle="modal" data-target="#manager_modal" role_status="0">-->
<div class="modal fade" id="manager_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4>行銷經理規範</h4>
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
    $("html,body").scrollTop(750);
    $("#aa-slider").hide();
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