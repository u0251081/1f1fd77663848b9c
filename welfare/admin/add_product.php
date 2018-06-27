<script type="text/javascript" src="ckeditor/ckeditor.js"></script>

<div class="widget">
    <h4 class="widgettitle">新增商品資料</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post">
            <p>
                <label>商品分類</label>
                <span class="field">
                    <select name="sclass[]" id="sclass" class="sclass span2">
                        <option value="none" selected="selected">請選擇分類</option>
                    </select>
                    <span id="sdisplay"></span>
                </span>
            </p>
            <p>
                <label>商品名稱</label>
                <span class="field"><input type="text" name="p_name" class="input-large" placeholder="請輸入商品名稱"/></span>
            </p>
            <p>
                <label>商品數量</label>
                <span class="field"><input type="text" name="p_qty" class="input-large" placeholder="請輸入商品數量"/></span>
            </p>
            <p>
                <label>商品價格</label>
                <span class="field">
                    建議售價：<input type="text" name="price" class="input-large" placeholder="請輸入建議售價"/><br><br>
                    網路價格：<input type="text" name="web_price" class="input-large" placeholder="請輸入網路價格"/>
                </span>
            </p>
            <p>
                <label>商品簡介</label>
                <span class="field"><input type="text" name="p_introduction" class="span3" maxlength="19" placeholder="請輸入一段簡單的商品介紹"></span>
            </p>
            <p>
                <label>商品詳細介紹</label>
                <span class="field"><textarea id="textbox" name="p_info" cols="50" rows="5"></textarea></span>
            </p>

            <p>
                <label>紅利點數</label>
                <span class="field"><input type="text" name="p_bonus" class="input-large" placeholder="請輸入購買後可得到的點數"/></span>
            </p>
            <p>
                <label>分潤%</label>
                <span class="field"><input type="text" name="p_profit" class="input-large" placeholder="請輸入與行銷經理的分潤金額"/></span>
            </p>
            <p>
                <label>注意事項</label>
                <span class="field"><textarea id="textbox2" name="p_notes" cols="50" rows="5" placeholder="請輸入注意事項"></textarea></span>
            </p>
            <p>
                <label>廣告影片</label>
                <span class="field"><input type="text" name="youtube" class="input-large" placeholder="請複製youtube網址貼上"/></span>
            </p>
            <p>
                <label>貨品供應商</label>
                <span class="field">
                    <select name="s_id" id="s_id" class="uniformselect">
                        <option value="">請選擇供應商</option>
                        <?php
                        $sql = "SELECT * FROM supplier";
                        $res = mysql_query($sql);
                        while ($row = mysql_fetch_array($res))
                        {
                            echo "<option value=".$row['id'].">".$row['supplier_name']."</option>";
                        }
                        ?>
                    </select>
                </span>
            </p>
            <p>
                <label>是否上架</label>
                <span class="field">
                    <input type="radio" name="added" class="input-large" value="1" checked />上架&nbsp;&nbsp;
                    <input type="radio" name="added" class="input-large" value="0" />下架
                </span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="span1 btn btn-primary" value="提交">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" class="span1 btn btn-default" value="清除">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
//------------以下新增至product表----------------------//

@$p_name = $_POST['p_name']; //商品名稱
@$p_qty = $_POST['p_qty']; //商品總數量
@$p_introduction = $_POST['p_introduction']; //商品簡介
@$p_info = $_POST['p_info']; //商品詳細介紹
//@$p_use = $_POST['p_use']; //使用方式
@$p_notes = $_POST['p_notes']; //注意事項
@$youtube = $_POST['youtube']; //youtube廣告
@$s_id = $_POST['s_id']; //所屬供應商
@$added = $_POST['added']; //是否上架 1:上架 0:下架(預設是1)
@$p_bonus = $_POST['p_bonus']; //點數
@$p_profit = $_POST['p_profit']; //分潤%數
//--------------------------------------------------//

//------------以下新增至price表----------------------//

@$price = $_POST['price']; //建議售價
@$web_price = $_POST['web_price']; //網路價格

//------------------------------------------------//

//------------以下新增至standard表------------------//

//@$standard = $_POST['standard']; //商品規格
//@$qty = $_POST['qty']; //數量
//-----------------------------------------------//

//------------以下新增至product_class-------------//
@$sclass = $_POST['sclass'];
//-----------------------------------------------//

if(isset($_POST['btn']))
{
    $sql = "INSERT INTO product SET p_name='$p_name', p_introduction='$p_introduction', p_info='$p_info', p_notes='$p_notes', p_bonus='$p_bonus', p_profit='$p_profit', youtube='$youtube', added='$added', add_day='".date('Y-m-d H:i:s')."', p_qty='$p_qty', s_id='$s_id'";
    mysql_query($sql);
    $insert_p_id = mysql_insert_id();
    if($insert_p_id != "")
    {
        if($_SESSION['identity'] == 'admin')
        {
            $sql2 = "INSERT INTO price SET p_id='$insert_p_id', price='$price', web_price='$web_price', sell_id='1', sell_account='".$_SESSION['id']."'";
            mysql_query($sql2);
        }

        //商店分類處理在此
        for($i=0; $i<count($sclass);$i++)
        {
            $sql ="INSERT INTO product_class SET pid='$sclass[$i]', product_id='$insert_p_id'";
            mysql_query($sql);
        }
    }

    ?>
    <script>
        alert('新增成功，請設定商品圖片');
        location.href='home.php?url=product_img';
    </script>
    <?php
}
?>

<script>
    $(function ()
    {
        CKEDITOR.replace('textbox');
        CKEDITOR.replace('textbox2');
    });
</script>

<script>

    $("input[name='p_profit']").blur(function()
    {
        var web_price = $('input[name="web_price"]').val();
        var profit = $(this).val();
        if(web_price != "")
        {
            if(p_profit >= web_price)
            {
                alert('分潤金額需比網路價格低');
                $("input[name='p_profit']").val('');
            }
        }
    });


    //----------------分類處理------------------//
    $(function()
    {
        $.ajax
        ({
            url:"sever_ajax.php", //接收頁
            type:"POST", //POST傳輸
            data:{type:"sclass"}, // key/value
            dataType:"json", //回傳形態
            success:function(i) //成功就....
            {
                $.each(i, function(index, items) //主選單
                {
                    if(items.id==items.parent_id)
                    {
                        $("#sclass").append("<option value='" + items.id + "'>"+items.name+"</option>");
                    }
                });

                $(document).on('change','.sclass', function() //添加change事件到select選單
                {
                    var svalue = $(this).val(); //選中的值
                    var snow = $(".sclass").index(this); //選擇的第幾個選單
                    $(".sclass").each(function(index, items)  //清除子分類
                    {
                        if (snow < index)
                        {
                            $(this).remove();
                        }
                    });

                    var sall = $('.sclass').length; //算共有幾個select選單

                    $("#sdisplay").append('<select name="sclass[]" class="sclass span2" id="sclass' + sall + '"><option value="none" selected="selected">請選擇分類</option></select> '); //添加子分類

                    var child_count=0; //計算子分類內有幾個項目
                    $.each(i, function(index, items) //新增子分類選項
                    {
                        if (items.parent_id ==svalue && items.id !=items.parent_id)
                        {
                            child_count+=1;
                            $('#sclass' + sall).append('<option value="' + items.id + '">' + items.name + '</option>');
                        }
                    });

                    //移除沒有子分類中已經到底的選單或為空的選單
                    if(child_count==0)
                    {

                        $('#sclass' + sall).remove();
                    }
                });
            },
            error:function()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    });
</script>