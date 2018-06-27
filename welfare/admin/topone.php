                <style>
                ul, li {
                    margin: 0;
                    padding: 0;
                    list-style: none;
                }
                .abgne_tab {
                    clear: left;
                    width: 400px;
                    margin: 10px 0;
                }
                ul.tabs {
                    width: 100%;
                    height: 32px;
                    border-bottom: 1px solid #999;
                    border-left: 1px solid #999;
                }
                ul.tabs li {
                    float: left;
                    height: 31px;
                    line-height: 31px;
                    overflow: hidden;
                    position: relative;
                    margin-bottom: -1px;    /* 讓 li 往下移來遮住 ul 的部份 border-bottom */
                    border: 1px solid #999;
                    border-left: none;
                    background: #e1e1e1;
                }
                ul.tabs li a {
                    display: block;
                    padding: 0 30px;
                    height: 31px;   
                    color: #000;
                    border: 1px solid #fff;
                    text-decoration: none;
                }
                ul.tabs li a:hover {
                    background: #ccc;
                }
                ul.tabs li.active  {
                    background: #fff;
                    border-bottom: 1px solid#fff;
                }
                ul.tabs li.active a:hover {
                    background: #fff;
                }
                div.tab_container {
                    clear: left;
                    width: 100%;
                    border: 1px solid #999;
                    border-top: none;
                    background: #fff;
                }
                div.tab_container .tab_content {
                    padding: 20px;
                }
                div.tab_container .tab_content h2 {
                    margin: 0 0 20px;
                }

                </style>
                <form class="editprofileform" method="post" enctype="multipart/form-data">
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="widgetbox">
                                <?php               
                                        $sql = "SELECT * FROM consumer_order WHERE datediff(now(),`order_time`)=0";
                                        $res = mysql_query($sql);
                                        $row = mysql_fetch_array($res);
                                        $num = mysql_num_rows($res);
                                    ?> 
                                <h4 class="widgettitle">今日訂單數量：<?php  echo $num;?></h4>
                                <div class="widgetcontent" id="check">
                                    <ul class="tabs" style="font-size: 15px;">
                                        <li><a href="#tab1">已付款</a></li>
                                        <li><a href="#tab2">未付款</a></li>
                                         <li><a href="#tab3">暢銷排行榜</a></li>
                                    </ul>
                 
                                    <div class="tab_container">
                                    <div id="tab1" class="tab_content">
                                        <table class="table responsive table-bordered">
                                             <thead>
                                                <tr>
                                                <th align="center">訂單編號</th>
                                                <th align="center">收件人資料</th>
                                                <th align="center">收貨時間</th>
                                                <th align="center">商品明細</th>
                                                <th align="center">訂單狀態</th>
                                            </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT * FROM consumer_order,addressee_set WHERE datediff(now(),`order_time`)=0 AND addressee_set.m_id=consumer_order.m_id AND addressee_set.order_no=consumer_order.order_no ORDER BY consumer_order.id";
                    $res=mysql_query($sql); //將查詢資料存到 $result 中
                    @$row=mysql_num_rows($res);

                    for($i=1;$i<=$row;$i++)
                    {
                        $ary[$i]=mysql_fetch_array($res);
                    }
                    for($i=1;$i<=$row;$i++)
                    {
                        if($ary[$i]['is_effective']==1 || $ary[$i]['is_effective']==3)
                        {
                        ?>

                        <tr align="center">  
                            <td width="1%" align="center"><?php echo $ary[$i]['order_no']; ?></td>      
                            <td width="5%" align="center">
                                <p>姓名：<?php echo $ary[$i]['name']; ?></p>
                                <p>電話：<?php echo $ary[$i]['cellphone']; ?></p>
                                <p>地址：<?php echo $ary[$i]['address']; ?></p>
                            </td>
                            <td width="1%">
                            <?php
                                if($ary[$i]['addressee_date']==1)
                                {
                                    echo "週一至週五";
                                }
                                else if($ary[$i]['addressee_date']==2)
                                {
                                    echo "週六";
                                } 
                                else
                                {         
                                    echo "都可以";
                                }

                            ?>
                            </td>
                           <td width="1%">
                                <?php
                                if($ary[$i]['order_no']!="")
                                {
                                $sql2 = "SELECT p_name FROM consumer_order2,consumer_order WHERE consumer_order.id=consumer_order2.order1_id AND order_no='".$ary[$i]['order_no']."'";
                                $res2=mysql_query($sql2);
                                while ($row2=mysql_fetch_array($res2)) {
                                    echo $row2['p_name'];
                                    echo '</br>';
                                }
                               
                               
                                }
                                ?>
                            </td>
                            <td width="1%">
                                 <?php
                                switch(@$ary[$i]['is_effective'])
                                                {
                                                    case 0 || "":
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

                            ?>
                            </td>
                        </tr>
                        <?php
                    }
                    }
                    ?>
                    </tbody>
                    
                </table>
                                     </div>
                                    <div id="tab2" class="tab_content">
                                        <table class="table responsive table-bordered">
                                        <thead>
                    <tr>
                       
                         <th align="center">訂單編號</th>
                        <th align="center">收件人資料</th>
                        <th align="center">收貨時間</th>
                        <th align="center">商品明細</th>
                        <th align="center">訂單狀態</th>
                    </tr>
                    </thead>
                                        <tbody>
                    <?php
                    for($i=1;$i<=$row;$i++)
                    {
                        if($ary[$i]['is_effective']==0 || $ary[$i]['is_effective']==2)
                        {
                        ?>

                        <tr align="center">
                            
                            <td width="1%" align="center"><?php echo $ary[$i]['order_no']; ?></td>      
                            <td width="4%" align="center">
                                <p>姓名：<?php echo $ary[$i]['name']; ?></p>
                                <p>電話：<?php echo $ary[$i]['cellphone']; ?></p>
                                <p>地址：<?php echo $ary[$i]['address']; ?></p>
                            </td>
                            <td width="1%">
                            <?php
                                if($ary[$i]['addressee_date']==1)
                                {
                                    echo "週一至週五";
                                }
                                else if($ary[$i]['addressee_date']==2)
                                {
                                    echo "週六";
                                } 
                                else
                                {         
                                    echo "都可以";
                                }

                            ?>
                            </td>
                             <td width="1%">
                                <?php
                                if($ary[$i]['order_no']!="")
                                {
                                $sql2 = "SELECT p_name FROM consumer_order2,consumer_order WHERE consumer_order.id=consumer_order2.order1_id AND order_no='".$ary[$i]['order_no']."'";
                                $res2=mysql_query($sql2);
                                while ($row2=mysql_fetch_array($res2)) {
                                    echo $row2['p_name'];
                                    echo '</br>';
                                }
                               
                               
                                }
                                ?>
                            </td>
                            <td width="1%">
                                 <?php
                                switch(@$ary[$i]['is_effective'])
                                                {
                                                    case 0 || "":
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

                            ?>
                            </td>
                        </tr>
                        <?php
                    }
                    }
                    ?>
                    </tbody>
                </table>

                                    </div>
                                    <div id="tab3" class="tab_content">
                                        <table class="table responsive table-bordered">
                                             <thead>
                                                <tr>
                                                <th align="center">編號</th>
                                                <th align="center">商品名稱</th>
                                                <th align="center">以賣數量</th>
                                                <th align="center">剩餘數量</th>
                                            </tr>
                    </thead>
                     <tbody>
                    <?php
                    $sql3 = "SELECT * FROM product WHERE added=1 ORDER BY sell_qty DESC";
                    $res3=mysql_query($sql3); //將查詢資料存到 $result 中
                    @$row3=mysql_num_rows($res3);

                    for($i=1;$i<=$row3;$i++)
                    {
                        $ary3[$i]=mysql_fetch_array($res3);
                    }
                    for($i=1;$i<=$row3;$i++)
                    {
                        
                            
                        ?>
                        <tr align="center">  
                            <td width="1%" align="center"><?php echo $ary3[$i]['id']; ?></td>      
                            <td width="1%" align="center"><?php echo $ary3[$i]['p_name']; ?></td>
                            <td width="1%" align="center"><?php echo $ary3[$i]['sell_qty']; ?></td>
                            <td width="1%" align="center"><?php echo $ary3[$i]['rem_qty']; ?></td>
                           
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                    
                </table>
                                     </div>
                                    </div>         
                                    
                                </div>
                            </div>

                           
                        </div>

                        <!--span8-->
                        <!-- <div class="span4 profile-right">
                            <div class="widgetbox profile-photo">
                                <div class="headtitle">
                                    
                                    <h4 class="widgettitle">銷售排行榜</h4>
                                </div>
                                <div class="widgetcontent" >
                                    <div class="profilethumb">
                                        <img id="profile_img" src="<?php echo $row['img']; ?>" width="200" height="200"/>
                                    </div>
                                </div>
                            </div>
                            <input type="file" style="display: none;" name="upload" id="fileinput"/>
                        </div> -->
                        <!--span4-->
                    </div>
                    
                </form>

                <?php
                @$logo_tmp = $_FILES['upload']['tmp_name'];
                @$logo = $_FILES['upload']['name'];
                @$filedir="images/photos/".$logo;//指定上傳資料
                @$old_img = $_POST['old_img'];
                @$password = $_POST['password'];
                @$name = $_POST['name'];
                if(isset($password) || isset($name))
                {
                    if($logo != "")
                    {
                        unlink($old_img);
                        $sql = "UPDATE admin SET password='$password', `name`='$name', img='$filedir' WHERE id='".$_SESSION['id']."'";
                        mysql_query($sql);
                        move_uploaded_file($logo_tmp,$filedir);
                    }
                    else
                    {
                        $sql = "UPDATE admin SET password='$password', `name`='$name' WHERE id='".$_SESSION['id']."'";
                        mysql_query($sql);
                    }
                    ?>
                    <script>
                        alert('修改成功');
                        location.href='home.php';
                    </script>
                    <?php
                }
                ?>

                <script>
                    $("#edit_pic").click(function ()
                    {
                        $('#fileinput').click();
                    });

                    //----------------顯示密碼------------------//
                    var password = $("input[name='password']");
                    var showpassword = $('#show');
                    var inputpassword = $('<input type="text" name="password" class="input-xlarge" />');
                    showpassword.click(function(){
                        if(this.checked)
                        {
                            password.replaceWith(inputpassword.val(password.val()));
                        }
                        else
                        {
                            inputpassword.replaceWith(password.val(inputpassword.val()));
                        }
                    });
                    $(function(){
                    // 預設顯示第一個 Tab
                    var _showTab = 0;
                    $('#check').each(function(){
                        // 目前的頁籤區塊
                        var $tab = $(this);
                 
                        var $defaultLi = $('ul.tabs li', $tab).eq(_showTab).addClass('active');
                        $($defaultLi.find('a').attr('href')).siblings().hide();
                 
                        // 當 li 頁籤被點擊時...
                        // 若要改成滑鼠移到 li 頁籤就切換時, 把 click 改成 mouseover
                        $('ul.tabs li', $tab).click(function() {
                            // 找出 li 中的超連結 href(#id)
                            var $this = $(this),
                                _clickTab = $this.find('a').attr('href');
                            // 把目前點擊到的 li 頁籤加上 .active
                            // 並把兄弟元素中有 .active 的都移除 class
                            $this.addClass('active').siblings('.active').removeClass('active');
                            // 淡入相對應的內容並隱藏兄弟元素
                            $(_clickTab).stop(false, true).fadeIn().siblings().hide();
                 
                            return false;
                        }).find('a').focus(function(){
                            this.blur();
                        });
                    });
                });
                </script>
