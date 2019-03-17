<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2019/3/16
 * Time: 下午 04:06
 */

use Base17Mai\Member, Base17Mai\Bank,Base17Mai\Bonus;
use function Base17Mai\take;

if(isset($_GET['NEW'])) include 'bonus_modify_new.php';
else include 'bonus_modify_list.php';

$id = take('id', '', 'GET');
// if (empty($id)) exit('尚未選擇家長');
?>

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
