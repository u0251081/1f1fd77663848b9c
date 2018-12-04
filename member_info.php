<?php
defined('BaseSecurity') or escapeFromHere();
function escapeFromHere()
{
    header('location: index.php?url=404');
    exit();
}

$memberNO = isset($_SESSION['member_no']) ? $_SESSION['member_no'] : false;
$member_info = false;
$member_email = ''; // 會員帳號
$member_name = ''; // 會員姓名
$member_born = '0000-00-00'; // 會員生日
$member_idCard = ''; // 統一編號(身分證字號/統一編號)
$member_BankID = ''; // 銀行代碼
$member_BankNO = ''; // 匯款帳號
$parent_number = ''; // 家長編號,避免同名狀況
$parent_name = ''; // 家長姓名
$member_gender = ''; // 會員性別 0 第三性 1 男生 2 女生
$member_city = ''; // 城市代碼
$member_area = ''; // 區代碼
$member_addr = ''; // 詳細住址
$member_phone = ''; // 連絡電話 手機為主

$requireInputName1 = 'name="born"';
$requireInputName2 = 'name="id_card"';
$requireInputName3 = 'name="parent_number"';
$requireInputName4 = 'name="gender"';

$bankIDListHtml = '<option vlaue="000">測試用銀行</option>';
$member_id = false;

use Base17Mai\Member;

$memberInterface = new Member();
$member_info = $memberInterface->getMemberProfile($memberNO);

// 取出會員資料, 若有會員流水號
if ($member_id !== false) {
    $sql = '';
    $sql .= "select a.*, b.m_name as parent_name, a.parent_no as parent_no from member as a";
    $sql .= " left join member as b on a.parent_no = b.member_no where a.id = {$member_id};";
    $res = pdo_select_sql($sql);
    $member_info = isset($res[0]) ? $res[0] : false;
}
// 配置會員資料
if ($member_info !== false) {
    $member_email = $member_info['email']; // 會員帳號
    $member_name = $member_info['m_name']; // 會員姓名
    $member_born = empty($member_info['born']) ? '' : $member_info['born'] . "\"readonly \""; // 會員生日
    $member_idCard = empty($member_info['id_card']) ? '' : $member_info['id_card'] . "\"readonly \""; // 統一編號(身分證字號/統一編號)
    $member_BankID = $member_info['bank_id']; // 銀行代碼
    $member_BankNO = $member_info['bank_no']; // 匯款帳號
    // print empty($member_info['parent_no']) ? '' : $member_info['parent_no'] . "\"disabled=\"";
    $parent_number = empty($member_info['parent_no']) ? '' : $member_info['parent_no'] . "\"readonly \""; // 家長編號,避免同名狀況
    $parent_name = empty($member_info['parent_name']) ? '' : $member_info['parent_name'] . "\" readonly \""; // 家長姓名
    $member_gender = $member_info['gender']; // 會員性別 0 第三性 1 男生 2 女生
    $member_city = $member_info['city_id']; // 城市代碼
    $member_area = $member_info['area_id']; // 區代碼
    $member_addr = $member_info['address']; // 詳細住址
    $member_phone = $member_info['cellphone']; // 連絡電話 手機為主
}

// 產生銀行選單
use Base17Mai\Bank;

$bank = new Bank();
$bankList = $bank->getBankList();
$bankIDListHtml = generateBankOptionsHtml($bankList, $member_BankID);
$genderList = $memberInterface->getAllGender();
$genderOptionsHtml = generateGenderOption($genderList, $member_gender);
$cityList = $memberInterface->getAllCity();
$cityOptionsHtml = generateCityOptionHtml($cityList, $member_city);
$areaList = $memberInterface->getSpecifyArea($member_city);
$areaOptionsHtml = generateAreaOptionHtml($areaList, $member_area);

// 處理一次性資料輸入無效化
// if (!empty($member_born)) $requireInputName1 = '';
// if (!empty($member_idCard)) $requireInputName2 = '';
// if (!empty($parent_number)) $requireInputName3 = '';
if (strlen($member_gender) === 0) {
    $genderHtml = "<select {$requireInputName4} class=\"form-control\">{$genderOptionsHtml}</select>";
} else {
    $genderHtml = "<input type=\"text\" class=\"form-control\" readonly value=\"{$genderList[$member_gender]}\">";
}

