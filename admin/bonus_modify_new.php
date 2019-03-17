<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2019/3/16
 * Time: 下午 04:56
 */

use Base17Mai\Administrator, Base17Mai\Manager, Base17Mai\Member;

$Manager = new Manager();
$Member = new Member();
$S_id = Administrator::GetAdminName();
$Managers = $Manager->ListManager(['manager_no', 'member_id']);
foreach ($Managers as $key => $manager) {
    $member_information = $Member->GetInformation('email', $manager['member_id']);
    if (isset($member_information[0])) $Managers[$key] = array_merge($manager, $member_information[0]);
    else unset($Managers[$key]);
}
?>

<div class="widget">
    <h4 class="widgettitle">新增獎金異動</h4>
    <div class="widgetcontent">
        <form id="BonusModify">
            <div class="form-group">
                <label for="recorder">記錄人員</label>
                <input id="recorder" type="text" readonly value="<?= $S_id ?>">
            </div>
            <div class="form-group">
                <label for="recordMonth">記錄月份</label>
                <input id="recordMonth" type="month" class="form-control" name="recordMonth"
                       value="<?= date('Y-m') ?>" aria-describedby="MonthDescription">
                <small id="MonthDescription" class="form-text text-muted">只接受未來或現在月份</small>
            </div>
            <div class="form-group">
                <label for="Manager">選擇對象</label>
                <select id="Manager" class="form-control" name="Manager">
                    <option value="none" disabled selected>請選擇經理</option>
                    <?php foreach ($Managers as $item): ?>
                        <option value="<?= $item['manager_no'] ?>"><?= $item['email'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="bonus">調整點數</label>
                <input id="bonus" class="form-control" type="number" aria-describedby="bonusDescription" name="bonus">
                <small id="bonusDescription" class="form-text text-muted">如欲扣除請加上負號(-)</small>
            </div>
            <div class="form-group">
                <label for="reason">調整原因</label>
                <input type="text" class="form-control" id="reason" name="reason" placeholder="請輸入原因">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->
<script type="text/javascript">
    // jquery 3.3.1 on([selector], [event], callback);
    // jquery 1.9.1 on([event], [selector], callback);
    $(document).on('submit', 'form#BonusModify', function () {
        if (confirm('上傳後將無法修改、刪除，請問是否確認上傳？')) {
            let inputValue = getFormData($(this));
            ajax17mai('Bonus', 'BonusModify', {}, inputValue);
        }
        return false;
    });
</script>