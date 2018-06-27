<style>
    #pay_check_div
    {
        margin-top: 13%;
        margin-bottom: 5%;
    }

    @media screen and (max-width: 1280px)
    {
        #pay_check_div
        {
            margin-bottom: 30%;
        }
    }
</style>
<script>
    var stock_uri = 'http://163.18.62.21/app/rest/stock.cgi';
</script>

<?php
if(strlen($member_id) > 10)
{
    $bonus_sql = "SELECT * FROM fb WHERE fb_id='$member_id'";
}
else
{
    $bonus_sql = "SELECT * FROM member WHERE member_no='$member_id'";
}
$bonus_res = mysql_query($bonus_sql);
@$bonus_row = mysql_fetch_array($bonus_res);
?>
<div class="container" id="pay_check_div">
    <div class="row">
        <table class="table table-bordered table-responsive table-condensed">
            <thead>
            <tr>
                <th colspan="5">
                    <h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">點數兌換查詢<br>您目前總點數為：<?php if($bonus_row['bonus']!=""){ echo "<span id='now_bonus'>".$bonus_row['bonus']."</span>"; }else{echo 0;} ?>點</h3>
                </th>
            </tr>
            <tr style="background: #DDDDDD;">
                <th>商品圖</th>
                <th>名稱</th>
                <th>點數</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody id="push_content">
            </tbody>
            <tr align="right">
                <td colspan="5">
                    <div class="container">
                        <div class="row">
                            <input type="button" class="btn btn-default" value="返回" onclick="location.href='index.php?url=member_center'">&nbsp;&nbsp;
                            <input type="button" class="btn btn-info" value="兌換紀錄" onclick="location.href='index.php?url=bonus_use'">
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
    $("#aa-slider").hide();
    var b_id,bonus_name,bonus_price; //android用的變數
    $(function()
    {
        $.ajax
        ({
            url:stock_uri,
            dataType:"json",
            success:function(i)
            {
                $.each(i,function(k,v)
                {
                    $("#push_content")
                        .append('<tr>'+
                            '<td align="center"><div id="qrcode" style="display: none;"></div><img class="img-responsive" id="p_img" src='+v.img+' width="100"></td>'+
                            '<td id=content'+v.id+'>'+v.title+'</td>'+
                            '<td>'+v.price+'</td>'+
                            '<td>'+
                            '<input type="button" class="btn btn-primary" value="兌換" onclick="pay_bonus('+v.id+','+v.price+')">'+
                            '</td>'+
                            '</tr>');
                });
            },
            error:function()
            {
                //do something
            }
        });
    });

    function pay_bonus(a,b)
    {
        var id = a; //點數商品id
        var b_title = $("#content"+id).text(); //點數商品內容
        var b_price = b; //點數商品金額

        b_id = a;
        bonus_name = $("#content"+id).text();
        bonus_price = b;

        if($("#device").text() == 'mobile')
        {
            window.javatojs.myconfirm('bonus');
        }
        else
        {
            if(confirm("是否要兌換該商品"))
            {
                $.ajax
                ({
                    url:"ajax.php", //接收頁
                    type:"POST", //POST傳輸
                    data:{type:"convert_bonus", id:id, b_title:b_title, b_price:b_price}, // key/value
                    dataType:"text", //回傳形態
                    success:function(i) //成功就....
                    {
                        var str = i.split("/");
                        if(str[0] == 1)
                        {
                            var bonus_change = $("#now_bonus").text() - str[1];
                            alert('兌換成功，若要使用請至兌換記錄查看');
                            $("#now_bonus").text(bonus_change)
                        }
                        else if(str[0] == 0)
                        {
                            alert('點數不足無法兌換');
                        }
                    },
                    error:function()//失敗就...
                    {
                    }
                });
            }
        }
    }

    function dialod_res(t)
    {
        if(t == 'yes')
        {
            $.ajax
            ({
                url:"ajax.php", //接收頁
                type:"POST", //POST傳輸
                data:{type:"convert_bonus", id:b_id, b_title:bonus_name, b_price:bonus_price}, // key/value
                dataType:"text", //回傳形態
                success:function(i) //成功就....
                {
                    var str = i.split("/");
                    if(str[0] == 1)
                    {
                        var bonus_change = $("#now_bonus").text() - str[1];
                        window.javatojs.showInfoFromJs('兌換成功，若要使用請至兌換記錄查看');
                        $("#now_bonus").text(bonus_change)
                    }
                    else if(str[0] == 0)
                    {
                        window.javatojs.showInfoFromJs('點數不足無法兌換');
                    }
                },
                error:function()//失敗就...
                {
                }
            });
        }
    }
</script>