<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="//apps.bdimg.com/libs/jqueryui/1.10.4/css/jquery-ui.min.css">
  <script src="//apps.bdimg.com/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//apps.bdimg.com/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
  <script>
  $(function() {
    $("#date1, #date2,#date3,#date4").datepicker({
        changeYear : true,
        changeMonth : true,
        dateFormat : "yy-mm-dd"
    });
  });

  
  </script>
</head>

<div class="widget" >
    <h4 class="widgettitle">搜尋</h4>
    <div class="widgetcontent">
        <form class="search" method="post">
        	<center>
        	<input type="radio" value="1" name="choose_type" id="choose_type" checked="true">訂單
            <input type="radio" value="2" name="choose_type" id="choose_type2">商品
            <input type="radio" value="3" name="choose_type" id="choose_type3">會員
            <input type="radio" value="4" name="choose_type" id="choose_type4">供應商
        	</center>
        </form>
        
       <div id="order" >
       		 <p>
                <label>訂單時間</label>
                <span class="field1" style="font-size: large;">
                   <input id="date1" name="date1">-<input id="date2" name="date2">
                </span>
            </p>
            <p>
                <label>訂單編號</label>
                <span class="field1">
                  <input id="order_id">
                </span>
            </p>
             <p>
                <label>收件人名稱</label>
                <span class="field1" style="font-size: large;">
                   <input id="order_name">
                </span>
            </p>
            <p>
                <label>訂單商品名稱</label>
                <span class="field1">
                  <input id="order_product">
                </span>
            </p>
            <input type="button" value="搜尋" name="order_button" id="order_button">
            <input type="button" value="清除" name="order_button" id="order_button_clear">
       </div>
       <div id="product" style="display: none;">
       		 <p>
                <label>商品上架日期</label>
                <span class="field2" style="font-size: large;">
                   <input id="date3">-<input id="date4">
                </span>
            </p>
            <p>
                <label>商品名稱</label>
                <span class="field2">
                  <input id="product_name">
                </span>
            </p>  
            <p class="rfield2">
              <input type="radio" value="2" name="product_status" id="product_status">審核
              <input type="radio" value="1" name="product_status" id="product_status">上架中
              <input type="radio" value="0" name="product_status" id="product_status">下架中
            </p>
            <input type="button" value="搜尋" id="product_button">
            <input type="button" value="清除" id="product_button_clear">
       </div>
       <div id="member" style="display: none;">
       		 <p>
                <label>會員姓名</label>
                <span class="field3" style="font-size: large;">
                   <input id="member_name">
                </span>
            </p>
            <p>
                <label>email</label>
                <span class="field3">
                  <input id="member_email">
                </span>
            </p>
            <p>
                <label>電話</label>
                <span class="field3">
                  <input id="member_phone">
                </span>
            </p>
  			 <input type="button" value="搜尋" id="member_button">
         <input type="button" value="清除" id="member_button_clear">
       </div>
        <div id="supplier" style="display: none;">
       		 <p>
                <label>供應商姓名</label>
                <span class="field4" style="font-size: large;">
                   <input id="supplier_name">
                </span>
            </p>
            <p>
                <label>供應商電話</label>
                <span class="field4" >
                  <input id="supplier_phone">
                </span>
            </p>
             <p>
                <label>供應商商品</label>
                <span class="field4" style="font-size: large;">
                   <input id="supplier_product">
                </span>
            </p>  
            <p class="rfield4">
              <input type="radio" value="2" name="supplier_status" id="supplier_status">審核
              <input type="radio" value="1" name="supplier_status" id="supplier_status">上架中
              <input type="radio" value="0" name="supplier_status" id="supplier_status">下架中
            </p>
            <input type="button" value="搜尋" id="supplier_button">
            <input type="button" value="清除" id="supplier_button_clear">
       </div>
    </div><!--widgetcontent-->
</div><!--widget-->
<script>
var dftWidth=1200;
var dftHeight=800;

function OpenWin(url)
{
   var w=window.screen.width/2-(dftWidth/2);
   var t=window.screen.height/2-(dftHeight/2)-35;
   window.open(url,"","scrollbars=yes,toolbar=no,status=1,resizable=yes,directories=no,menubar=no,top="+t+",left="+w+",width="+dftWidth+",height="+dftHeight+"");
}
$("#order_button").click(function(){
	var date1=$("#date1").val();
	var date2=$("#date2").val();
	var order_name=$("#order_name").val();
	var order_id=$("#order_id").val();
	var order_product=$("#order_product").val();
	OpenWin("search_result.php?date1="+date1+"&date2="+date2+"&order_name="+order_name+"&order_id="+order_id+"&order_product="+order_product+"&type=1");
});
$("#product_button").click(function(){
	var date3=$("#date3").val();
	var date4=$("#date4").val();
	var product_name=$("#product_name").val();
	var product_status=$('input[name=product_status]:checked').val();
	OpenWin("search_result.php?date3="+date3+"&date4="+date4+"&product_name="+product_name+"&product_status="+product_status+"&type=2");
	
});
$("#member_button").click(function(){
	var member_name=$("#member_name").val();
	var member_email=$("#member_email").val();
	var member_phone=$("#member_phone").val();
	OpenWin("search_result.php?member_name="+member_name+"&member_email="+member_email+"&member_phone="+member_phone+"&type=3");
});
$("#supplier_button").click(function(){
	var supplier_name=$("#supplier_name").val();
	var supplier_phone=$("#supplier_phone").val();
	var supplier_product=$("#supplier_product").val();
	var supplier_status=$('input[name=supplier_status]:checked').val();
	OpenWin("search_result.php?supplier_name="+supplier_name+"&supplier_phone="+supplier_phone+"&supplier_product="+supplier_product+"&supplier_status="+supplier_status+"&type=4");
});
    $("#choose_type").change(function()
    {
        if($(this).is(':checked') && $(this).val() == "1")
        {
        	$("#order").show();
        	$("#supplier,#product,#member").hide();
        }
    });
    $("#choose_type2").change(function()
    {
        if($(this).is(':checked') && $(this).val() == "2")
        {
        	$("#product").show();
        	$("#supplier,#order,#member").hide();
        }
    });
    $("#choose_type3").change(function()
    {
        if($(this).is(':checked') && $(this).val() == "3")
        {
        	$("#member").show();
        	$("#supplier,#product,#order").hide();
        }
    });
    $("#choose_type4").change(function()
    {
        if($(this).is(':checked') && $(this).val() == "4")
        {
        	$("#supplier").show();
        	$("#order,#product,#member").hide();
        }
    });
 $("#order_button_clear").click(function(){
      $(".field1 >input").val("");

 });
 $("#product_button_clear").click(function(){
      $(".field2 > input").val("");
      $(".rfield2 > input").attr("checked",false);
 });
 $("#member_button_clear").click(function(){
      $(".field3 >input").val("");
 });
 $("#supplier_button_clear").click(function(){
      $(".field4 >input").val("");
      $(".rfield4 > input").attr("checked",false);
 });
 
</script>