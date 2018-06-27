<?php
@session_start();
header("Content-Type:text/html; charset=utf-8");

unset($_SESSION['front_id']);
unset($_SESSION['front_identity']);
unset($_SESSION['manager_no']);
unset($_SESSION["member_no"]);
unset($_SESSION['device']);
?>
<script>
	alert('登出成功');
	window.location.href="index.php";
</script>
