<?php
/**
 * Created by PhpStorm.
 * User: andychen
 * Date: 9/4/18
 * Time: 11:51 PM
 */

use Base17Mai\Administrator;

$Administrator = new Administrator();
$sysConfigs = $Administrator->GetSysConfig();
$threshold = $sysConfigs['threshold'];
$angelValue = $sysConfigs['angelValue'];
$storeFee = $sysConfigs['storeFee'];
?>

<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<div class="widget">
    <h4 class="widgettitle">獎金參數設置</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post">
            <p>
                <label>有效消費額</label>
                <span class="field">
                    <input type="number" min="0" name="threshold" value="<?= $threshold ?>">&nbsp;&nbsp;&nbsp;元
                </span>
            </p>
            <p>
                <label>天使獎勵</label>
                <span class="field">
                    <input type="number" min="0" name="angelValue" value="<?= $angelValue ?>">&nbsp;&nbsp;&nbsp;元
                </span>
            </p>
            <p>
                <label>店家回饋比率</label>
                <span class="field">
                    <input type="number" min="0" name="storeFee" value="<?= $storeFee ?>">&nbsp;&nbsp;&nbsp;%
                </span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="span1 btn btn-primary" value="提交">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="cancel" class="span1 btn btn-default" value="重設">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->
<script>
    var cnt = 0;

    $(document).ready(function () {
    });

    $(document).on('click', 'input[type="cancel"]', function () {
        if (confirm('確認還原上次設定？')) ajax17mai('Administrator', 'ResetSys');
    });

    $(document).on('submit', 'form', function () {
        let inputValue = getFormData($(this));
        if (confirm('確認儲存本次設定？')) ajax17mai('Administrator', 'SaveSys', {}, inputValue);
        return false;
    });


</script>