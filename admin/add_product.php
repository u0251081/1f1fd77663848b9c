<?php

$vendorList = vendorOption();

function vendorOption()
{
    $sql = "SELECT * FROM supplier";
    $res = mysql_query($sql);
    $html = '';
    while ($row = mysql_fetch_array($res)) {
        $html .= "<option value=\"{$row['id']}\">{$row['supplier_name']}</option>";
    }
    return $html;
}

?>

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
                <label>商品ID</label>
                <span class="field">
                    新商品ID將於刊登後自動產生
                </span>
            </p>
            <p>
                <label>供應商ID</label>
                <span class="field">
                    這裡顯示供應商的ID
                </span>
            </p>
            <p>
                <label>商品名稱</label>
                <span class="field">
                    <input type="text" name="PName" class="input-large" placeholder="請輸入商品名稱"/>
                </span>
            </p>
            <p>
                <!-- , unitPrice, feedBack, bonus, healthResume, productInformation-->
                <label>商品規格</label>
                <span class="field">
                    <button type="button" id="btn_addSpec">增加規格</button>
                </span>
                <span class="field" id="specField" style="display: flex; flex-direction: column;">
                </span>
            </p>
            <p>
                <label>商品數量</label>
                <span class="field">
                    <input type="number" name="Quantity" class="input-large" placeholder="請輸入商品數量"/>
                </span>
            </p>
            <p>
                <label>商品價格</label>
                <span class="field">
                    <input type="number" name="unitPrice" class="input-large" placeholder="請輸入商品價格(單價)"/>
                    <!--<br><br>-->
                </span>
            </p>
            <p>
                <label>回饋比率%</label>
                <span class="field">
                    <input type="number" name="feedBack" class="input-large" min="0" max="100"
                           placeholder="請輸入與品台的分潤比率"/>&nbsp;%
                </span>
            </p>
            <p>
                <label>紅利點數</label>
                <span class="field">
                    <input type="number" name="bonus" class="input-large" placeholder="請輸入購買後可得到的點數"/>&nbsp;點
                </span>
            </p>
            <p>
                <label>健康履歷</label>
                <span class="field">
                    URL&nbsp;:&nbsp;&nbsp;<input type="url" name="healthResume" class="span5" placeholder="請輸入履歷連接"/>
                    <br><br>
                </span>
            </p>
            <p>
                <label>商品簡介</label>
                <span class="field">
                    <input type="text" name="description" class="span3" maxlength="19" placeholder="請在20字以內描述您的商品">
                </span>
            </p>
            <p>
                <label>商品詳細介紹<br>如果您有說明、注意事項、使用教學.....等，請打在這</label>
                <span class="field">
                    <textarea id="textbox" name="p_info" cols="50" rows="5"></textarea>
                </span>
            </p>
            <p>
                <label>廣告影片</label>
                <span class="field">
                    <input type="text" name="youtube" class="input-large" placeholder="請複製youtube網址貼上"/>
                </span>
            </p>
            <p>
                <label>貨品供應商<br>如果不是 Admin 不可選</label>
                <span class="field">
                    <select name="s_id" id="s_id" class="uniformselect">
                        <option value="">請選擇供應商</option>
                        <?= $vendorList ?>
                    </select>
                </span>
            </p>
            <p>
                <label>是否上架</label>
                <span class="field">
                    <input type="text" name="Prelease" class="input-large" value="1" checked>
                </span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="span1 btn btn-primary" value="提交">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="reset" class="span1 btn btn-default" value="清除">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->
