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
        #draggable {
        background-color: white;
        border: 1px solid #cccccc;
        border-collapse: collapse;
        color: black;
        text-align: center;
        width: 100%;
    }
    #draggable th {
        background-color: white;
        border: 1px solid #cccccc;
        color: black;
        padding: 5px;
    }
    #draggable td {
        border: 1px solid #cccccc;
        cursor: move;
        padding: 5px;
    }
    </style>
</head>

<body>
<?php
    include_once "mysql.php";
    sql();
	@$pid = $_GET['pid']; //父id
	if($pid == "")
	{
		?>
        <center>
            <table class="table table-hover" id="draggable">
                <tr>
                    <h4 class="widgettitle">商品分類列表<input type="button" id="class_btn"  onclick="add()" value="新增" /></h4>
                </tr>
                <tr>
                <td align="center">編號</td>
                  <td align="center">排序位置</td>         	
                	<td align="center">名稱</td>
                	<td align="center">操作</td>
               	</tr>
        <?php
		$sql = "SELECT * FROM class WHERE id = parent_id ORDER by sort";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res)) 
		{
			?>
				<tr align="center" draggable="true" class="data">
				  <td width="10%" align="center" class="id" ><?php echo $row['id'];?></td>
					<td width="16%" align="center" class="sort" ><?php echo $row['sort']; ?></td>
					<td width="51%">
                    	<a href="home.php?url=p_class&pid=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a>
                    </td>
					<td align="center" width="23%">
                    	<input type="button" class="btn span1" style="color:#fff; background: green;" value="修改" onclick=window.open('p_modify.php?id=<?php echo $row['id']; ?>&sid=<?php echo $row['sort']; ?>','',"width=800","height=600") />
                        &nbsp;&nbsp;&nbsp;
                        <input type="button" class="btn btn-danger span1" value="刪除" onclick="delet('<?php echo $row['id']; ?>')" />
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
                    <h4 class="widgettitle">商品分類列表</h4>
              	</tr>
                <tr>
                  <td align="center">編號</td>
                	<td align="center">排序位置</td>
                	<td align="center">名稱</td>
                	<td align="center">操作</td>
               	</tr>
              <?php
                $sql = "SELECT * FROM class WHERE parent_id = ".$pid." AND id != parent_id ORDER BY sort";
                $res = mysql_query($sql);
				$rnum = mysql_num_rows($res);
				if($rnum > 0)
				{
					while($row = mysql_fetch_array($res)) 
					{
					?>
						<tr align="center" draggable="true" class="data">
						  <td width="10%" align="center" class="id"><?php echo $row['id'];?></td>
							<td width="16%" align="center" class="sort"><?php echo $row['sort']; ?></td>
							<td width="51%"><a href="home.php?url=p_class&pid=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></td>
							<td align="center" width="23%">
								<input type="button" class="btn span1" style="color:#fff; background: green;" value="修改" onclick=window.open('p_modify.php?id=<?php echo $row['id']; ?>&pid=<?php echo $row['parent_id']; ?>&sid=<?php echo $row['sort']; ?>','',"width=800","height=600") />
								&nbsp;&nbsp;&nbsp;
								<input type="button" class="btn btn-danger span1" value="刪除" onclick="delet('<?php echo $row['id']; ?>')" />
                                
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
                	<input type="button" class="btn btn-primary" onclick="add(<?php echo $pid; ?>)" value="新增" />&nbsp;&nbsp;
                	<input type="button" class="btn" value="回上層" onclick="window.history.back();" />
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
			window.open("p_add.php",'',"width=800","height=600");
		}
		else
		{
			window.open("p_add.php?pid="+str+"",'',"width=800","height=600");
		}
	}
	
	function delet(id)
	{
		var reply = confirm('請問是否真的要刪除?'); 
		if(reply == true)
		{
			window.open('p_delet.php?id='+id,'',"width=800","height=600");
		}
	}


/*
 * HTML Drag and Drop API（拖、放操作 API）
 */
var dragged;  // 保存拖動元素的引用（ref.），就是拖動元素本身

// 當開始拖動一個元素或一個選擇文本的時候 dragstart 事件就會觸發（設定拖動資料和拖動用的影像，且當從 OS 拖動檔案進入瀏覽器時不會觸發）
document.addEventListener('dragstart', function(event) {
    dragged = event.target;
    event.target.style.backgroundColor = 'rgba(240, 240, 240, 100)';
    event.target.style.color = 'rgba(255, 255, 255, 100)';
}, false);

// 不論結果如何，拖動作業結束當下，被拖動元素都會收到一個 dragend 事件（當從 OS 拖動檔案進入瀏覽器時不會觸發）
document.addEventListener('dragend', function(event) {
    // 重置樣式
    event.target.style.backgroundColor = 'white';
    event.target.style.color = 'black';
}, false);

// 當一個元素或者文本被拖動到有效放置目標 dragover 事件就會一直觸發（每隔幾百毫秒）
// 絕大多數的元素預設事件都不准丟放資料，所以想要丟放資料到元素上，就必須取消預設事件行為
// 取消預設事件行為能夠藉由呼叫 event.preventDefault 方法
document.addEventListener('dragover', function(event) {
    // 阻止預設事件行為
    event.preventDefault();
}, false);

// 當拖動的元素或者文本進入一個有效的放置目標 dragenter 事件就會觸發
document.addEventListener('dragenter', function(event) {
    // 當拖動的元素進入可放置的目標（自訂符合條件），變更背景顏色
    // 自訂條件：class 名稱 && 不是本身的元素
    if (event.target.parentNode.className == 'data' &&
        dragged !== event.target.parentNode) {
        dragged.style.background = 'red';

        // 判斷向下或向上拖動，來決定在元素前或後插入元素
        if (dragged.rowIndex < event.target.parentNode.rowIndex) {
            event.target.parentNode.parentNode.insertBefore(dragged, event.target.parentNode.nextSibling);
        }
        else {
            event.target.parentNode.parentNode.insertBefore(dragged, event.target.parentNode);
        }
    }
}, false);

// 當拖動的元素或者文本離開有效的放置目標 dragleave 事件就會觸發
document.addEventListener('dragleave', function(event) {
    // 當拖動元素離開可放置目標節點，重置背景
    // 自訂條件：class 名稱 && 不是本身的元素
    if (event.target.parentNode.className == 'data' &&
        dragged !== event.target.parentNode) {
        // 當拖動元素離開可放置目標節點，重置背景
        event.target.parentNode.style.background = '';
    }
}, false);

// 當丟放拖動元素到拖拉目標區時 drop 事件就會觸發；此時事件處理器可能會需要取出拖拉資料並處理之
// 這個事件只有在被允許下才會觸發，如果在使用者取消拖拉操作時，如按 ESC 鍵或滑鼠不是在拖拉目標元素上，此事件不會觸發
document.addEventListener('drop', function(event) {
    /*
     * AJAX Update DB
     */
    var id = document.querySelectorAll('.id');
    var data = [];  // 儲存所有 ID
    
    for (var i = 0, len = id.length; i < len; i++) {
        // 取得所有 ID 並存為 array
        data.push(id[i].innerHTML);

        // 重新排序表格 Sort 數值
        id[i].parentNode.querySelector('.sort').innerHTML = i;
    }
    console.log(data);
    // jQuery AJAX
    $.get('p_class_ajax.php', {"data": data});
}, false);
</script>

</html>