// 產生銀行選單
function generateBankOptionsHtml($bankList = array(), $targetID = false)
{
    $html = '';
    if (!is_array($bankList)) return false;
    foreach ($bankList as $key => $value) {
        if (!isset($value['id'])) return false;
        $selected = ($value['code'] === $targetID) ? 'selected' : '';
        $html .= "<option value='{$value['code']}' {$selected}>{$value['code']} : {$value['Institutions']}</option>";
    }
    return $html;
}

// 產生性別選單
function generateGenderOption($genderList = array(), $targetID = false)
{
    $html = '';
    $html .= '<option value="NaN">請選擇性別</option>';
    if (!is_array($genderList)) return false;
    foreach ($genderList as $key => $value) {
        $selected = ((String)$key === $targetID) ? 'selected' : '';
        $html .= "<option value='{$key}' {$selected}>{$value}</option>";
    }
    return $html;
}

// 產生城市選單
function generateCityOptionHtml($cityList = array(), $targetID = false)
{
    $html = '';
    if (!is_array($cityList)) return false;
    foreach ($cityList as $key => $value) {
        $selected = ($value['id'] === $targetID) ? 'selected' : '';
        $html .= "<option value='{$value['id']}' {$selected}>{$value['city']}</option>";
    }
    return $html;
}

// 產生地區選單
function generateAreaOptionHtml($areaList = array(), $targetID = false)
{
    $html = '';
    if (!is_array($areaList)) return false;
    foreach ($areaList as $key => $value) {
        $selected = ($value['id'] === $targetID) ? 'selected' : '';
        $html .= "<option value='{$value['id']}' {$selected}>{$value['area']}</option>";
    }
    return $html;
}

