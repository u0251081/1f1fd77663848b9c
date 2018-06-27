<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />.
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
<title>總修改頁面處理</title>
</head>

<body>
<?php
	include("mysql.php");
	sql();
	@$id = $_GET['id']; //修改欄位的id
	@$pid = $_GET['pid']; //父類別id
	@$sid = $_GET['sid']; //排序id
	
	@$txt = $_POST['txt']; //修改的分類字串
	@$sort = $_POST['sort']; //排序id修改欄位
	
	$sql = "SELECT * FROM class WHERE id = '".$id."'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	
	$sql ="UPDATE class SET `name`='$txt', sort='$sort' WHERE id='$id'";
	
	if($txt!="")
	{
		mysql_query($sql);
		?>
			<script language="javascript">
				alert("修改成功");
				self.opener.location.reload();
				window.close(this);
			</script>
		<?php
	}
?>
<center>
	<form method="post">
        <table class="table table-hover">
        	<tr>
                <h4 class="widgettitle">修改分類</h4>
            </tr>
            <tr>
              <td>原始名稱</td>
              <td>
              	<?php
					echo $row['name'];
				?>
              </td>
            </tr>
            <tr>
              <td>排序</td>
              <td>
              	<select name="sort">
              	<?php
					if($id != "")
					{
						$sql1 = "SELECT COUNT(class.id) AS tt FROM class WHERE id=".$id." AND parent_id=".$id.""; //算出是否為 父類別
						$res1 = mysql_query($sql1);
						$row1 = mysql_fetch_array($res1);
						if($row1['tt'] ==1)
						{
							$sql1 = "SELECT COUNT(class.id) AS tt FROM class WHERE id=parent_id";
							$res1 = mysql_query($sql1);
							$row1 = mysql_fetch_array($res1);
							for($i=1;$i<=$row1['tt'];$i++)
							{
								if($i==$sid)
								{
									echo "<option selected='selected' value='".$i."'>".$i."</option>";
								}
								else
								{
									echo "<option value='".$i."'>".$i."</option>";
								}
							}
						}
						else
						{
							$sql1 = "SELECT COUNT(class.id) AS tt FROM class WHERE id!=parent_id AND parent_id =".$pid.""; //算一個父類中有幾個子類別
							$res1 = mysql_query($sql1);
							$row1 = mysql_fetch_array($res1);
							for($i=1;$i<=$row1['tt'];$i++)
							{
								if($i==$sid)
								{
									echo "<option selected='selected' value='".$i."'>".$i."</option>";
								}
								else
								{
									echo "<option value='".$i."'>".$i."</option>";
								}
							}
						}
					}
				?>
                </select>
              </td>
            </tr>
            <tr>
                <td>新分類名稱</td>
                <td><input type="text" name="txt" value="<?php echo $row['name'];?>" /></td>
            </tr>
            <tr align="center">
                <td colspan="2"><input type="submit" class="btn btn-primary" value="修改" /></td>
            </tr>
        </table>
     </form>
</center>
</body>
</html>