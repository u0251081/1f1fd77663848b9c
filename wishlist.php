<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li><a href="index.php?url=member_center">會員專區</a></li>
                <li class="active">追蹤清單</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->

<?php
if(isset($_SESSION["member_no"]))
{
    $member_id = $_SESSION["member_no"];
}
else
{
    $member_id = $_SESSION["fb_id"];
}

if($member_id !="")
{
    ?>
    <!-- Cart view section -->
    <section id="cart-view">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="cart-view-area" style="margin-bottom: 11%; padding-top: 1px;">
                        <div class="cart-view-table aa-wishlist-table">
                            <form action="">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>取消</th>
<!--                                            <th>商品圖</th>-->
                                            <th>商品名稱</th>
                                            <th>商品金額</th>
                                            <th>商品狀態</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if(isset($member_id))
                                        {
                                            $sql = "SELECT * FROM product_track AS a JOIN product AS b ON a.p_id = b.id WHERE m_id='".$member_id."'";
                                            //echo $sql;
                                            $res = mysql_query($sql);
                                            while($row = mysql_fetch_array($res))
                                            {
                                                if($row['added'] == 1)
                                                {
                                                    ?>
                                                    <tr id="tr<?php echo $row['id']; ?>">
                                                        <td>
                                                            <a class="remove" href="javascript:void(0)" name="remove_track" pid="<?php echo $row['id']; ?>">
                                                                <fa class="fa fa-close"></fa>
                                                            </a>
                                                        </td>
                                                        <td><a class="aa-cart-title" href="index.php?url=product_detail&product_id=<?php echo $row['id'];?>"><?php echo $row['p_name']; ?></a></td>
                                                        <td>
                                                            <?php
                                                            $price = "SELECT * FROM price WHERE p_id='".$row['id']."' AND sell_id='1'";
                                                            $price_res = mysql_query($price);
                                                            $price_row = mysql_fetch_array($price_res);
                                                            echo 'NT$'.$price_row['web_price'];
                                                            ?>
                                                        </td>
                                                        <td><?php echo '上架中'; ?></td>
                                                        <td><a class="aa-add-to-cart-btn add_cart_btn" p_id="<?php echo $row['p_id']; ?>" last_qty="<?php echo $row['rem_qty']; ?>">放入購物車</a></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- / Cart view section -->
    <?php
}
else
{
    ?>
    <script>
        alert('請先登入或註冊成為會員');
        location.href='index.php?url=login';
    </script>
    <?php
}
?>
<script>
	var pid;
    $(function ()
    {
        $("#aa-slider").hide();
        $("html,body").scrollTop(30);
    });

    $("a[name='remove_track']").on('click',function()
    {
        pid = $(this).attr('pid');
		if($("#device").text() == 'mobile')
		{
			window.javatojs.myconfirm('wishlist');
		}
		else
		{
			if(confirm('您是否要移除追蹤清單?'))
			{
				$.ajax
				({
					url:"ajax.php", //接收頁
					type:"POST", //POST傳輸
					data:{type:"favorite", pid:pid}, // key/value
					dataType:"text", //回傳形態
					success:function(i) //成功就....
					{
						$("#tr"+pid).remove();
						alert('已移除追蹤清單');
					},
					error:function()//失敗就...
					{
						//alert("ajax失敗");
					}
				});
			}
		}
    });

    $(".add_cart_btn").click(function()
    {
        var p_id = $(this).attr('p_id');
        var last_qty = $(this).attr('last_qty');
        if(last_qty <= 0)
        {
            if($("#device").text() == 'mobile')
            {
                window.javatojs.showInfoFromJs('此商品已完售，無法加入購物車');
            }
            else
            {
                alert('此商品已完售，無法加入購物車');
            }
        }
        else
        {
            $.ajax
            ({
                url:"ajax.php", //接收頁
                type:"POST", //POST傳輸
                data:{type:"wish_to_cart", pid:p_id}, // key/value
                dataType:"text", //回傳形態
                success:function(i) //成功就....
                {
                    if(i == 1)
                    {
                        if($("#device").text() == 'mobile')
                        {
                            window.javatojs.showInfoFromJs('商品已經在購物車中');
                        }
                        else
                        {
                            alert('商品已經在購物車中');
                        }
                    }
                    else if(i == 2)
                    {
                        if($("#device").text() == 'mobile')
                        {
                            window.javatojs.showInfoFromJs('已加入購物車');
                        }
                        else
                        {
                            alert('已加入購物車');
                        }
                    }
                },
                error:function()//失敗就...
                {
                }
            });
        }
    });
	
	function dialod_res(t)
	{
		if(t == 'yes')
		{
			$.ajax
			({
				url:"ajax.php", //接收頁
				type:"POST", //POST傳輸
				data:{type:"favorite", pid:pid}, // key/value
				dataType:"text", //回傳形態
				success:function(i) //成功就....
				{
					if(i == 0)
					{
						$("#tr"+pid).remove();
						window.javatojs.showInfoFromJs('已移除追蹤清單');
					}
				},
				error:function()//失敗就...
				{
				}
			});
		}
	}
</script>
