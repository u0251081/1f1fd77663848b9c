<!--<a href="javascript:void(0);" class="btn btn-primary" onclick="insert_btn()">新增</a>-->

<table class="table responsive table-bordered">
    <thead>
    <tr>
        <h4 class="widgettitle" style="text-align: center;">訂單列表</h4>
    </tr>
    <tr>
        <th align="center">編號</th>
        <th align="center">訂單編號</th>
        <th align="center">收件人資訊</th>
        <th align="center">購買數量</th>
        <th align="center">購買日期</th>
        <th align="center">狀態</th>
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

    $sql = "SELECT *,a.id AS aid FROM consumer_order AS a JOIN consumer_order2 AS b ON a.id = b.order1_id AND a.order_over IS NOT NULL GROUP BY a.order_no ORDER BY a.order_time DESC";
    //echo $sql;
    $res=mysql_query($sql);
    $row=mysql_num_rows($res); //透過mysql_num_rows()取得總頁數
    $maxpage=ceil($row/$total); //*計算總頁數=(總筆數/每頁筆數)後無條件進位避免 總筆數與總頁數除不盡時剩餘頁無法顯示

    if($page>$maxpage)
    {
        $page=$maxpage; //如果初始頁數大於總頁數就等於總頁數
    }

    $start=$total*($page-1); // 初始頁數 =每筆頁數*(頁預設數-1)
    $sql = "SELECT *,a.id AS aid FROM consumer_order AS a JOIN consumer_order2 AS b ON a.id = b.order1_id AND a.order_over IS NOT NULL GROUP BY a.order_no ORDER BY a.order_time DESC LIMIT ".$start.",".$total;
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
            <td width="5%" align="center"><?php echo $ary[$i]['aid']; ?></td>
            <td width="5%" align="center"><?php echo $ary[$i]['order_no']; ?></td>
            <td width="10%">
                <?php
                $addressee= "SELECT * FROM addressee_set WHERE order_no='".$ary[$i]['order_no']."'";
                $addressee_res = mysql_query($addressee);
                $addressee_row = mysql_fetch_array($addressee_res);
                echo $addressee_row['name']."<br>".$addressee_row['cellphone'];
                ?>
            </td>
            <td width="3%"><?php echo $ary[$i]['qty']; ?></td>
            <td width="10%"><?php echo $ary[$i]['order_time']; ?></td>
            <td width="4%">
                <?php
                if($ary[$i]['order_time'] !="")
                {
                    switch ($ary[$i]['is_effective'])
                    {
                        case 0:
                            echo '未付款';
                            break;
                        case 1:
                            echo '備貨中';
                            break;
                        case 2:
                            echo '已取消';
                            break;
                        case 3:
                            echo '已出貨';
                            break;
                    }
                }
                ?>
            </td>
            <td width="4%">
                <a href="javascript:void(0);" onclick="edit_fun(<?php echo $ary[$i]['aid']; ?>)" class="btn span1"
                   style="color:#fff; background: green;">查看
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
                    <a style='text-decoration:none;' href="?url=order_list&page=<?php echo $page-1;?>">上一頁&nbsp;&nbsp;</a>
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
                        echo "<option value=home.php?url=order_list&page=".$i.">".$i."</option>";
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
                    <a style='text-decoration:none;' href="?url=order_list&page=<?php echo $page+1;?>">&nbsp;&nbsp;下一頁</a>
                <?php }?>
                <br />
            </td>
        </tr>
    </table>
</table>

<script>
    function edit_fun(id)
    {
        location.href='home.php?url=order_detail&id='+id;
    }
</script>