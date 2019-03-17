<?php

use Base17Mai\Manager, Base17Mai\Member, Base17Mai\Bank, Base17Mai\Bonus;
use function Base17Mai\take;

$id = take('id', '', 'GET');
if (empty($id)) exit('尚未選擇會員');
$Member = new Member();
$Manager = new Manager();
$Bonus = new Bonus();
$Bank = new Bank();
$Profile = $Member->getMemberProfile(['id' => $id]);
$Profile['memberID'] = $id;
$bankList = $Bank->getBankList();
$bankIDListHtml = $Member->generateBankOptionsHtml($bankList, $Profile['bank_id']);
$genderList = $Member->getAllGender();
$genderOptionsHtml = $Member->generateGenderOption($genderList, $Profile['gender']);
$cityList = $Member->getAllCity();
$cityOptionsHtml = $Member->generateCityOptionHtml($cityList, $Profile['city_id']);
$areaList = $Member->getSpecifyArea($Profile['city_id']);
$areaOptionsHtml = $Member->generateAreaOptionHtml($areaList, $Profile['area_id']);

$member_no = $Member->GetMemberInformation('member_no', ['id' => $id]);
if (!isset($member_no[0])) exit('嚴重錯誤，請回上一頁再試一次');
else $member_no = $member_no[0]['member_no'];
$self = $Member->GetRecord($member_no);
$selfAmount = $self['Amount'];
$selfBonus = $self['bonus'];
$ManagerNO = $Manager->GetManagerInformation('manager_no', ['member_id' => $member_no]);
if (!isset($ManagerNO[0])) $ManagerNO = '';
else $ManagerNO = $ManagerNO[0]['manager_no'];
if (!empty($ManagerNO)) {
    $modifyBonus = $Bonus->SumModifyBonus($ManagerNO);
    $CrewMember = $Manager->ListCrewMemberNO();
    $ValidBonus = $Bonus->CalculateBonus($selfAmount, $CrewMember) + $modifyBonus;
} else $ValidBonus = '此會員並非家長，不會有家族獎金';

$id = $_GET['id'];
$sql = "SELECT * FROM member WHERE id='$id'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
?>
<div class="widget">
    <h4 class="widgettitle">修改會員資料</h4>
    <div class="widgetcontent">
        <form id="profile_form" class="stdform stdform2" method="post">
            <p>
                <label>會員名稱</label>
                <span class="field">
                    <input type="text" name="m_name" class="input-large" value="<?= $Profile['m_name'] ?>"
                           placeholder="請輸入會員名稱"/>
                </span>
            </p>
            <p>
                <label>電子信箱(帳號)</label>
                <span class="field">
                    <input type="hidden" name="memberID" value="<?= $Profile['memberID'] ?>">
                    <input type="text" name="email" class="input-large" value="<?= $Profile['email'] ?>"
                           placeholder="請輸入電子信箱(帳號)"/>
                </span>
            </p>
            <p>
                <label>密碼</label>
                <span class="field">
                    <input type="password" name="password" class="input-large" placeholder="不更改請留白"/>
                </span>
            </p>
            <p>
                <label>身分證字號</label>
                <span class="field">
                    <input type="text" name="id_card" class="input-large" value="<?= $Profile['id_card']; ?>"
                           placeholder="請輸入身分證字號"/>
                </span>
            </p>
            <p>
                <label>生日</label>
                <span class="field">
                    <input type="date" name="birthday" class="input-large" value="<?= $Profile['born']; ?>"/>
                </span>
            </p>
            <p>
                <label>性別</label>
                <span class="field">
                    <select name="sex" id="sex" class="uniformselect">
                        <?= $genderOptionsHtml ?>
                    </select>
                </span>
            </p>
            <p>
                <label>出金帳戶</label>
                <span class="field">
                    <select class="form-control" name="bank_id" style="margin: 0px;">
                        <option value="NaN">請選擇銀行</option>
                        <?= $bankIDListHtml ?>
                    </select>
                    <input class="form-control" name="bank_no" type="text" value="<?= $Profile['bank_no'] ?>"
                           placeholder="ex：0001990-9876123">
                </span>
            </p>
            <p>
                <label>行動電話</label>
                <span class="field">
                    <input type="text" name="cellphone" class="input-large" value="<?= $Profile['cellphone'] ?>"
                           placeholder="請輸入行動電話"/></span>
            </p>
            <p>
                <label>個人點數</label>
                <span class="field">
                    <?= $selfBonus ?> 點
                </span>
            </p>
            <p>
                <label>家族點數</label>
                <span class="field">
                    <?= $ValidBonus ?> 點
                </span>
            </p>
            <p>
                <label>地址</label>
                <span class="field">
                    <select name="city" id="city_id" class="form-control">
                        <option value="">請選擇市</option>
                        <?= $cityOptionsHtml ?>
                    </select>
                    <select name="area" id="area_id" class="form-control">
                        <option value="">請選擇區</option>
                        <?= $areaOptionsHtml ?>
                    </select>
                    <br>
                    <input type="text" name="address" value="<?= $Profile['address'] ?>" class=" input-xxlarge"/>
                </span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="btn btn-primary span1" value="修改">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" class="btn btn-default span1" value="返回"
                       onclick="location.href='home.php?url=member'">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
@$m_name = $_POST['m_name'];
@$email = $_POST['email'];
@$password = $_POST['password'];
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

if (isset($_POST['btn'])) {
    $sql = "UPDATE member SET m_name='$m_name', email='$email', password='$password', id_card='$id_card', birthday='$birthday', 
 sex='$sex', cellphone='$cellphone', bonus='$bonus', profit='$profit', city_id='$city_id', area_id='$area_id', address='$address' WHERE id='$id'";
    mysql_query($sql);
    ?>
    <script>
        alert('修改成功');
        location.href = 'home.php?url=member';
    </script>
    <?php
}
?>

<script type="text/javascript">

    $('#profile_form').on('submit', function () {
        let inputValue = getFormData($(this));
        ajax17mai('Member', 'UpdateProfile', {}, inputValue);
        return false;
    });

    $("#city_id").change(function () {
        $("#area_id").find("option").not(":first").remove();

        var id = $(this).val();
        $.ajax
        ({
            url: "sever_ajax.php", //接收頁
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