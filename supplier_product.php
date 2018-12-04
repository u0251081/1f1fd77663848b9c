<?php
@$sid = $_GET['sid'];
$product_sql = "SELECT * FROM supplier left join product on supplier.id = product.vendorID WHERE supplier.id = '$sid' AND product.Prelease = 1;";
$product_res = mysql_query($product_sql);
?>
<!-- product category -->
<section id="aa-promo" class="product_content" style="margin-bottom: 150px;">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="aa-promo-area">
                    <div class="row">

                        <div class="tab-content ax">
                        <?php
                        if(mysql_num_rows($product_res) > 0 )
                        {
                            while (@$product_row = mysql_fetch_array($product_res))
                            {
                                if($product_row['id'] != "")
                                {
                                    ?>
                                    <div class="col-md-4 col-sm-4">
                                        <article class="aa-latest-blog-single">
                                            <figure class="aa-blog-img">
                                                <a class="aa-product-img" href="index.php?url=product_detail&product_id=<?php echo $product_row['id']; ?>">

                                                    <?php

                                                    $sql = "SELECT * FROM productimage WHERE Cover = 1 AND productID = '{$product_row['productID']}';";
                                                    $res = mysql_query($sql);
                                                    $row = mysql_fetch_array($res);
                                                    if($row['id'] != "")
                                                    {
                                                        ?>
                                                        <img src="admin/<?php echo $row['picture']; ?>" width="250" height="300">

                                                        <?php
                                                    }
                                                    ?>
                                                </a>
                                            </figure>
                                            <div class="aa-blog-info">
                                                <h3 class="aa-blog-title"><a href="index.php?url=product_detail&product_id=<?php echo $product_row['0']; ?>"><?php echo $product_row['p_name']; ?></a></h3>
                                                <?php
                                                $sql = "SELECT * FROM price WHERE p_id='".$product_row['0']."' AND sell_id='1'";
                                                $res = mysql_query($sql);
                                                $row = mysql_fetch_array($res);
                                                if($row['id'] != "")
                                                {
                                                    ?>
                                                    <span class="aa-product-price"><?php echo "網路價： NT$".$row['web_price']; ?></span><br>
                                                    <p class="aa-product-descrip"><?php echo $product_row['p_introduction']; ?></p>

                                                    <a href="javascript:void(0);" id="fav_btn<?php echo $product_row['0']; ?>" onclick="add_favorite(<?php echo $product_row['0']; ?>)">
                                                        <img src="img/icon/clean.png">
                                                    </a>

                                                    <a href="javascript:void(0);" id="cart_btn<?php echo $product_row['0']; ?>" onclick="add_cart(<?php echo $product_row['0']; ?>)">
                                                        <img src="img/icon/clean_cart.png">
                                                    </a>

                                                    <!--<a href="javascript:void(0);" id="search_btn" data-toggle2="tooltip" data-toggle="modal" data-target="#quick-view-modal">
                                                        <span class="fa fa-search"></span>
                                                    </a>-->
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </article>
                                        <br>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        else
                        {
                            echo '<center><span style="color:red;">很抱歉，此供應商暫無商品</span></center>';
                        }
                        ?>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>


<script>
    function add_favorite(id)
    {
        $.ajax
        ({
            url:"ajax.php", //接收頁
            type:"POST", //POST傳輸
            data:{type:"favorite", pid:id}, // key/value
            dataType:"text", //回傳形態
            success:function(i) //成功就....
            {
                if(i == 1)
                {
                    $("#fav_btn" + id).find("img").attr('src','img/icon/add.png');
                }
                else if(i == 0)
                {
                    $("#fav_btn" + id).find("img").attr('src','img/icon/clean.png');
                }
                else
                {
                    if($(window).width() < 767)
                    {
                        window.javatojs.showInfoFromJs('請先登入或成為會員，才能使用此功能');
                    }
                    else
                    {
                        alert('請先登入或成為會員，才能使用此功能');
                    }
                }
            },
            error:function()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    }

    function add_cart(id)
    {
        $.ajax
        ({
            url:"ajax.php", //接收頁
            type:"POST", //POST傳輸
            data:{type:"cart", pid:id}, // key/value
            dataType:"text", //回傳形態
            success:function(i) //成功就....
            {
                if(i == 1)
                {
                    $("#cart_btn" + id).find("img").attr('src','img/icon/add_cart.png');
                }
                else if(i == 0)
                {
                    $("#cart_btn" + id).find("img").attr('src','img/icon/clean_cart.png');
                }
                else
                {
                    if($(window).width() < 767)
                    {
                        window.javatojs.showInfoFromJs('請先登入或成為會員，才能使用此功能');
                    }
                    else
                    {
                        alert('請先登入或成為會員，才能使用此功能');
                    }
                }
            },
            error:function()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    }

    $(function()
    {
        $.ajax
        ({
            url:"ajax.php", //接收頁
            type:"POST", //POST傳輸
            data:{type:"favorite_search"}, // key/value
            dataType:"text", //回傳形態
            success:function(i) //成功就....
            {
                var id = i.split(",");
                for(var n=0; n < id.length; n++)
                {
                    $("#fav_btn" + id[n]).find("img").attr('src','img/icon/add.png');
                }
            },
            error:function()//失敗就...
            {
                //alert("ajax失敗");
            }
        });

        $.ajax
        ({
            url:"ajax.php", //接收頁
            type:"POST", //POST傳輸
            data:{type:"cart_search"}, // key/value
            dataType:"text", //回傳形態
            success:function(i) //成功就....
            {
                var id = i.split(",");
                for(var n=0; n < id.length; n++)
                {
                    $("#cart_btn" + id[n]).find("img").attr('src','img/icon/add_cart.png');
                }
            },
            error:function()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    });
</script>