<?php
/**
 * Created by PhpStorm.
 * User: andychen
 * Date: 8/24/18
 * Time: 11:14 PM
 */

use Base17Mai\Product;
use function Base17Mai\take;

$pid = take('id', '');
$Product = new Product($pid);
$Product->setEditorData();
$vendorID = $Product->getVendor();
$subTitle = $Product->getEditorProductTitle();
$productID = $Product->getProductID();
$productClass = $Product->getClass();
$PName = $Product->getPName();
$productSpec = $Product->getSpecification();
$quantity = $Product->getQuantity();
$unitPrice = $Product->getUnitPrice();
$feedBack = $Product->getFeedBack();
$bonus = $Product->getBonus();
$resume = $Product->getResume();
$description = $Product->getDescription();
$productInfo = $Product->getInformation();
$youtube = $Product->getYoutube();
$prelease = $Product->getRelease();
function vendorOption($vendorID)
{
    $sql = "SELECT * FROM supplier";
    $res = mysql_query($sql);
    $html = '';
    while ($row = mysql_fetch_array($res)) {
        $selected = ($vendorID === $row['id']) ? 'selected' : '';
        $html .= "<option value=\"{$row['id']}\"{$selected}>{$row['supplier_name']}</option>";
    }
    return $html;
}

function generateVendorList($vendorID)
{
    $vendorList = vendorOption($vendorID);
    $result = "
    <select name=\"s_id\" id=\"s_id\" class=\"uniformselect\">
        <option>請選擇供應商</option>
        {$vendorList}
    </select>";
    return $result;
}

if ($identity === 'admin') $vendorContent = generateVendorList($vendorID);
else $vendorContent = $memberNO;