?>
    <style>
        @media (max-width: 768px) {
            #btn_panel {
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


    <div>
        <div class="container">
            <h1 align="center">個人資料</h1>
            <hr>
            <div class="row">
                <!-- edit form column -->
                <div class="col-md-12 personal-info">
                    <form class="form-horizontal" id="profile_form" role="form" method="post">
                        <!-- 會員編號 -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label">會員編號</label>
                            <div class="col-lg-8">
                                <input class="form-control" type="text" value="<?= $memberNO ?>" disabled>
                            </div>
                        </div>
                        <!-- 帳號/e-mail -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label">E-mail(帳號)</label>
                            <div class="col-lg-8">
                                <input class="form-control" name="email" type="text" value="<?= $member_email ?>"
                                       disabled>
                            </div>
                        </div>
                        <!-- 密碼/不填寫不更新 -->
                        <div class="form-group NotForMobile">
                            <label class="col-lg-3 control-label">
                                <span style="color:red;">*</span>密碼
                            </label>
                            <div class="col-lg-8">
                                <span style="color: red; font-size: 14px;">若不欲修改請留白</span>
                                <input class="form-control" name="password" type="password" value=""
                                       placeholder="輸入新密碼">
                                <span style="display:none; color: red; font-size: 14px;" id="password-hint">密碼為必填</span>
                            </div>
                        </div>
                        <!-- 會員姓名 -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><span style="color:red;">*</span>會員姓名</label>
                            <div class="col-lg-8">
                                <input class="form-control" name="m_name" type="text" value="<?= $member_name ?>">
                                <span style="display: none; color: red; font-size: 14px;" id="name-hint">姓名為必填</span>
                            </div>
                        </div>
                        <!-- 匯款帳號 -->
                        <div class="form-group NotForMobile">
                            <label class="col-lg-3 control-label">
                                <span style="color:red;">*</span>出金帳戶
                            </label>
                            <div class="col-lg-3">
                                <!--<span style="color: red; font-size: 14px;">此資料僅用於審核自然人或是法人是否有效</span>-->
                                <select class="form-control" name="bank_id">
                                    <option value="NaN">請選擇銀行</option>
                                    <?= $bankIDListHtml ?>
                                </select>
                                <span style="display: none; color: red; font-size: 14px;"
                                      id="bankid-hint">銀行代碼為必填</span>
                            </div>
                            &nbsp;&nbsp;
                            <div class="col-lg-5">
                                <!--<span style="color: red; font-size: 14px;">此資料僅用於審核自然人或是法人是否有效</span>-->
                                <input class="form-control" name="bank_no" type="text" value="<?= $member_BankNO ?>"
                                       placeholder="ex：0001990-9876123">
                                <span style="display: none; color: red; font-size: 14px;"
                                      id="bankno-hint">銀行帳號為必填</span>
                            </div>
                        </div>
                        <!-- 地址 城市/地區 -->
                        <div class="form-group">
                            <label class="col-lg-3 control-label">市 / 區</label>
                            <div class="col-lg-4">
                                <div class="ui-select">
                                    <select name="city" id="city_id" class="form-control">
                                        <option value="">請選擇市</option>
                                        <?= $cityOptionsHtml ?>
                                    </select>
                                </div>
                            </div>&nbsp;&nbsp;
                            <div class="col-lg-4">
                                <div class="ui-select">
                                    <select name="area" id="area_id" class="form-control">
                                        <option value="">請選擇區</option>
                                        <?= $areaOptionsHtml ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- 地址 街道/巷弄 -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><span style="color:red;">*</span>地址</label>
                            <div class="col-md-8">
                                <input class="form-control" name="address" type="text"
                                       value="<?= $member_addr ?>">
                                <span style="display: none; color: red; font-size: 14px;" id="address-hint">地址為必填</span>
                            </div>
                        </div>
                        <!-- 手機 -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><span style="color:red;">*</span>手機</label>
                            <div class="col-md-8">
                                <input class="form-control" name="cellphone" type="text"
                                       value="<?= $member_phone ?>">
                                <span style="display: none; color: red; font-size: 14px;" id="phone-hint">手機為必填</span>
                            </div>
                        </div>
                        <fieldset>
                            <legend align="center">身份確認資料</legend>
                            <div class="form-group">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-8">
                                    <span style="color: red; font-size: 14px;">以下資料請仔細確認，一旦送出將無法輕易修改</span>
                                </div>
                            </div>
                            <!-- 出生年月日 -->
                            <div class="form-group">
                                <label class="col-lg-3 control-label">
                                    <span style="color:red;">*</span>出生年月日
                                </label>
                                <div class="col-lg-8">
                                    <input class="form-control" <?= $requireInputName1 ?> type="date"
                                           value="<?= $member_born ?>" placeholder="ex：1992/01/01">
                                    <!-- 用於驗證是否為身份證字號本人用之個資 -->
                                    <span style="display: none; color: red; font-size: 14px;"
                                          id="born-hint">出生年月日為必填</span>
                                </div>
                            </div>
                            <!-- 統一編號 -->
                            <div class="form-group">
                                <label class="col-lg-3 control-label">
                                    <span style="color:red;">*</span>統一編號
                                </label>
                                <div class="col-lg-8">
                                    <span style="color: red; font-size: 14px;">此資料僅用於審核自然人或是法人是否有效</span>
                                    <input class="form-control" <?= $requireInputName2 ?> type="text"
                                           value="<?= $member_idCard ?>" placeholder="ex：A123456789 or 12345675">
                                    <span style="display: none; color: red; font-size: 14px;"
                                          id="id_card-hint">統一編號為必填</span>
                                </div>
                            </div>
                            <!-- 團購家族家長-編號 -->
                            <div class="form-group">
                                <label class="col-lg-3 control-label">團購家族-家長編號</label>
                                <div class="col-lg-8">
                                    <input class="form-control" <?= $requireInputName3 ?> type="text"
                                           value="<?php echo $parent_number; ?>">
                                    <span style="display: none; color: red; font-size: 14px;"
                                          id="parent-hint">團購家族家長編號有誤</span>
                                </div>
                            </div>
                            <!-- 團購家族家長-姓名 -->
                            <div class="form-group">
                                <label class="col-lg-3 control-label">團購家族-家長姓名</label>
                                <div class="col-lg-8">
                                    <input class="form-control" id="parent_name" type="text" readonly
                                           placeholder="團購家族家長姓名，於編號輸入後自動帶入，僅用於確認"
                                           value="<?php echo $parent_name; ?>">
                                    <span style="display: none; color: red; font-size: 14px;"
                                          id="parent-hint">團購家族家長姓名有誤</span>
                                </div>
                            </div>
                            <!-- 性別 -->
                            <div class="form-group">
                                <label class="col-lg-3 control-label">性別</label>
                                <div class="col-lg-8">
                                    <div class="ui-select">
                                        <?= $genderHtml ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="btn_panel">
                                <label class="col-md-3 control-label"></label>
                                <div class="col-md-8">
                                    <input type="submit" name="btn" class="btn btn-primary btn-block" value="儲存">
                                    <input type="button" class="btn btn-default btn-block"
                                           onclick="location.href='index.php?url=member_center'" value="返回會員專區">
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
        <hr>
    </div>
    <script>
        $(function () {
            $("#aa-slider").hide();
            $("html,body").scrollTop(70);
        });

        $('#city_id').on('blur', function () {
            let targetID = $(this).val();
            let postValue = {targetID: targetID};
            ajax17mai('Member', 'ChangeCity', {}, postValue);
        });

        $(document).on('blur', 'input[name="parent_number"]:not(:disabled)', function () {
            let targetID = $(this).val();
            if (targetID.length > 0)
                ajax17mai('Member', 'GetMemberName', {}, {targetID: targetID});
        });

        $('#profile_form').on('submit', function () {
            let inputValue = getFormData($(this));
            ajax17mai('Member', 'UpdateProfile', {}, inputValue);
            return false;
        });

        function checkInput() {
            var error_flag = false;
            var passwd = $('input[name="password"]');
            if (!checkPassword(passwd.val())) error_flag = true;
            var member_name = $('input[name="m_name"]');
            if (!checkMemberName(member_name.val())) error_flag = true;
            var member_born = $('input[name="born"]');
            if (!checkMemberBorn(member_born.val())) error_flag = true;
            var member_idCard = $('input[name="id_card"]');
            var bank_id = $('select[name="bank_id"]');
            var bank_no = $('input[name="bank_no"]');
            var parent_number = $('input[name="parent_number"]');
            var gender = $('select[name="sex"]');
            var city = $('select[name="city"]');
            var area = $('select[name="area"]');
            var address = $('input[name="address"]');
            var cellphone = $('input[name="cellphone"]');
            return !error_flag;
        }

        //---- password ----//
        $('input[name="password"]').on('blur', function () {
            checkPassword($(this).val());
        });

        function checkPassword(passwd) {
            var error_flag = false;
            var hint1 = '密碼長度必須超過六碼';
            var hint2 = '密碼必須包含至少 1個英文、 1個數字';
            var hint = new Array();
            if (passwd.length > 0) {
                if (passwd.length < 6) {
                    hint.push(hint1);
                    error_flag = true;
                }
                if (passwd.match(/\d+/) === null || passwd.match(/\D+/) === null) {
                    hint.push(hint2);
                    error_flag = true;
                }
                var message = hint.join('<br>');
                $('#password-hint').html(message);
            }
            if (error_flag) $('#password-hint').show(1000);
            else $('#password-hint').hide(1000, function (event) {
                var message = hint.join('<br>');
                $('#password-hint').html(message);
            });
            return !error_flag;
        }

        //---- MemberName ----//
        $('input[name="m_name"]').on('blur', function () {
            checkMemberName($(this).val());
        });

        function checkMemberName(name) {
            let error_flag = false;
            if (name.length < 1) {
                error_flag = true;
                $('#name-hint').show(1000);
            } else {
                $('#name-hint').hide(1000);
            }
            return !error_flag;
        }

        //---- MemberBorn ----//
        $('input[name="born"]').on('blur', function () {
            checkMemberBorn($(this).val());
        });

        function checkMemberBorn(born) {
            let error_flag = false;
            if (born.match(/^\d{4}-\d{2}-\d{2}$/) === null) {
                $('#born-hint').show(1000);
                error_flag = true;
            } else {
                $('#born-hint').hide(1000);
            }
            return error_flag;
        }

        //---- MemberIDCard ----//
        $('input[name="id_card"]').on('blur', function () {
            checkMemberIDCard($(this).val());
        });

        function checkMemberIDCard(idCode) {
            let error_flag = false;
            let natural = checkIDNumber(idCode);
            let juristic = checkGUINumber(idCode);
            let message = '';
            if (idCode.length === 0) {
                message = '統一編號為必填';
                error_flag = true;
            }
            if (natural === false && juristic === false) {
                message = '統一編號錯誤';
                error_flag = true;
            }
            return error_flag;
        }

        //---- 自然人驗證 ----//
        function checkIDNumber(idCode) {
            let tab = "ABCDEFGHJKLMNPQRSTUVXYWZIO"
            let A1 = new Array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3);
            let A2 = new Array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 1, 2, 3, 4, 5);
            let Mx = new Array(9, 8, 7, 6, 5, 4, 3, 2, 1, 1);
            let sum = 0;

            if (idCode.length != 10) return false;
            let i = tab.indexOf(idCode.charAt(0));
            if (i == -1) return false;
            sum += A1[i] + A2[i] * 9;

            for (i = 1; i < 10; i++) {
                let v = parseInt(idCode.charAt(i));
                if (isNaN(v)) return false;
                sum += v * Mx[i];
            }
            if (sum % 10 != 0) return false;
            return true;
        }

        //---- 法人驗證 ----//
        function checkGUINumber(idCode) {
            let invalidList = "00000000,11111111";
            if (/^\d{8}$/.test(idCode) == false || invalidList.indexOf(idCode) != -1) {
                return false;
            }

            let validateOperator = [1, 2, 1, 2, 1, 2, 4, 1],
                sum = 0,
                calculate = function (product) { // 個位數 + 十位數
                    let ones = product % 10,
                        tens = (product - ones) / 10;
                    return ones + tens;
                };
            for (let i = 0; i < validateOperator.length; i++) {
                sum += calculate(idCode[i] * validateOperator[i]);
            }

            return sum % 10 == 0 || (idCode[6] == "7" && (sum + 1) % 10 == 0);
        }

        function checkIDCardRe(idCode) {
            $.ajax();
        }

        function form_stop() {
            if ($('input[name="parent_name"]').val() !== '') {
                if (check_name($('input[name="parent_name"]').val()) !== true) {
                    $('input[name="parent_name"]').focus();
                    $('#parent-hint').show();
                    return false;
                }
            }
            if ($("#device").text() != 'mobile' && $("input[name='password']").val() == '') {
                $("input[name='password']").focus();
                $("#password-hint").show();
                return false;
            }

            if ($("input[name='m_name']").val() == '') {
                $("input[name='m_name']").focus();
                $("#name-hint").show();
                return false;
            }

            if ($("input[name='id_card']").val() == '') {
                $("input[name='id_card']").focus();
                $("#id_card-hint").text('出生年月日必填');
                $("#id_card-hint").show();
                return false;
            }

            if ($("input[name='id_card']").val() != '') {
                var id_card = $("input[name='id_card']").val();
                var id_card_reg = /^[0-9]{8}$/;
                if (!id_card_reg.test(id_card)) {
                    $("input[name='id_card']").focus();
                    $("input[name='id_card']").val('');
                    $("#id_card-hint").text('請填寫正確格式');
                    $("#id_card-hint").show();
                    return false;
                }
            }

            if ($("input[name='address']").val() == '') {
                $("input[name='address']").focus();
                $("#address-hint").show();
                return false;
            }

            if ($("input[name='cellphone']").val() == '') {
                $("input[name='cellphone']").focus();
                $("#phone-hint").text('手機為必填');
                $("#phone-hint").show();
                return false;
            }

            if ($("input[name='cellphone']").val() != '') {
                var phone = $("input[name='cellphone']").val();
                var cellphone_reg = /^09[0-9]{8}$/;
                if (!cellphone_reg.test(phone)) {
                    $("input[name='cellphone']").focus();
                    $("input[name='cellphone']").val('');
                    $("#phone-hint").text('請填寫正確格式');
                    $("#phone-hint").show();
                    return false;
                }
            }
        }

        function check_name(con) {
            var return_value;
            $.ajax({
                url: "ajax.php",
                type: "POST",
                async: false,
                data: {
                    type: 'checkname',
                    content: con
                },
                dataType: "json",
                success: function (rst) {
                    if (rst.msg === 'checked') {
                        // do something
                        return_value = true;
                    } else {
                        // do something
                        return_value = false;
                    }
                }
            });
            return return_value;
        }

        function change_flag(con) {
            if (con === 'checked') {
                tmp = true;
            }
        }

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
                    //alert("ajax失敗");
                }
            });
        });
    </script>
