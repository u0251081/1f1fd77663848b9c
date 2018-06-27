<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li><a href="index.php?url=member_center">會員專區</a></li>
                <li class="active">購物車</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->

<!-- Cart view section -->
<section id="cart-view">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="cart-view-area" style="margin-bottom: 100px; padding-top: 1px;">
                    <div class="cart-view-table">
                        <form method="post">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>取消</th>
                                        <th>名稱</th>
                                        <th>狀態</th>
                                        <th>金額</th>
                                        <th>數量</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if(isset($_SESSION["member_no"]))
                                    {
                                        $member_id = $_SESSION["member_no"];
                                    }
                                    else
                                    {
                                        $member_id = $_SESSION["fb_id"];
                                    }

                                    $sql = "SELECT * FROM shoping_cart WHERE m_id='".$member_id."'";
                                    $res = mysql_query($sql);
                                    while($row = mysql_fetch_array($res))
                                    {
                                        $product = "SELECT * FROM product WHERE id='".$row['p_id']."'";
                                        $product_res = mysql_query($product);
                                        $product_row = mysql_fetch_array($product_res);
                                        $last = $product_row['rem_qty'];
                                        if($product_row['added'] == 1 && $last > 0)
                                        {
                                            ?>
                                            <tr id="tr<?php echo $row['id']; ?>">
                                                <td>
                                                    <a class="remove" href="javascript:void(0)" name="remove_cart" pid="<?php echo $row['id']; ?>">
                                                        <fa class="fa fa-close"></fa>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a class="aa-cart-title" href="index.php?url=product_detail&product_id=<?php echo $row['p_id']; ?>">
                                                        <?php
                                                        echo $product_row['p_name'];
                                                        ?>
                                                    </a>
                                                    <span name="pid" style="display: none;"><?php echo $row['p_id']; ?></span>
                                                </td>
                                                <td><a><?php echo '上架'; ?></a></td>
                                                <td name="price" id="td<?php echo $row['id']; ?>">
                                                    <?php
                                                    $price_sql = "SELECT web_price FROM price WHERE p_id='".$row['p_id']."'";
                                                    $price_res = mysql_query($price_sql);
                                                    $price_row = mysql_fetch_array($price_res);
                                                    echo @$price_row['web_price'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <input class="aa-cart-quantity" type="number" name="qty" value="<?php echo $row['p_qty']; ?>">
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="6" class="aa-cart-view-bottom">
                                            <div class="aa-cart-coupon">
                                                <input class="aa-cart-view-btn" type="button" onclick="location.href='index.php'" value="繼續購物">
                                            </div>
                                            <input class="aa-cart-view-btn" id="btn" type="button" value="修改數量">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <!-- Cart Total view -->
                        <div class="cart-view-total">
                            <h4>購物車合計</h4>
                            <table class="aa-totals-table">
                                <tbody>
                                <tr>
                                    <th>合計</th>
                                    <td>NT$<span id="price_total"></span></td>
                                </tr>
                                </tbody>
                            </table>
                            <a href="javascript:void(0)" id="to_pay" class="aa-cart-view-btn">前往結帳</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<form id="cart_form" action="index.php?url=pay_check" method="post">
    <input type="hidden" name="cart_pid" id="cart_pid">
    <input type="hidden" name="cart_price" id="cart_price">
    <input type="hidden" name="cart_qty" id="cart_qty">
    <input type="hidden" name="manager_id" value="<?php echo @$_SESSION['share_manager_no']; ?>">
    <input type="hidden" name="fb_no" value="<?php echo @$_SESSION['share_fb_no']; ?>">
    <input type="hidden" name="vip_id" value="<?php echo @$_SESSION['share_vip_id']; ?>">
</form>
<!-- / Cart view section -->

<script>
    var pid;
    var price_ary = Array();
    $("td[name='price']").each(function(i)
    {
        price_ary[i] = $(this).text().trim();
    });

    var qty_ary = Array();
    $("input[name='qty']").each(function(i)
    {
        qty_ary[i] = $(this).val().trim();
    });

    $(function ()
    {
        $("#aa-slider").remove();
        $("html,body").scrollTop(70);
    });

    total(price_ary,qty_ary);

    function total(price,qty)
    {
        var num = 0;
        for(var i=0; i<price.length; i++)
        {
            num += price[i] * qty[i];
        }
        $("#price_total").text(num);
    }

    $("#btn").click(function()
    {
        var pid_arr = Array();
        $("span[name='pid']").each(function(i)
        {
            pid_arr[i] = $(this).text().trim();
        });

        var qty_arr = Array();
        $("input[name='qty']").each(function(i)
        {
            qty_arr[i] = $(this).val().trim();
        });

        var price_arr = Array();
        $("td[name='price']").each(function(i)
        {
            price_arr[i] = $(this).text().trim(price_arr);
        });

        $.ajax
        ({
            url:"ajax.php", //接收頁
            type:"POST", //POST傳輸
            data:{type:"update_cart", pid_arr:pid_arr,qty_arr:qty_arr,price_arr:price_arr}, // key/value
            dataType:"text", //回傳形態
            success:function(i) //成功就....
            {
                if(i == 1)
                {
                    if($("#device").text() == 'mobile')
                    {
                        window.javatojs.showInfoFromJs('更新成功');
                        window.location.href='index.php?url=cart';
                    }
                    else
                    {
                        alert('更新成功');
                        window.location.href='index.php?url=cart';
                    }
                }
            },
            error:function()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    });

    $("a[name='remove_cart']").click(function()
    {
        pid = $(this).attr('pid');
        if($("#device").text() == 'mobile')
        {
            window.javatojs.myconfirm('cart');
        }
        else
        {
            if(confirm('是否要從您的追蹤清單內移除?'))
            {
                var price = $(this).parent().siblings("td[name='price']").text().trim();
                var qty = $(this).parent().siblings('td').find('input[name="qty"]').val();
                var price_total = $("#price_total").text();
                var num = price * qty;
                $("#price_total").text(price_total - num);
                $.ajax
                ({
                    url:"ajax.php", //接收頁
                    type:"POST", //POST傳輸
                    data:{type:"remove_cart", pid:pid}, // key/value
                    dataType:"text", //回傳形態
                    success:function(i) //成功就....
                    {
                        if(i == 1)
                        {
                            $("#tr"+pid).remove();
                        }
                        else
                        {
                            alert('意外的錯誤，請重新操作');
                        }
                    },
                    error:function()//失敗就...
                    {
                        //alert("ajax失敗");
                    }
                });
            }
        }
    });

    function dialod_res(t)
    {
        if(t == 'yes')
        {
            var price = $("a[name='remove_cart']").parent().siblings("td#td"+pid).text().trim();
            var qty = $("a[name='remove_cart']").parent().siblings("td#td"+pid).next().find('input[name="qty"]').val();
            var price_total = $("#price_total").text();
            var num = price * qty;
            $("#price_total").text(price_total - num);
            $.ajax
            ({
                url:"ajax.php", //接收頁
                type:"POST", //POST傳輸
                data:{type:"remove_cart", pid:pid}, // key/value
                dataType:"text", //回傳形態
                success:function(i) //成功就....
                {
                    if(i == 1)
                    {
                        $("#tr"+pid).remove();
                        window.javatojs.showInfoFromJs('已移除購物車');
                    }
                },
                error:function()//失敗就...
                {
                    //alert("ajax失敗");
                }
            });
        }
    }

    $("#to_pay").click(function()
    {
        var p_t = $("#price_total").text().trim();
        if(p_t <= 0)
        {
            if($("#device").text() == 'mobile')
            {
                window.javatojs.showInfoFromJs('購物車內沒有商品');
            }
            else
            {
                alert('購物車內沒有商品');
            }
        }
        else
        {
            var pid_arr = Array();
            $("span[name='pid']").each(function(i)
            {
                pid_arr[i] = $(this).text().trim();
            });
            //pid_arr =>商品id陣列
            //price_ary =>單價陣列
            //qty_ary =>數量陣列
            $("#cart_pid").val(pid_arr);
            $("#cart_price").val(price_ary);
            $("#cart_qty").val(qty_ary);

            $("form#cart_form").submit();
        }
    });
</script>
