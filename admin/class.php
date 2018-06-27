<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>無限層總頁面處理</title>
<link rel="stylesheet" href="prettify/prettify.css" type="text/css">
<style>
    #class_btn
    {
        float:left; 
        position:relative;
        bottom:3px;
        right:6px;
    }
</style>
</head>

<body>
<?php
    include_once('mysql.php');
    sql();
    
	@$pid = $_GET['pid']; //父id
	if($pid == "")
	{
		?>
        <center>
        	<table class="table table-hover">
           		<tr>
           		  <h4 class="widgettitle">分類列表<input type="button" id="class_btn" onclick="add()" value="新增" /></h4>
       		  	</tr>
                <tr>
                  <td align="center">編號</td>
                	<td align="center">排序位置</td>
                	<td align="center">名稱</td>
                	<td align="center">操作</td>
               	</tr>
        <?php
		$sql = "SELECT * FROM fn_set WHERE id = parent_id ORDER BY sort";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
		{
			?>
				<tr align="center">
				  <td width="10%" align="center"><?php echo $row['id'];?></td>
					<td width="16%" align="center"><?php echo $row['sort']; ?></td>
					<td width="51%">
                    	<a href="home.php?url=class&pid=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a>
                    </td>
					<td align="center" width="23%">
                    	<input type="button" class="btn" style="color: #fff; background: green;" value="修改" onclick="window.open('modify.php?id=<?php echo $row['id']; ?>&sid=<?php echo $row['sort']; ?>','',width='800',height='600')" />
                        &nbsp;&nbsp;&nbsp;
                        <input type="button" class="btn btn-danger" value="刪除" onclick="delet('<?php echo $row['id']; ?>')" />
                    </td>
				</tr>
			<?php
		}
		?>
            </table>
		</center>
    <?php
	}
	else if($pid != "")
	{
		?>
		<center>
            <table class="table table-hover">
           		<tr>
                    <h4 class="widgettitle">分類列表</h4>
              	</tr>
                <tr>
                  <td align="center">編號</td>
                	<td align="center">排序位置</td>
                	<td align="center">名稱</td>
                	<td align="center">操作</td>
               	</tr>
              <?php
                $sql = "SELECT * FROM fn_set WHERE parent_id = ".$pid." AND id != parent_id ORDER BY sort";
                $res = mysql_query($sql);
				$rnum = mysql_num_rows($res);
				if($rnum > 0)
				{
					while($row = mysql_fetch_array($res)) 
					{
					?>
						<tr align="center">
						  <td width="10%" align="center"><?php echo $row['id'];?></td>
							<td width="16%" align="center"><?php echo $row['sort']; ?></td>
							<td width="51%"><a href="home.php?url=class&pid=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></td>
							<td align="center" width="23%">
								<input type="button" value="修改" class="btn" style="color: #fff; background: green;"  onclick="window.open('modify.php?id=<?php echo $row['id']; ?>&pid=<?php echo $row['parent_id']; ?>&sid=<?php echo $row['sort']; ?>','',width='800',height='600')" />
								&nbsp;&nbsp;&nbsp;
								<input type="button" value="刪除" class="btn btn-danger" onclick="delet('<?php echo $row['id']; ?>')" />
						  </td>
						</tr>
					<?php
					}
				}
				else
				{
					?>
                    <tr>
						<td colspan="4"><font color="#FF0000">此目錄已經到底，請返回上一層</font></td>
                    </tr>
                    <?php
				}
					?>
              <tr>
                <td align="center" colspan="4">
                	<input type="button" onclick="add(<?php echo $pid; ?>)" class="btn btn-primary" value="新增" />&nbsp;&nbsp;
                	<input type="button" value="回上層" onclick="window.history.back();" class="btn" />
                </td>
              </tr>
           </table>
        </center>
        <?php
	}
 ?>
</body>
<script>
	function add(str)
	{
		if(isNaN(str))
		{
			window.open("add.php",'',"width=800","height=600");
		}
		else
		{
			window.open("add.php?pid="+str+"",'',"width=800","height=600");
		}
	}
	
	function delet(id)
	{
		var reply = confirm('請問是否真的要刪除?'); 
		if(reply == true)
		{
			window.open('delet.php?id='+id,'',"width=800","height=600");
		}
	}
</script>
</html>