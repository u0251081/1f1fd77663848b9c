 <style>
 
 	#supplierleft img
 	{
 		width: 100%;
 	}
 	#abgne_float_ad {
	display: none;
	position: absolute;
	}

		#abgne_float_ad button
 		{
 		background-color:rgb(255, 147, 147); 
 		color:white; 
 		border-color:transparent;
 		width:70px;
 		height: 70px;
 		}
 		#abgne_float_ad button:hover
 		{
 			background-color: white;
    		color: rgb(255, 147, 147);
    		border-color: rgb(255, 147, 147);
    		
 		}
 		

 	
	@media (max-width: 767px) 
 	{
    	
 		.product_content4 h3
 		{
 			/*font-size: 18px;*/
 		}
 		.product_content4 p
 		{
 			/*font-size: 5px;*/
 			
 		}
 		#supplierleft img
 		{
 			margin-top: 15px;
 		}
 		
 		#abgne_float_ad button
 		{
 		width: 60px;
 		height: 60px;
 		
 		}
	}
	@media (max-width: 480px) 
 	{
    	
 		.product_content4 h3
 		{
 			font-size: 18px;
 		}
 		.product_content4 p
 		{
 			font-size: 5px;

 		}
 		#supplierleft img
 		{
 			margin-top: 15px;
 		}
 		
 		#abgne_float_ad button
 		{
 		width: 40px;
 		height: 40px;
 		
 		}
	}

 </style>
 
<section id="aa-product-details" class='product_content4' style="margin-bottom:11%;">
    <div class="container">
        <div class="row">
        	<?php
                @$id = $_GET['supplier_id'];
                $sql = "SELECT * FROM supplier WHERE id='$id'";
                $res = mysql_query($sql);
                $row = mysql_fetch_array($res);
                if($row['id']== "")
                {
                    ?>
                        <script>
                        alert('沒有此供應商');
                        window.location.href='index.php';
                        </script>
                    <?php
                }
        	?>
            <div class="col-md-12">
                <div class="aa-product-details-area">
                    <div class="aa-product-details-content">
                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-5" id="supplierleft">
                             	
                             	<img src="admin/<?php echo $row['big_img']; ?>" style="border-radius: 50%;">

                            </div>
                            <div class="col-md-8 col-sm-8 col-xs-7">
                             	<h3><?php echo $row['supplier_title']; ?></h3>
                             	<p><?php echo $row['info']; ?></p>

                            </div>
                        </div>
                    </div>

                    <div class="aa-product-details-bottom" id="suplier_bottom">
                        <p style="line-height: 50px; margin-bottom: 100px;"><?php echo $row['info2']; ?></p>
                    </div>
                    <!-- Related product -->
                </div>
            </div>
        </div>
    </div>
</section>


<div id="abgne_float_ad" >
		
			
			<button class="buy">購買</button> 
		
</div>

<script type="text/javascript">
         $("#aa-slider").hide();
	$(window).load(function(){

		
		var $win = $(window),
		$ad = $('#abgne_float_ad').css('opacity', 0).show(),	// 讓廣告區塊變透明且顯示出來
		_width = $ad.width(),
		_height = $ad.height(),
		_diffY = 300,
		 _diffX = 20,	// 距離右及下方邊距
		_moveSpeed = 500;
		// 移動的速度
		
	// 先把 #abgne_float_ad 移動到定點
	$ad.css({
		top: $(document).height(),
		left: $win.width() - _width - _diffX,
		opacity: 1
	});
 
	// 幫網頁加上 scroll 及 resize 事件
	$win.bind('scroll resize', function(){
		var $this = $(this);
 
		// 控制 #abgne_float_ad 的移動
		$ad.stop().animate({
			top: $this.scrollTop() + $this.height() - _height - _diffY,
			left: $this.scrollLeft() + $this.width() - _width - _diffX
		}, _moveSpeed);
	}).scroll();	// 觸發一次 scroll()
});
	
	 
	 $('.buy').click(function(){
	 	<?php
	 	 @$id = $_GET['supplier_id'];
                $sql1 = "SELECT id FROM supplier WHERE id='$id'";
                $res1 = mysql_query($sql1);
                $row1 = mysql_fetch_array($res1);
	 	    if($row1['id']!= "")
                {
                	         
                    ?>
                                
                     window.location.href='index.php?url=supplier_product&sid=<?php echo $row1['id']; ?>';
                    <?php
                	
                }
        	?>

	 });
    
	
	
</script>