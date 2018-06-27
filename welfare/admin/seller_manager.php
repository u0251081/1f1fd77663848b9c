<!--<a href="javascript:void(0);" class="btn btn-primary" onclick="insert_btn()">新增</a>-->

<table class="table responsive table-bordered">
    <thead>
    <tr>
        <h4 class="widgettitle" style="text-align: center;">行銷經理列表</h4>
    </tr>
    <tr>
        <th align="center">編號</th>
        <th align="center">行銷經理編號</th>
        <th align="center">姓名</th>
        <th align="center">聯絡電話</th>
        <th align="center">申請原因</th>
        <th align="center">申請時間</th>
        <th align="center">申請狀態</th>
        <th align="center">分潤金額</th>
        <th align="center">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total=10; //預設每筆頁數
    $page=1; //預設初始頁數

    //--------以上兩樣即為 limit 0,3 ==($page,$total)

    if($page<1  || $page=="")
    {
        $page=1; //初始頁數
    }

    if(isset($_GET['page']))
    {
        $page=$_GET['page'];
    }

    $sql = "SELECT * FROM seller_manager ORDER BY id DESC";
    $res=mysql_query($sql);
    $row=mysql_num_rows($res); //透過mysql_num_rows()取得總頁數
    $maxpage=ceil($row/$total); //*計算總頁數=(總筆數/每頁筆數)後無條件進位避免 總筆數與總頁數除不盡時剩餘頁無法顯示

    if($page>$maxpage)
    {
        $page=$maxpage; //如果初始頁數大於總頁數就等於總頁數
    }

    $start=$total*($page-1); // 初始頁數 =每筆頁數*(頁預設數-1)
    $sql = "SELECT * FROM seller_manager ORDER BY id DESC LIMIT ".$start.",".$total;
    $res=mysql_query($sql); //將查詢資料存到 $result 中
    @$row=mysql_num_rows($res);
    for($i=1;$i<=$row;$i++)
    {
        $ary[$i]=mysql_fetch_array($res);
    }

    for($i=1;$i<=$row;$i++)
    {
        ?>
        <tr align="center">
            <td width="2%" align="center"><?php echo $ary[$i]['id']; ?></td>
            <td width="8%" align="center"><?php echo $ary[$i]['manager_no']; ?></td>
            <td width="8%" align="center">
                    <?php
                    
                    if(strlen($ary[$i]['member_id']) > 11)
                    {
                        $sql2 = "SELECT * FROM fb WHERE fb_id ='".$ary[$i]['member_id']."'";
                        $res2 = mysql_query($sql2);
                        $row2 = mysql_fetch_array($res2);
                        ?>
                            <a href="home.php?url=edit_fb_member&id=<?php echo $row2['id']; ?>">
                        <?php
                        echo $row2['fb_name'];
                    }
                    else
                    {
                        $sql2 = "SELECT * FROM member WHERE member_no = '".$ary[$i]['member_id']."'";
                        $res2 = mysql_query($sql2);
                        $row2 = mysql_fetch_array($res2);
                        ?>
                                <a href="home.php?url=edit_member&id=<?php echo $row2['id']; ?>">
                        <?php
                        echo $row2['m_name'];
                    }
                    ?>
                </a>
            </td>
            <td width="10%"><?php echo $row2['cellphone']; ?></td>
            <td width="12%"><?php echo $ary[$i]['self_introduction']; ?></td>
            <td width="8%"><?php echo $ary[$i]['apply_time']; ?></td>
            <td width="5%">
                <?php
                if($ary[$i]['apply_status'] == 0)
                {
                    echo '申請失敗';
                }
                else if($ary[$i]['apply_status'] == 1)
                {
                    echo '通過';
                }
                else if($ary[$i]['apply_status'] == 2)
                {
                    echo '待審核';
                }
                ?>
            </td>
            <td width="8%"><?php 
            $sql3 = "SELECT * FROM member WHERE member_no = '".$ary[$i]['member_id']."'";
            $res3 = mysql_query($sql3);
            $row3 = mysql_fetch_array($res3);
            echo $row3['profit']; 
            ?></td>
            <td width="13%">
                <a href="javascript:void(0);" onclick="edit_fun(
                <?php
                
                    if(strlen($ary[$i]['member_id']) > 10)
                    {
                        echo $row2['id'].",'fb')\" class=\"btn\" style=\"color:#fff; background: green;\">";
                    }
                    else
                    {
                        echo $row2['id'].",'n')\" class=\"btn\" style=\"color:#fff; background: green;\">";
                    }
                    ?>
                    <i class="iconsweets-bandaid iconsweets-white"></i>修改
                </a>
                <a href="javascript:void(0);" onclick="edit_fun2(
                <?php
                
                    if(strlen($ary[$i]['member_id']) > 10)
                    {
                        echo $row2['id'].",'fb')\" class=\"btn\" style=\"color:#fff; background: green;\">";
                    }
                    else
                    {
                        echo $row2['id'].",'n')\" class=\"btn\" style=\"color:#fff; background: green;\">";
                    }
                    ?>
                    <i class="iconsweets-book iconsweets-white"></i>分享查看
                </a>
                <a href="javascript:void(0);" onclick="delete_fun(<?php echo $ary[$i]['id']; ?>)" class="btn btn-danger"
                   style="color:#fff;">
                    <i class="iconsweets-trashcan iconsweets-white"></i>刪除
                </a>

            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <table width="90%">
        <tr>
            <td align="center">
                <br />
                <?php
                if($page==1){
                    ?>
                    上一頁&nbsp;&nbsp;
                    <?php
                }else{
                    ?>
                    <a style='text-decoration:none;' href="?url=seller_manager&page=<?php echo $page-1;?>">上一頁&nbsp;&nbsp;</a>
                <?php }?>

                <?php
                echo "<select onChange='location = this.options[this.selectedIndex].value;' class='span1'>";
                for($i=1;$i<=$maxpage;$i++)
                {
                    if($i==$page)
                    {
                        echo "<option selected='selected' value=".$i.">".$i."</option>";
                    }
                    else
                    {
                        echo "<option value=home.php?url=seller_manager&page=".$i.">".$i."</option>";
                    }
                }
                echo "</select>";
                ?>

                <?php
                if($page==$maxpage){
                    ?>
                    &nbsp;&nbsp;下一頁
                    <?php
                }else{
                    ?>
                    <a style='text-decoration:none;' href="?url=seller_manager&page=<?php echo $page+1;?>">&nbsp;&nbsp;下一頁</a>
                <?php }?>
                <br />
            </td>
        </tr>
    </table>
</table>


<script>
    /*function insert_btn()
    {
        location.href='home.php?url=add_supplier';
    }*/

    function edit_fun(id,arg)
    {
        location.href='home.php?url=review_manager&id='+id+"&arg="+arg;
    }

    function delete_fun(id)
    {
        if(confirm("確定要刪除?"))
        {
            location.href='home.php?url=delete_fun&d_id='+id+'&pg=seller_manager';
        }
    }
    function edit_fun2(id,arg)
    {
        location.href='home.php?url=seller_manager_share&id='+id+"&arg="+arg;
    }
</script>
