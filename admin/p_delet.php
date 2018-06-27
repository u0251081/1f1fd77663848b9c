<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>刪除總處理</title>
</head>

<body>
<?php
	include("mysql.php");
	sql();
	@$id = $_GET['id'];
	$sql = "SELECT * FROM class WHERE id =".$id." OR parent_id ='".$id."'";
	$res = mysql_query($sql);
	$num = mysql_num_rows($res);
	if($num>1)
	{
		?>
			<script language="javascript">
				alert("此目錄上層或下層尚有資料，無法刪除，請先刪除上層或下層的資料");
				self.opener.location.reload();
				window.close(this);
			</script>
		<?php
	}
	else
	{
		$sql1 = "DELETE FROM class WHERE id = '".$id."'";
		mysql_query($sql1);
		?>
			<script language="javascript">
				alert("刪除成功");
				self.opener.location.reload();
				window.close(this);
			</script>
		<?php
	}
?>
</body>
</html>