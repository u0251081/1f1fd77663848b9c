<?php

use Base17Mai\Member, Base17Mai\Manager;

$Member = new Member();
$Manager = new Manager();
$Members = $Member->GetMemberInformation(['id', 'm_name', 'email', 'cellphone', 'parent_no', 'member_no']);
foreach ($Members as $key => $member) {
    $ManagerNO = $Manager->GetManagerInformation('member_id', ['manager_no' => $member['parent_no']]);
    if (isset($ManagerNO[0])) {
        $manager = $Member->GetMemberInformation(['m_name', 'email'], ['member_no' => $ManagerNO[0]['member_id']]);
        if (isset($ManagerName[0])) {
            $ManagerName = $manager[0]['m_name'];
            $ManagerEmail = $manager[0]['email'];
        } else {
            $ManagerName = '家長不存在';
            $ManagerEmail = '家長不存在';
        }
    } else {
        $ManagerName = '尚未加入團購家族';
        $ManagerEmail = '尚未加入團購家族';
    }
    $Members[$key]['ParentName'] = $ManagerName;
    $Members[$key]['ParentEmail'] = $ManagerEmail;
}
?>

<!--<a href="javascript:void(0);" class="btn btn-primary" onclick="insert_btn()">新增</a>-->
<table class="DataTable table responsive table-bordered">
    <thead>
    <tr>
        <h4 class="widgettitle" style="text-align: center;">會員列表</h4>
    </tr>
    <tr>
        <th style="text-align:center;">編號</th>
        <!--<th style="text-align:center;">fb會員id</th>-->
        <th style="text-align:center;">團購家族-家長</th>
        <th style="text-align:center;">會員帳號</th>
        <th style="text-align:center;">會員姓名</th>
        <th style="text-align:center;">聯絡電話</th>
        <!--<th style="text-align:center;">地址</th>-->
        <th style="text-align:center;">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($Members as $member): ?>
        <tr>
            <td><?= $member['id'] ?></td>
            <td><?= $member['ParentName'] ?></td>
            <td><?= $member['email'] ?></td>
            <td><?= $member['m_name'] ?></td>
            <td><?= $member['cellphone'] ?></td>
            <td>
                <a href="javascript:void(0);" onclick="edit_fun(<?= $member['id'] ?>)" class="btn"
                   style="color:#fff; background: green;">
                    <i class="iconsweets-bandaid iconsweets-white"></i>修改
                </a>
                <a href="javascript:void(0);" onclick="check_fun(<?= $member['id'] ?>,<?= $member['member_no'] ?>);"
                   class="btn" style="color:#fff; background: green;">
                    <i class="iconsweets-book iconsweets-white"></i>訂單
                </a>
                <a href="javascript:void(0);" onclick="delete_fun(<?= $member['id'] ?>)" class="btn btn-danger"
                   style="color:#fff;">
                    <i class="iconsweets-trashcan iconsweets-white"></i>刪除
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php
    /*
    $total = 10; //預設每筆頁數
    $page = 1; //預設初始頁數

    //--------以上兩樣即為 limit 0,3 ==($page,$total)

    if ($page < 1 || $page == "") {
        $page = 1; //初始頁數
    }

    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    }
    $sql = "SELECT * FROM member ORDER BY id DESC";
    //echo $sql;

    $res = mysql_query($sql);
    $row = mysql_num_rows($res); //透過mysql_num_rows()取得總頁數
    $maxpage = ceil($row / $total); //*計算總頁數=(總筆數/每頁筆數)後無條件進位避免 總筆數與總頁數除不盡時剩餘頁無法顯示

    if ($page > $maxpage) {
        $page = $maxpage; //如果初始頁數大於總頁數就等於總頁數
    }

    $start = $total * ($page - 1); // 初始頁數 =每筆頁數*(頁預設數-1)
    $sql = "SELECT * FROM member ORDER BY id DESC LIMIT " . $start . "," . $total;
    $res = mysql_query($sql); //將查詢資料存到 $result 中
    $row = mysql_num_rows($res);
    for ($i = 1; $i <= $row; $i++) {
        $ary[$i] = mysql_fetch_array($res);
        $condition = empty($ary[$i]['parent_no']) ? 'false' : 'member_no = \'' . $ary[$i]['parent_no'] . '\'';
        $sql2 = 'select m_name from member where ' . $condition . ';';
        $res2 = mysql_query($sql2); //將查詢資料存到 $result 中
        $row2 = mysql_fetch_array($res2);
        $ary[$i]['parent_name'] = isset($row2['m_name']) ? $row2['m_name'] : '尚未設定';
        //$ary[$i]['parent_name'] = $sql2;
    }

    for ($i = 1; $i <= $row; $i++) {
        ?>
        <tr align="center">
            <td style="width:3%; text-align:center;"><?= $ary[$i]['id'] ?></td>
            <!--<td style="width:5%; text-align:center;"><?= $ary[$i]['fb_id'] === '' ? '' : $ary[$i]['fb_id'] ?></td>-->
            <td style="width:5%; text-align:center;"><?= $ary[$i]['parent_name'] ?></td>
            <td style="width:5%; text-align:center;"><?= $ary[$i]['email'] ?></td>
            <td style="width:5%; text-align:center;"><?= $ary[$i]['m_name'] ?></td>
            <td style="width:8%;"><?php echo $ary[$i]['cellphone']; ?></td>
            <!--
            <td style="width:15%">
                <?php
            $city_sql = "SELECT city FROM city WHERE id='" . $ary[$i]['city_id'] . "'";
            $city_res = mysql_query($city_sql);
            $city_row = mysql_fetch_array($city_res);

            $area_sql = "SELECT area FROM area WHERE id='" . $ary[$i]['area_id'] . "'";
            $area_res = mysql_query($area_sql);
            $area_row = mysql_fetch_array($area_res);

            echo $city_row['city'] . $area_row['area'] . $ary[$i]['address'];
            ?>
            </td>
            -->
            <td width="13%">
                <a href="javascript:void(0);" onclick="edit_fun(<?php echo $ary[$i]['id']; ?>)" class="btn"
                   style="color:#fff; background: green;">
                    <i class="iconsweets-bandaid iconsweets-white"></i>修改
                </a>
                <a href="javascript:void(0);" onclick="check_fun(<?php echo $ary[$i]['id'];
                echo ',';
                echo $ary[$i]['member_no']; ?>)" class="btn"
                   style="color:#fff; background: green;">
                    <i class="iconsweets-book iconsweets-white"></i>訂單
                </a>
                <a href="javascript:void(0);" onclick="delete_fun(<?php echo $ary[$i]['id']; ?>)" class="btn btn-danger"
                   style="color:#fff;">
                    <i class="iconsweets-trashcan iconsweets-white"></i>刪除
                </a>
            </td>
        </tr>
        <?php
    }
    */
    ?>
    </tbody>
    <!--
    <table width="90%">
        <tr>
            <td align="center">
                <br/>
                <?php
    if ($page == 1) {
        ?>
                    上一頁&nbsp;&nbsp;
                    <?php
    } else {
        ?>
                    <a style='text-decoration:none;'
                       href="?url=member&page=<?php echo $page - 1; ?>">上一頁&nbsp;&nbsp;</a>
                <?php } ?>

                <?php
    echo "<select onChange='location = this.options[this.selectedIndex].value;' class='span1'>";
    for ($i = 1; $i <= $maxpage; $i++) {
        if ($i == $page) {
            echo "<option selected='selected' value=" . $i . ">" . $i . "</option>";
        } else {
            echo "<option value=home.php?url=member&page=" . $i . ">" . $i . "</option>";
        }
    }
    echo "</select>";
    ?>

                <?php
    if ($page == $maxpage) {
        ?>
                    &nbsp;&nbsp;下一頁
                    <?php
    } else {
        ?>
                    <a style='text-decoration:none;'
                       href="?url=member&page=<?php echo $page + 1; ?>">&nbsp;&nbsp;下一頁</a>
                <?php } ?>
                <br/>
            </td>
        </tr>
    </table>
    -->
</table>


<script>
    $(document).ready(function () {
        DTable.order([0, 'desc']).draw();
    });

    function insert_btn() {
        location.href = 'home.php?url=add_member';
    }

    function edit_fun(id) {
        location.href = 'home.php?url=edit_member&id=' + id;
    }

    function check_fun(id, member_no) {
        location.href = 'home.php?url=check_member&id=' + id + '&member_no=' + member_no;
    }

    function delete_fun(id) {
        if (confirm("確定要刪除?")) {
            location.href = 'home.php?url=delete_fun&d_id=' + id + '&pg=member';
        }
    }
</script>
