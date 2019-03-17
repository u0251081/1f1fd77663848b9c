<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2019/3/16
 * Time: 下午 04:55
 */

use Base17Mai\Bonus, Base17Mai\Administrator, Base17Mai\Manager, Base17Mai\Member;

$Bonus = new Bonus();
$Administrator = new Administrator();
$Manager = new Manager();
$Member = new Member();
$ListBonusModify = $Bonus->ListBonusModify();
foreach ($ListBonusModify as $key => $item) {
    $AdminName = $Administrator->GetAdminInformation('name', ['s_id' => $item['StaffID']]);
    if (isset($AdminName[0]['name'])) $ListBonusModify[$key]['Admin'] = $AdminName[0]['name'];
    else unset($ListBonusModify[$key]);
    $ManagerMNO = $Manager->GetManagerInformation('member_id', ['manager_no' => $item['ManagerNO']]);
    if (isset($ManagerMNO[0]['member_id'])) {
        $MemberEmail = $Member->GetMemberInformation('email', ['member_no' => $ManagerMNO[0]['member_id']]);
        if (isset($MemberEmail[0])) $ListBonusModify[$key]['Manager'] = $MemberEmail[0]['email'];
        else unset($ListBonusModify[$key]);
    } else unset($ListBonusModify[$key]);
    $ListBonusModify[$key]['ModMonth'] = substr($item['ModMonth'], 0, -3);
}

?>
    <a href="javascript:void(0);" class="btn btn-primary" id="add_bonus_modify">新增</a>
    <div class="widget">
        <h4 class="widgettitle">獎金異動紀錄</h4>
        <div class="widgetcontent">
            <table class="DataTable table responsive table-bordered">
                <thead>
                <tr>
                    <th align="center">編號</th>
                    <th align="center">紀錄人員</th>
                    <th align="center">紀錄月份</th>
                    <th align="center">會員</th>
                    <th align="center">點數</th>
                    <th align="center">操作</th>
                </tr>
                </thead>
                <tbody id="tableBody">
                <?php foreach ($ListBonusModify as $item): ?>
                    <tr>
                        <td align="center"><?= $item['id'] ?></td>
                        <td align="center"><?= $item['Admin'] ?></td>
                        <td align="center"><?= $item['ModMonth'] ?></td>
                        <td align="center"><?= $item['Manager'] ?></td>
                        <td align="center"><?= $item['bonus'] ?></td>
                        <td align="center">
                            <a href="javascript:void(0);" onclick="view_fun(<?= $item['id'] ?>)" class="btn"
                               style="color:#fff; background: green;">
                                <i class="iconsweets-documents iconsweets-white"></i>檢視
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div><!--widgetcontent-->
    </div><!--widget-->
    <script type="text/javascript">
        $(document).on('click', '#add_bonus_modify', function () {
            location.href = 'home.php?url=bonus_modify&NEW';
        });

        function view_fun(id = false) {
            ajax17mai('Bonus', 'BonusModifyV', {}, {id: id});
        }
    </script>
<?php
$context = file_get_contents('template/Modal.html');
// print $context;
?>