<?php
$password = isset($_POST['password']) ? $_POST['password'] : false;
$m_name = isset($_POST['m_name']) ? $_POST['m_name'] : false;
$id_card = isset($_POST['id_card']) ? $_POST['id_card'] : false; //出生年月日ex:19910101
$parent_nm = isset($_POST['parent_name']) ? $_POST['parent_name'] : false;
//@$birthday = $_POST['birthday'];
$sex = isset($_POST['sex']) ? $_POST['sex'] : false;
$city = isset($_POST['city']) ? $_POST['city'] : false;
$area = isset($_POST['area']) ? $_POST['area'] : false;
$address = isset($_POST['address']) ? $_POST['address'] : false;
$cellphone = isset($_POST['cellphone']) ? $_POST['cellphone'] : false;
//@$line = $_POST['line'];

if (@$_POST['btn'] && @$_SESSION["member_no"] != "") {
    $mid = get_mid($parent_nm);
    if ($_SESSION['device'] == 'desktop') {
        $sql = "UPDATE member SET";
        $sql .= " password='$password',";
        $sql .= " m_name='$m_name',";
        if ($mid !== false) {
            $sql .= " parent_no='$mid',";
        }
        $sql .= " id_card='$id_card',";
        $sql .= " sex='$sex',";
        $sql .= " city_id='$city',";
        $sql .= " area_id='$area',";
        $sql .= " address='$address',";
        $sql .= " cellphone='$cellphone'";
        $sql .= " WHERE id='$member_id'";
    } else {
        $sql = "UPDATE member SET";
        $sql .= " m_name='$m_name',";
        if ($mid !== false) {
            $sql .= " parent_no='$mid',";
        }
        $sql .= " id_card='$id_card',";
        $sql .= " sex='$sex',";
        $sql .= " city_id='$city',";
        $sql .= " area_id='$area',";
        $sql .= " address='$address',";
        $sql .= " cellphone='$cellphone'";
        $sql .= " WHERE id='$member_id'";
    }
    $rst = mysql_query($sql);
    if (empty(mysql_error())) {
        update_success();
    } else {
        update_failure();
    }
} else {
    if (@$_POST['btn'] && @$_SESSION["fb_id"] != "") {
        $sql = "UPDATE fb SET id_card='$id_card', sex='$sex', city_id='$city', area_id='$area', address='$address', cellphone='$cellphone' WHERE fb_id='" . $_SESSION["fb_id"] . "'";
        // mysql_query($sql);
        ?>
        <script>
            if ($("#device").text() == 'mobile') {
                window.javatojs.showInfoFromJs('儲存完畢');
            }
            else {
                alert('儲存完畢');
            }
            location.href = 'index.php?url=member_info';
        </script>
        <?php
    }
}
function update_success()
{
    ?>
    <script>
        if ($("#device").text() == 'mobile') {
            window.javatojs.showInfoFromJs('儲存完畢');
        }
        else {
            alert('儲存完畢');
        }
        location.href = 'index.php?url=member_info';
    </script>
    <?php
}

function update_failure()
{
    ?>
    <script>
        if ($("#device").text() == 'mobile') {
            window.javatojs.showInfoFromJs('儲存失敗，詳情請連絡相關人員');
        }
        else {
            alert('儲存失敗，詳情請連絡相關人員');
        }
        location.href = 'index.php?url=member_info';
    </script>
    <?php
}

function get_mid($m_nm = false)
{
    if ($m_nm !== false) {
        $sql = "select member_no from member";
        $sql .= " right join seller_manager on member_id = member_no";
        $sql .= " where m_name = '$m_nm';";
        $rst = mysql_query($sql);
        if (mysql_num_rows($rst) > 0) {
            $rst = mysql_fetch_array($rst);
            $rst = $rst['member_no'];
            return $rst;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

?>