<script>
    var cnt = 0;

    $(document).ready(function () {
    });

    $(document).on('click', 'button#btn_addSpec', AddSpec);
    $(document).on('click', 'a.btn_RemoveSpec', function () {
        let id = $(this).attr('value');
        RemoveSpec(id);
    });

    $(document).on('submit','form',function() {
        return false;
    });

    function RemoveSpec(id) {
        $('input[name="p_spec[' + id + ']"]').parent().get(0).replaceWith('');
        if (id > 0) var target = document.getElementById(String(Number(id)));
        else var target = document.getElementById(String(Number(id + 1)));
        if (target !== null) target.replaceWith('');
        cnt--;
        let length = $('input[name^="p_spec"]').length;
        for (let i = Number(id) + 1; i <= length; i++) {
            let tmpSpec = $('input[name="p_spec[' + i + ']"]');
            let tmpSpan = tmpSpec.closest('div').find('span');
            let tmpRemove = $('a[value="' + i + '"]');
            let tmpBr = $('br[id="' + i + '"]');
            if (id < 1) tmpBr = $('br[id="' + (i + 1) + '"]');
            tmpBr.attr('id', i - 1);
            if (id < 1) tmpBr.attr('id', i );
            tmpSpan.text(String(i).padStart(2, '0'));
            tmpSpec.attr('name', 'p_spec[' + (i - 1) + ']');
            tmpRemove.attr('value', i - 1);
        }
        if (cnt < 10) $('button#btn_addSpec').prop('disabled', false);
    }

    function AddSpec() {
        if (cnt < 10) {
            let element = "<input type=\"text\" name=\"p_spec[" + cnt + "]\" class=\"input-large\" placeholder=\"請輸入商品規格\"/>";
            let remove = "<a href=\"javascript:void(0);\" class=\"btn_RemoveSpec\" value=\"" + cnt + "\">&#x2715;</a>";
            let code = "<span>" + String(cnt + 1).padStart(2, '0') + "</span>";
            let html = "<div>" + code + '&nbsp;:&nbsp;&nbsp;' + element + remove + "</div>";
            if (cnt > 0) html = "<br id=\"" + cnt + "\">" + html;
            $('#specField').append(html);
            cnt++;
            if (cnt === 10) $('button#btn_addSpec').prop('disabled', true);
        } else {
            alert("不能增加更多規格！！！");
        }
    }


    $(function () {
        CKEDITOR.replace('textbox');

        let specialBotton = $('input[name="Prelease"]');
        specialBotton.attr('type', 'checkbox');
        specialBotton.switchbutton({
            //checkedLabel: 'YES',
            //uncheckedLabel: 'NO'
            classes: 'ui-switchbutton-thin',
            labels: false
        });


        //----------------分類處理------------------//
        $.ajax({
            url: "sever_ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "sclass"}, // key/value
            dataType: "json", //回傳形態
            success: function (i) //成功就....
            {
                $.each(i, function (index, items) //主選單
                {
                    if (items.id == items.parent_id) {
                        $("#sclass").append("<option value='" + items.id + "'>" + items.name + "</option>");
                    }
                });

                $(document).on('change', '.sclass', function () //添加change事件到select選單
                {
                    var svalue = $(this).val(); //選中的值
                    var snow = $(".sclass").index(this); //選擇的第幾個選單
                    $(".sclass").each(function (index, items)  //清除子分類
                    {
                        if (snow < index) {
                            $(this).remove();
                        }
                    });

                    var sall = $('.sclass').length; //算共有幾個select選單

                    $("#sdisplay").append('<select name="sclass[]" class="sclass span2" id="sclass' + sall + '"><option value="none" selected="selected">請選擇分類</option></select> '); //添加子分類

                    var child_count = 0; //計算子分類內有幾個項目
                    $.each(i, function (index, items) //新增子分類選項
                    {
                        if (items.parent_id == svalue && items.id != items.parent_id) {
                            child_count += 1;
                            $('#sclass' + sall).append('<option value="' + items.id + '">' + items.name + '</option>');
                        }
                    });

                    //移除沒有子分類中已經到底的選單或為空的選單
                    if (child_count == 0) {

                        $('#sclass' + sall).remove();
                    }
                });
            },
            error: function ()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    });


    function validBonus() {
        let unitPrice = $('input[name="unitPrice"]').val();
        let bonus = $('input[name="bonus"]').val();
        if (unitPrice !== '' && bonus !== '') {
            if (unitPrice <= bonus) {
                alert('強烈建議獎金應比定價低');
            }
        }
    }

    $("input[name='bonus']").blur(validBonus);

    $(document).on('submit', 'form', function () {
        let inputValue = getFormData($(this));
        ajax17mai('Product', 'CreateProduct', {}, inputValue)
    });


</script>

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

if (isset($_POST['btn'])) {
    $sql = "INSERT INTO product SET p_name='$p_name', p_introduction='$p_introduction', p_info='$p_info', p_notes='$p_notes', p_bonus='$p_bonus', p_profit='$p_profit', youtube='$youtube', added='$added', add_day='" . date('Y-m-d H:i:s') . "', p_qty='$p_qty', s_id='$s_id'";
    mysql_query($sql);
    $insert_p_id = mysql_insert_id();
    if ($insert_p_id != "") {
        if ($_SESSION['identity'] == 'admin') {
            $sql2 = "INSERT INTO price SET p_id='$insert_p_id', price='$price', web_price='$web_price', sell_id='1', sell_account='" . $_SESSION['id'] . "'";
            mysql_query($sql2);
        }

        //商店分類處理在此
        for ($i = 0; $i < count($sclass); $i++) {
            $sql = "INSERT INTO product_class SET pid='$sclass[$i]', product_id='$insert_p_id'";
            mysql_query($sql);
        }
    }

    ?>
    <script>
        alert('新增成功，請設定商品圖片');
        location.href = 'home.php?url=product_img';
    </script>
    <?php
}
?>

<script>
</script>

<script>
</script>