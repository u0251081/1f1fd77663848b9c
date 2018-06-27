<script type="text/javascript" src="ckeditor/ckeditor.js"></script>

<?php
@$id = $_GET['id'];
$sql = "SELECT * FROM product WHERE id='$id'";
$res = mysql_query($sql);
@$row = mysql_fetch_array($res);
?>
<div class="widget">
    <h4 class="widgettitle">修改商品資料</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" onSubmit="return form_stop()" method="post">
            <p>
                <label>商品分類</label>
                <span class="field">
                    <span id="sdisplay"></span>
                <?php
                $sql1 = "SELECT * FROM product AS a JOIN class AS b JOIN product_class AS c ON a.id = c.product_id AND b.id = c.pid WHERE a.id='$id'";
                //echo $sql1;
                $res1 = mysql_query($sql1);
                while($row1 = mysql_fetch_array($res1))
                {
                    if($row1['pid']==$row1['id'])
                    {
                        echo"
								<select class='preset span2'>
									<option value='".$row1['id']."' selected='selected'>".$row1['name']."</option>
								</select>";
                    }
                }
                ?>
                <input type="button" id="btn" class="span1 btn btn-primary" style="margin-bottom: 10px;" value="清除">
                </span>
            </p>

            <p>
                <label>商品名稱</label>
                <span class="field"><input type="text" name="p_name" class="input-large" value="<?php echo $row['p_name']; ?>" placeholder="請輸入商品名稱"/></span>
            </p>
            <p>
                <label>商品數量</label>
                <span class="field"><input type="text" name="p_qty" class="input-large" value="<?php echo $row['p_qty']; ?>" placeholder="請輸入商品數量"/></span>
            </p>
            <p>
                <label>已賣數量</label>
                <span class="field"><?php echo $row['sell_qty'] == "" ? 0 : $row['sell_qty']; ?></span>
            </p>
            <p>
                <label>剩餘數量</label>
                <span class="field"><?php 
                
                    echo $row['rem_qty']; 
                
                
                ?></span>
            </p>
            <p>
                <label>商品價格</label>
                <span class="field">
                    <?php
                    if($_SESSION['identity'] == 'admin')
                    {
                        $price_sql = "SELECT * FROM price WHERE p_id='".$row['id']."' AND sell_id='1'";
                        $price_res = mysql_query($price_sql);
                        $price_row = mysql_fetch_array($price_res);
                    }
                    ?>
                    建議售價：<input type="text" name="price" class="input-large" value="<?php echo $price_row['price']; ?>" placeholder="請輸入建議售價"/><br><br>
                    網路價格：<input type="text" name="web_price" class="input-large" value="<?php echo $price_row['web_price']; ?>" placeholder="請輸入網路價格"/>
                </span>
            </p>
            <p>
                <label>商品簡介</label>
                <span class="field"><input type="text" name="p_introduction" class="span3" maxlength="19" value="<?php echo $row['p_introduction']; ?>"></span>
            </p>
            <p>
                <label>商品詳細介紹</label>
                <span class="field"><textarea id="my_ckeditor" name="p_info" cols="50" rows="5"><?php echo $row['p_info']; ?></textarea></span>
            </p>
            <p>
                <label>紅利點數</label>
                <span class="field"><input type="text" name="p_bonus" class="input-large" value="<?php echo $row['p_bonus']; ?>" placeholder="請輸入購買後可得到的點數"/></span>
            </p>
            <p>
                <label>分潤%</label>
                <span class="field"><input type="text" name="p_profit" class="input-large" value="<?php echo $row['p_profit']; ?>" placeholder="行銷經理行銷成功後可獲得的金額"/></span>
            </p>
             <p>
                <label>分潤可得金額</label>
                <span class="field"><input type="text" name="p_profit" disabled class="input-large" value="<?php echo floor($price_row['web_price']*$row['p_profit']/100); ?>" placeholder="行銷經理行銷成功後可獲得的金額"/></span>
            </p>
            <p>
                <label>注意事項</label>
                <span class="field"><textarea id="textbox2" name="p_notes" cols="50" rows="5" placeholder="請輸入注意事項"><?php echo $row['p_notes']; ?></textarea></span>
            </p>
            <p>
                <label>廣告影片</label>
                <span class="field"><input type="text" name="youtube" class="input-large" value="<?php echo $row['youtube']; ?>" placeholder="請複製youtube網址貼上"/></span>
            </p>
            <p>
                <label>貨品供應商</label>
                <span class="field">
                    <select name="s_id" id="s_id" class="uniformselect">
                        <option value="">請選擇供應商</option>
                        <?php
                        $s_sql = "SELECT * FROM supplier";
                        $s_res = mysql_query($s_sql);
                        while ($s_row = mysql_fetch_array($s_res))
                        {
                            if($row['s_id'] == $s_row['id'])
                            {
                                echo "<option value=".$s_row['id']." selected>".$s_row['supplier_name']."</option>";
                            }
                            else
                            {
                                echo "<option value=".$s_row['id'].">".$s_row['supplier_name']."</option>";
                            }
                        }
                        ?>
                    </select>
                </span>
            </p>
            <p>
                <label>是否上架</label>
                <span class="field">
                    <input type="radio" name="added" <?php if($row['added'] == 1){echo 'checked';} ?> class="input-large" value="1" />上架&nbsp;&nbsp;
                    <input type="radio" name="added" <?php if($row['added'] == 0){echo 'checked';} ?> class="input-large" value="0" />下架
                </span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="btn btn-primary span1" value="修改">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" class="btn btn-default span1" value="返回" onclick="window.history.back(-1);">
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
//@$standard_id = $_POST['standard_id']; //規格id
//@$standard = $_POST['standard']; //商品規格
//@$qty = $_POST['qty']; //數量
//-----------------------------------------------//

