<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="//apps.bdimg.com/libs/jqueryui/1.10.4/css/jquery-ui.min.css">
  <script src="//apps.bdimg.com/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//apps.bdimg.com/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
  <script>
  $(function() {
    $( "#date" ).datepicker({
        changeYear : true,
                        changeMonth : true
    });
  });
  </script>
</head>

<style>
    #pay_check_div
    {
        margin-top: 13%;
        margin-bottom: 24%;
    }
    #dspan 
        {
            margin-left:10px;
        }
        #tspan 
        {
margin-left:10px;
        }
        #pspan 
        {
        margin-left:10px;
        }
        #submit
        {
            margin-left: 10px;
        }
      @media (max-width:1200px)
    {
        #pay_check_div
        {
            margin-top: 20%;
        }
    }
    @media (max-width:995px)
    {
        #pay_check_div
        {
            margin-top: 22%;
        }
        #dspan input
        {
            
        }
        #tspan select 
        {
            width: 100px;
        }
        #pspan input
        {

        }
    }
    @media (max-width:850px)
    {
        #pay_check_div
        {
            margin-top: 25%;
        }
        #dspan input
        {
            
        }
        #tspan select 
        {
            width: 100px;
        }
        #pspan input
        {

        }
    }
    @media (max-width:768px)
    {
        #pay_check_div
        {
            margin-top: 30%;
        }
        #dspan 
        {
            display: block;
        }
        #tspan 
        {
           display: block;
        }
        #pspan 
        {
           display: block;
        }
        #submit
        {
            margin-left: 10px;
        }
    }
     @media (max-width:535px)
    {
        #pay_check_div
        {
            margin-top: 40%;
        }
        #dspan 
        {
            display: block;
        }
        #tspan 
        {
           display: block;
        }
        #pspan 
        {
           display: block;
        }
        #submit
        {
            margin-left: 10px;
        }
    }
</style>

<?php
$sql = "SELECT * FROM fans_data WHERE manager_no='".$_SESSION['manager_no']."'";
$res=mysql_query($sql);
$list_arr=array();
$i=0;
while($row=mysql_fetch_array($res)){ 
$list_arr[$i]=$row;
$i++;
}
$m_id=$list_arr[0]['id'];
$sql2 ="SELECT `fb_no`,`is_effective` FROM consumer_order WHERE `fb_no` LIKE '%".$list_arr[0]['id']."-%'";
for($i=1;$i<count($list_arr);$i++)
{
    $sql2.="or `fb_no` LIKE '%".$list_arr[$i]['id']."-%'";
    $m_id.=",".$list_arr[$i]['id'];
    
}

$res2=mysql_query($sql2);
$u=0;
while ($row2 = mysql_fetch_array($res2)) {
    if($row2['is_effective']!='0' && $row2['is_effective']!='2')
    {
     $rows[$u] = $row2['fb_no'];
     $u++;  
    }
       
} 

?>


<div class="container" id="pay_check_div">
    <div class="row">
        <table class="table table-bordered table-responsive table-condensed" style="margin-top: 20px;">
            <span id="dspan">日期：<input type="text" id="date"></span>
            <span id="tspan">時段：
                <select id="timer">
                 <option value="all">全部時段</option>
　               <option value="morning">上午 7:00 ~ 11:59</option>
　               <option value="afternoon">中午 12:00 ~ 16:59</option>
　               <option value="night">晚上 17:00 ~ 23:59</option>
　               <option value="Early morning">凌晨 00:00 ~ 6:59</option>
                </select></span>
            <span id="pspan" >編號：<input type="text" id="page"></span>
            <button " id="submit">送出</button>
            <thead>
            <tr>
                <th colspan="5"><h4 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">fb導購查詢<br>您目前成功導購數為：<br><?php if($rows!="")
                { 
                     $array1=array_count_values($rows);
                     foreach($array1 as $key => $val) {
                          
                            echo $key.":";
                            echo $val." 筆<br>"; 
                        

                     }

                     
                    
                }
                else{echo 0;echo "筆";} ?></h4></th>
            </tr>          
            </thead> 
                 
        </table>
        <table class="table table-bordered table-responsive table-condensed" style="margin-top: 20px;" id="inserttable">
                
        </table>    
    </div>
</div>

<script>
    $("#aa-slider").remove();
    $('#submit').click(function(){
    var timer=$('#timer').val();
    var page=$('#page').val();
    var date=$('#date').val();
    var m_id="<?php echo $m_id ?>";
     switch(timer)
                    {
                        case 'all':
                             var time1="00:00:00";
                             var time2="23:59:59";
                            break;
                        case 'morning':
                             var time1="07:00:00";
                             var time2="11:59:59";
                            break;
                        case 'afternoon':
                             var time1="12:00:00";
                             var time2="16:59:59";
                            break;
                        case 'night':
                             var time1="17:00:00";
                             var time2="23:59:59";
                            break;
                        case 'Early morning':
                             var time1="00:00:00";
                             var time2="06:59:59";
                            break;
                    }

    if(date!="")
    {
         date=date.split("/")['2']+"/"+date.split("/")['0']+"/"+date.split("/")['1'];
    }


     $.ajax
            ({
                url:"ajax.php",
                type:"POST",
                data:{type:"fb_search", time1:time1,time2:time2,page:page,date:date,m_id:m_id},
                dataType:"json",
                success:function(data)
                {
                    $("#inserttable").html("");
                    $("#inserttable").append(
                             "<tr style='background: #DDDDDD;'><th>粉專姓名</th>"
                                +"<th>編號</th>"
                                +"<th>點擊時間</th></tr>"

                        );
                    for(var i=0;i<data.length;i++)
                    {
                        $("#inserttable").append(
                           
                                "<tr id='search_data'><td>"+data[i]['name']+"</td>"
                                +"<td>"+data[i]['page']+"</td>"
                                +"<td>"+data[i]['storetime']+"</td></tr>"
                           
                            
                        );
                    }
                     
                            
                },
                error:function(data)
                {
                    alert("發生錯誤請重新輸入");
                }
            });

    });
  
    
</script>