?>

    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <div class="widget">
        <h4 class="widgettitle"><?= $subTitle ?></h4>
        <div class="widgetcontent">
            <form class="stdform stdform2" method="post">
                <p>
                    <label>商品分類</label>
                    <span class="field" id="sdisplay">
                        <?= $productClass ?>
                    </span>
                </p>
                <p>
                    <label>商品ID</label>
                    <span class="field">
                        <?= $productID ?>
                    </span>
                </p>
                <p>
                    <label>貨品供應商<br>如果不是 Admin 不可選</label>
                    <span class="field">
                        <?= $vendorContent ?>
                    </span>
                </p>
                <p>
                    <label>商品名稱</label>
                    <span class="field">
                        <input type="text" name="PName" class="input-large" placeholder="請輸入商品名稱"
                               value="<?= $PName ?>"/>
                    </span>
                </p>
                <p>
                    <label>商品規格</label>
                    <span class="field">
                        <button type="button" id="btn_addSpec">增加規格</button>
                    </span>
                    <span class="field" id="specField" style="display: flex; flex-direction: column;"></span>
                </p>
                <!--
                <p>
                    <label>商品欲登記數量</label>
                    <span class="field">
                        <?= $quantity ?>
                    </span>
                </p>
                -->
                <p>
                    <label>商品價格</label>
                    <span class="field">
                        <input type="number" name="unitPrice" min="0" class="input-large" placeholder="請輸入商品價格(單價)"
                               value="<?= $unitPrice ?>"/>
                    </span>
                </p>
                <p>
                    <label>回饋比率%</label>
                    <span class="field">
                        <input type="number" name="feedBack" class="input-large" min="0" max="100"
                               placeholder="請輸入與品台的分潤比率" value="<?= $feedBack ?>"/>&nbsp;%
                    </span>
                </p>
                <p>
                    <label>紅利點數</label>
                    <span class="field">
                        <input type="number" name="bonus" min="0" class="input-large" placeholder="請輸入購買後可得到的點數"
                               value="<?= $bonus ?>"/>&nbsp;點
                    </span>
                </p>
                <p>
                    <label>健康履歷</label>
                    <span class="field">
                        URL&nbsp;:&nbsp;&nbsp;<input type="url" name="healthResume" class="span5"
                                                     placeholder="請輸入履歷連接" value="<?= $resume ?>"/>
                    </span>
                </p>
                <p>
                    <label>商品簡介</label>
                    <span class="field">
                        <input type="text" name="description" class="span3" maxlength="19" placeholder="請在20字以內描述您的商品"
                               value="<?= $description ?>">
                    </span>
                </p>
                <p>
                    <label>商品詳細介紹<br>如果您有說明、注意事項、使用教學.....等，請打在這</label>
                    <span class="field">
                        <textarea id="textbox" name="p_info" cols="50" rows="5"><?= $productInfo ?></textarea>
                    </span>
                </p>
                <p>
                    <label>廣告影片</label>
                    <span class="field">
                        <input type="text" name="youtube" class="input-large" placeholder="請複製youtube網址貼上"
                               value="<?= $youtube ?>"/>
                    </span>
                </p>
                <p>
                    <label>是否上架</label>
                    <span class="field">
                        <input type="text" name="Prelease" class="input-large" value="1" <?= $prelease ?>>
                    </span>
                </p>
                <p class="stdformbutton">
                    <input type="submit" name="btn" class="span1 btn btn-primary" value="提交">&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="cancel" class="span1 btn btn-default" value="返回">
                </p>
            </form>
        </div><!--widgetcontent-->
    </div><!--widget-->
    <script>
        var cnt = 0;

        $(document).ready(function () {
            $('#specField').append('<?= $productSpec ?>');
            cnt = $('input[name^="p_spec"]').length;
        });

        $(document).on('click', 'input[type="cancel"]', function () {
            location.href = "home.php?url=product";
        });

        $(document).on('click', 'button#btn_addSpec', AddSpec);
        $(document).on('click', 'a.btn_RemoveSpec', function () {
            let id = $(this).attr('value');
            RemoveSpec(id);
        });

        $(document).on('submit', 'form', function () {
            return false;
        });

        function RemoveSpec(element) {
            let target = element.parentElement;
            let next = target.nextElementSibling;
            let parent = target.parentElement;
            let last = parent.lastElementChild;
            target.remove();
            if (next !== null && next.tagName === 'BR') next.remove();
            if (last !== null && last.tagName === 'BR') parent.removeChild(last);
            cnt--;
            for (let i = 0; i < parent.childElementCount; i++) {
                let tmp = parent.children.item(i);
                if (tmp.tagName === 'BR') continue;
                tmp.children.item(0).innerHTML = String(i + 1).padStart(2, '0');
            }
            /*
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
                if (id < 1) tmpBr.attr('id', i);
                tmpSpan.text(String(i).padStart(2, '0'));
                tmpSpec.attr('name', 'p_spec[' + (i - 1) + ']');
                tmpRemove.attr('value', i - 1);
            }
            */
            if (cnt < 10) $('button#btn_addSpec').prop('disabled', false);
        }

        function AddSpec() {
            if (cnt < 10) {
                let code = document.createElement('span');
                code.innerHTML += String(cnt + 1).padStart(2, '0');
                let spec = document.createElement('input');
                spec.type = "text";
                spec.name = 'spec[' + cnt + '][specification]';
                spec.className = 'input-large';
                spec.placeholder = '請輸入商品規格';
                let Quantity = document.createElement('input');
                Quantity.type = 'number';
                Quantity.name = 'spec[' + cnt + '][Quantity]';
                Quantity.min = 0;
                Quantity.className = 'input-large';
                Quantity.placeholder = '請輸入商品數量';
                let s = '&nbsp;&nbsp;&nbsp;';
                let remove = document.createElement('a');
                remove.innerHTML = '&#x2715';
                remove.href = 'javascript:void(0);';
                remove.addEventListener('click', function () {
                    RemoveSpec(this)
                });
                let html = document.createElement('div');
                html.append(code);
                html.innerHTML += '&nbsp;:&nbsp;&nbsp;';
                html.append(spec);
                html.innerHTML += s;
                html.append(Quantity);
                html.append(remove);
                let target = document.getElementById('specField');
                target = $('#specField');
                console.log(target.children().last().prop('tagName') === 'DIV');
                if (target.children().last().prop('tagName') === 'DIV') target.append("<br>");
                target.append(html);
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
            let unitPrice = Number($('input[name="unitPrice"]').val());
            let bonus = Number($('input[name="bonus"]').val());
            if (unitPrice !== '' && bonus !== '') {
                if (unitPrice <= bonus) {
                    alert('強烈建議獎金應比定價低');
                }
            }
        }

        $("input[name='bonus']").blur(validBonus);

        $(document).on('submit', 'form', function () {
            let inputValue = getFormData($(this));
            ajax17mai('Product', 'UpdateProduct', {}, inputValue)
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