<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
<?php
    include_once('mysql.php');
    sql();

	@$pid = $_GET['pid']; //父id
	if($pid == "")
	{
		@$a = $_POST['a'];
        @$a_url = $_POST['a_url'];
        @$a_icon = $_POST['a_icon'];
        @$a_visibility = $_POST['a_visibility'];

		@$sort = mysql_insert_id();
		if($sort ==0)
		{
			$sort = 1;
		}
		$sql = "SELECT COUNT(fn_set.id) AS total FROM fn_set";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
		{
			$sort = $row['total']+1;
		}

		$sql2 = "SELECT COUNT(fn_set.id) AS stotal FROM fn_set WHERE id=parent_id";
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

		$sql2 = "INSERT INTO fn_set SET id='$sort', name='$a', url='$a_url', icon='$a_icon', identity='$a_visibility', parent_id='$sort', sort='$sort2'";
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
                <tr>
                    <td height="79">連結路徑</td>
                    <td><input type="text" name="a_url" value="home.php?url=" /></td>
                </tr>
                <tr>
                    <td height="79">icon</td>
                    <td><input type="text" name="a_icon" /></td>
                </tr>
                <tr>
                    <td height="79">開放觀看權限</td>
                    <td>
                        <select name="a_visibility">
                            <option selected value="">請選擇</option>
                            <option value="all">所有人</option>
                            <option value="supplier">供應商</option>
                            <option value="store">智慧門市</option>
                            <option value="admin">總管理</option>
                        </select>
                    </td>
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
        @$b_url = $_POST['b_url'];
        @$b_visibility = $_POST['b_visibility'];

		@$sort = mysql_insert_id();
		if($sort ==0)
		{
			$sort = 1;
		}
		$sql = "SELECT MAX(fn_set.id) AS total FROM fn_set";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
		{
			$sort = $row['total']+1;  //子類別id計算
		}

		$sql2 = "SELECT COUNT(fn_set.id) AS stotal FROM fn_set WHERE id=".$pid." OR parent_id =".$pid."";
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
        
		$sql2 = "INSERT INTO fn_set SET id='$sort', name='$b', url='$b_url', identity='$b_visibility', parent_id='".$pid."',sort='$sort2'";
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
              <table width="75%" border="1">
                <tr>
                    <td colspan="2">新增分類</td>
                </tr>
                <tr>
                    <td height="79">次分類</td>
                    <td><input type="text" name="b" /></td>
                </tr>
                <tr>
                    <td height="79">連結路徑</td>
                    <td><input type="text" name="b_url" value="home.php?url=" /></td>
                </tr>
                <tr>
                    <td height="79">開放觀看權限</td>
                    <td>
                        <select name="b_visibility">
                            <option selected value="">請選擇</option>
                            <option value="all">所有人</option>
                            <option value="supplier">供應商</option>
                            <option value="store">智慧門市</option>
                            <option value="admin">總管理</option>
                        </select>
                    </td>
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
