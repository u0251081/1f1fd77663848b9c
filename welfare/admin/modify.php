<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
<title>總修改頁面處理</title>
</head>

<body>
<?php
    include_once('mysql.php');
    sql();
    
	@$id = $_GET['id']; //修改欄位的id
	@$pid = $_GET['pid']; //父類別id
	@$sid = $_GET['sid']; //排序id
	
	@$txt = $_POST['txt']; //修改的分類字串
	@$sort = $_POST['sort']; //排序id修改欄位
    @$m_visibility = $_POST['m_visibility'];

	$sql = "SELECT * FROM fn_set WHERE id = '".$id."'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	
	$sql ="UPDATE fn_set SET name='$txt', sort='$sort', `identity`='$m_visibility' WHERE id='$id'";
	
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
    	<table class="table table-hover" width="75%" border="1">
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
						$sql1 = "SELECT COUNT(fn_set.id) AS tt FROM fn_set WHERE id=".$id." AND parent_id=".$id.""; //算出是否為 父類別
						$res1 = mysql_query($sql1);
						$row1 = mysql_fetch_array($res1);
						if($row1['tt'] ==1)
						{
							$sql1 = "SELECT COUNT(fn_set.id) AS tt FROM fn_set WHERE id=parent_id";
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
							$sql1 = "SELECT COUNT(fn_set.id) AS tt FROM fn_set WHERE id!=parent_id AND parent_id =".$pid.""; //算一個父類中有幾個子類別
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
            <tr>
                <td height="79">開放觀看權限</td>
                <td>
                    <select name="m_visibility">
                        <?php
                            $sql = "SELECT * FROM fn_set WHERE id = '".$_GET['id']."' AND parent_id = '".$_GET['pid']."'";
                            $res = mysql_query($sql);
                            $row = mysql_fetch_array($res);
                        ?>
                        <option selected value="">請選擇</option>
                        <option value="all" <?php if($row['identity'] == 'all')echo "selected"; ?>>所有人</option>
                        <option value="supplier" <?php if($row['identity'] == 'supplier')echo "selected"; ?>>供應商</option>
                        <option value="store" <?php if($row['identity'] == 'store')echo "selected"; ?>>智慧門市</option>
                        <option value="admin" <?php if($row['identity'] == 'admin')echo "selected"; ?>>總管理</option>
                    </select>
                </td>
            </tr>
            <tr align="center">
                <td colspan="2"><input type="submit" class="btn" style="color: #fff; background: green;" value="修改" /></td>
            </tr>
        </table>
     </form>
</center>
</body>
</html>