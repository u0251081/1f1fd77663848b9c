<a href="#myModal" data-toggle="modal" class="btn btn-primary" style="color:#fff;">推播設定</a>
<input type="checkbox" id="set_all">全選

<table class="table responsive table-bordered">
    <thead>
    <tr>
        <h4 class="widgettitle" style="text-align: center;">可推播會員列表</h4>
    </tr>
    <tr>
        <th align="center">編號</th>
        <th align="center">會員帳號</th>
        <th align="center">會員姓名</th>
        <th align="center">狀態</th>
        <th align="center">選擇</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sql = "SELECT * FROM member";
    //echo $sql;
    $res = mysql_query($sql);
    while ($row = mysql_fetch_array($res))
    {
        ?>
        <tr align="center">
            <td width="5%" align="center"><?php echo $row['id']; ?></td>
            <td width="10%" align="center"><?php echo $row['email']; ?></td>
            <td width="10%" align="center"><?php echo $row['m_name']; ?></td>
            <td width="5%">
                <?php
                if($row['status'] == 1)
                {
                    echo '啟用';
                }
                else
                {
                    echo '停用';
                }
                ?>
            </td>
            <td width="2%">
                <input type="checkbox" name="reg_id" value="<?php echo $row['reg_id']; ?>">
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>

<div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal hide fade in" id="myModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="myModalLabel">標題</h3>
    </div>
    <div class="modal-body">
        <input type="text" name="input5" id="push_msg" style="width: 500px;" placeholder="推播內容">
    </div>
    <div class="modal-footer">
        <button data-dismiss="modal" class="btn">取消</button>
        <button class="btn btn-primary" id="push_set">送出</button>
    </div>
</div><!--#myModal-->


<script>
    $("#push_set").click(function()
    {
        var reg_id_val = new Array();
        $('input:checkbox:checked[name="reg_id"]').each(function(i)
        {
            reg_id_val[i] = this.value;
        });

        var push_msg = $("#push_msg").val();
        if(reg_id_val != "")
        {
            $.ajax
            ({
                url:"sever_ajax.php", //接收頁
                type:"POST", //POST傳輸
                data:{type:"push_action", reg_id_val:reg_id_val, push_msg:push_msg}, // key/value
                dataType:"text", //回傳形態
                success:function(i) //成功就....
                {
					if(i > 0)
                    {
                        alert('推播成功');
                        location.reload();
                    }
                },
                error:function()//失敗就...
                {
                    //alert("ajax失敗");
                }
            });
        }
        else
        {
            alert('請勾選要推播的會員');
        }
    });

    //全選/取消全選
    $("#set_all").click(function()
    {
        if($("#set_all").prop("checked"))
        {
            $('input:checkbox[name="reg_id"]').each(function()
            {
                $(this).prop('checked',true);
                $(this).parent('span').addClass('checked');
            });
        }
        else
        {
            $('input:checkbox[name="reg_id"]').each(function()
            {
                $(this).prop('checked',false);
                $(this).parent('span').removeClass('checked');
            });
        }
    });
</script>