//------------以下新增至product_class-------------//
@$sclass = $_POST['sclass'];
//----------------------------------------------//

if(isset($_POST['btn']))
{
    if($p_profit >= $web_price)
    {
        ?>
        <script>
            alert('分潤金額不能大於等於網路價格');
        </script>
        <?php
    }
    else
    {
        $sql = "UPDATE product SET p_name='$p_name', p_introduction='$p_introduction', p_info='$p_info',rem_qty='$p_qty', p_notes='$p_notes', youtube='$youtube', added='$added', p_qty='$p_qty', s_id='$s_id', p_bonus='$p_bonus', p_profit='$p_profit' WHERE id='$id'";
        mysql_query($sql);

        if($_SESSION['identity'] == 'admin')
        {
            $sql2 = "UPDATE price SET price='$price', web_price='$web_price' WHERE p_id='$id' AND sell_id='".$_SESSION['id']."'";
            mysql_query($sql2);
        }

        //分類處理在此
        if($sclass!="")
        {
            $sql = "DELETE FROM product_class WHERE product_id='$id'";
            mysql_query($sql);
        }

        for($i=0; $i<count($sclass);$i++)
        {
            $sql ="INSERT INTO product_class SET pid='$sclass[$i]', product_id='$id'";
            mysql_query($sql);
        }

        ?>
        <script>
            alert('修改成功');
            location.href='home.php?url=product';
        </script>
        <?php
    }
}
?>
<script>
    $(function ()
    {
        CKEDITOR.replace('my_ckeditor');
        CKEDITOR.replace('textbox2');
    });

    function creat()
    {
        $.ajax
        ({
            url:"sever_ajax.php", //接收頁
            type:"POST", //POST傳輸
            data:{type:"sclass"}, // key/value
            dataType:"json", //回傳形態
            success:function(i) //成功就....
            {
                $("#sdisplay").append('<select name="sclass[]" id="sclass" class="span2 sclass uniformselect" /> ');
                $("#sclass").append('<option value="none" selected="selected">請選擇分類</option>');
                $(".preset").remove();
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

                    $("#sdisplay").append('<select name="sclass[]" class="span2 sclass uniformselect" id="sclass' + sall + '"><option value="none">請選擇分類</option></select> '); //添加子分類

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
    }

    var btn = $("#btn");
    if(btn.val()!="")
    {
        btn.click(function()
        {
            creat();
            btn.attr({disabled:"disabled"});
        });
    }
</script>