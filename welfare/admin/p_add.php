<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
<title>所有分類新增處理</title>
</head>

<body>
<?php
	include("mysql.php");
	sql();
	@$pid = $_GET['pid']; //父id
	if($pid == "")
	{
		@$a = $_POST['a'];

		@$sort = mysql_insert_id();
		if($sort ==0)
		{
			$sort = 1;
		}
		$sql = "SELECT COUNT(class.id) AS total FROM class";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
		{
			$sort = $row['total']+1;
		}
		
		$sql2 = "SELECT COUNT(class.id) AS stotal FROM class WHERE id=parent_id";
		$res2 = mysql_query($sql2);
		while($row2 = mysql_fetch_array($res2))
		{
			if($row2['stotal']==0)
			{
				@$sort2 =1;
			}
			else
			{
				@$sort2 = $row2['stotal']+1;
			}
		}
		
		$sql2 = "INSERT INTO class SET id='$sort', name='$a', parent_id='$sort', sort='$sort2'";
		if($a != "")
		{
			mysql_query($sql2);
			?>
			<script language="javascript">
				alert("新增成功");
				self.opener.location.reload();
				window.close(this);
			</script>
			<?php
		}
		?>
        <center>
            <form method="post">
                <table class="table" width="75%" border="1">
                  <tr>
                    <td colspan="2">新增分類</td>
                </tr>
                  <tr>
                    <td height="79">主分類</td>
                    <td><input type="text" name="a" /></td>
                </tr>
                  <tr align="center">
                    <td height="39" colspan="2"><input type="submit" class="btn btn-primary" value="新增" /></td>
                </tr>
              </table>
            </form>
        </center>
        <?php
	}
	else if($pid != "")
	{
		@$b = $_POST['b'];

		@$sort = mysql_insert_id();
		if($sort ==0)
		{
			$sort = 1;
		}
		$sql = "SELECT MAX(class.id) AS total FROM class";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
		{
			$sort = $row['total']+1;  //子類別id計算
		}
		
		$sql2 = "SELECT COUNT(class.id) AS stotal FROM class WHERE id=".$pid." OR parent_id =".$pid."";
		$res2 = mysql_query($sql2);
		while($row2 = mysql_fetch_array($res2))
		{
			if($row2['stotal']==0)
			{
				@$sort2 =1;
			}
			else
			{
				@$sort2 = $row2['stotal']; //子類別 排序層級計算
			}
			
		}
		
		$sql2 = "INSERT INTO class SET id='$sort', name='$b', parent_id='".$pid."',sort='$sort2'";
		if($b != "")
		{
			mysql_query($sql2);
			?>
			<script language="javascript">
				alert("新增成功");
				self.opener.location.reload();
				window.close(this);
			</script>
			<?php
		}
		?>
        <center>
            <form method="post">
                <table class="table" width="75%" border="1">
                  <tr>
                    <td colspan="2">新增分類</td>
                </tr>
                  <tr>
                    <td height="79">次分類</td>
                    <td><input type="text" name="b" /></td>
                </tr>
                  <tr align="center">
                    <td height="39" colspan="2"><input type="submit" class="btn btn-primary" value="新增" /></td>
                </tr>
              </table>
            </form>
        </center>
        <?php
	}
?>
</body>
</html>