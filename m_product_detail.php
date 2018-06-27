<script src="js/clipboard.min.js"></script>
<style>
    .my_cart_btn
    {
        border: 1px solid #ccc;
        color: #555;
        display: inline-block;
        font-size: 14px;
        font-weight: bold;
        letter-spacing: 0.5px;
        margin-top: 5px;
        padding: 10px 15px;
        text-transform: uppercase;
        transition: all 0.5s ease 0s;
        cursor:pointer;
    }

    .my_add_cart
    {
        border-color: #CC6060;
        color:#CC6060;
    }
</style>

<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.html">首頁</a></li>
                <li><a href="#">主類別</a></li>
                <li class="active">子類別</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->

<!-- product category -->
<section id="aa-product-details">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="aa-product-details-area">
                    <div class="aa-product-details-content">
                        <div class="row">
                            <!-- Modal view slider -->
                            <?php
                            $img_ary = array();
                            @$id = $_GET['id'];
                            $sql = "SELECT * FROM product_img WHERE p_id='$id'";
                            $res = mysql_query($sql);
                            while($row = mysql_fetch_array($res))
                            {
                                $img_ary[] = $row['picture'];
                            }
                            ?>
                            <div class="col-md-5 col-sm-5 col-xs-12">
                                <div class="aa-product-view-slider">
                                    <div id="demo-1" class="simpleLens-gallery-container">
                                        <div class="simpleLens-container">
                                            <div class="simpleLens-big-image-container">

                                                <a data-lens-image="admin/<?php echo $img_ary[0]; ?>" class="simpleLens-lens-image">
                                                    <img src="admin/<?php echo $img_ary[0]; ?>" class="simpleLens-big-image">
                                                </a>

                                            </div>
                                        </div>

                                        <div class="simpleLens-thumbnails-container">
                                            <a data-big-image="admin/<?php echo $img_ary[0]; ?>"
                                               data-lens-image="admin/<?php echo $img_ary[0]; ?>"
                                               class="simpleLens-thumbnail-wrapper" href="javascript:void(0);">
                                                <img src="admin/<?php echo $img_ary[0]; ?>" width="50" height="55">
                                            </a>

                                            <a data-big-image="admin/<?php echo $img_ary[1]; ?>"
                                               data-lens-image="admin/<?php echo $img_ary[1]; ?>"
                                               class="simpleLens-thumbnail-wrapper" href="javascript:void(0);">
                                                <img src="admin/<?php echo $img_ary[1]; ?>" width="50" height="55">
                                            </a>

                                            <a data-big-image="admin/<?php echo $img_ary[2]; ?>"
                                               data-lens-image="admin/<?php echo $img_ary[2]; ?>"
                                               class="simpleLens-thumbnail-wrapper" href="javascript:void(0);">
                                                <img src="admin/<?php echo $img_ary[2]; ?>" width="50" height="55">
                                            </a>

                                            <a data-big-image="admin/<?php echo $img_ary[3]; ?>"
                                               data-lens-image="admin/<?php echo $img_ary[3]; ?>"
                                               class="simpleLens-thumbnail-wrapper" href="javascript:void(0);">
                                                <img src="admin/<?php echo $img_ary[3]; ?>" width="50" height="55">
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal view content -->
                            <div class="col-md-7 col-sm-7 col-xs-12">
                                <?php
                                $sql = "SELECT * FROM product AS a JOIN(SELECT p_id,web_price,sell_id FROM price) AS b ON a.id = b.p_id AND b.sell_id='1' WHERE a.id='$id'";
                                $res = mysql_query($sql);
                                $row = mysql_fetch_array($res);
                                ?>
                                <div class="aa-product-view-content">
                                    <h3><?php echo $row['p_name']; ?></h3>
                                    <div class="aa-price-block">
                                        <span class="aa-product-view-price"><?php echo "NT$<span id='price'>".$row['web_price']."</span>"; ?></span>
                                    </div>
                                    <p><?php echo $row['p_introduction']; ?></p>

                                    <h4>規格</h4>
                                    <div class="aa-prod-view-size">
                                        <?php
                                        $standard = "SELECT * FROM standard WHERE p_id='".$row['p_id']."'";
                                        $standard_res = mysql_query($standard);
                                        while($standard_row = mysql_fetch_array($standard_res))
                                        {
                                            ?>
                                            <a href="#"><?php echo $standard_row['standard']; ?></a>
                                            <span style="display: none;"><?php echo $standard_row['qty']; ?></span>
                                            <?php
                                        }
                                        ?>
                                    </div>

                                    <h4>商品分享</h4>
                                    <div class="aa-color-tag">
                                        <a href="javascript:void(0);" id="fb_btn" target="_blank"><img src="img/icon/fb.png"></a>
                                        <a href="http://line.naver.jp/R/msg/text/?{hi}" target="_blank"><img src="img/icon/line.jpg" width="32" height="32"></a>
                                        <a href="javascript:void(0);" id="url_btn" data-toggle="modal" data-target="#copy_url_modal"><img src="img/icon/url.png"></a>
                                    </div>

                                    <div class="aa-prod-quantity">
                                        購買數量
                                        <select id="p_qty" name="p_qty" style="width: 55px;">
                                            <?php
                                            for($i=1; $i<= $row['p_qty']; $i++)
                                            {
                                                ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <p class="aa-prod-category">
                                            剩餘: <a href="javascript:void(0);"><?php echo $row['p_qty']; ?></a>
                                        </p>
                                    </div>
                                    <div class="aa-prod-view-bottom">
                                        <span class="my_cart_btn" name="add_cart_btn" b_type="0">加入購物車</span>&nbsp;&nbsp;&nbsp;
                                        <span class="my_cart_btn" name="add_cart_btn" b_type="1">加入追蹤清單</span>
                                        <span class="my_cart_btn" name="add_cart_btn" b_type="2">直接購買</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <span id="pid" style="display: none;"><?php echo @$id; ?></span>

                    <div class="aa-product-details-bottom">
                        <ul class="nav nav-tabs" id="myTab2">
                            <li><a href="#description" data-toggle="tab">詳細內容</a></li>
                            <li><a href="#review" data-toggle="tab">商品廣告</a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="description">
                                <p>
                                    <?php
                                    echo $row['p_info'];
                                    ?>
                                </p>
                                <p>
                                    <?php
                                    echo $row['p_use'];
                                    ?>
                                </p>
                                <p>
                                    <?php
                                    echo $row['p_notes'];
                                    ?>
                                </p>
                                <br>
                            </div>

                            <div align="center" class="tab-pane fade " id="review">
                                <?php
                                if($row['youtube'] == "")
                                {
                                    echo '<br><p style="color: red;">很抱歉，此商品目前沒有廣告</p><br>';
                                }
                                else
                                {
                                    ?>
                                    <br>
                                    <div class="video-container">
                                        <iframe src="<?php echo $row['youtube']; ?>" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <br><br><br>
                        </div>
                    </div>
                    <!-- Related product -->
                </div>
            </div>
        </div>
    </div>
</section>

<span id='url' style='display:none;'><?php echo @$_GET['url']; ?></span>
<span id='pid' style='display:none;'><?php echo @$_GET['id']; ?></span>
<span id='manager_id' style='display:none;'><?php echo @$_SESSION['manager_no']; ?></span>

<!-- / product category -->

<?php
if(isset($_SESSION['history_ary']))
{
    $key = array_search($id,$_SESSION['history_ary']);
    if($key !== false)
    {
        //unset($_SESSION['history_ary'][$key]);
        //array_values($_SESSION['history_ary']);
    }
    else
    {
        array_push($_SESSION['history_ary'],$id);
    }
}
else
{
    $_SESSION['history_ary'] = array();
    array_push($_SESSION['history_ary'],$id);
}
?>

<!-- 彈出視窗 -->
<div class="modal fade" id="copy_url_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4>請複製下列網址，並把它分享給您的朋友，謝謝</h4>
                <div id="copy_url"></div>
                <button id='copy_btn' style="margin: 10px;" class="btn btn-primary pull-right" data-clipboard-target="#copy_url">複製</button>
                <span id='copy_success' style="display: none;">複製成功</span>
            </div>
        </div><!-- /.彈出視窗內容 -->
    </div><!-- /.彈出視窗結束 -->
</div>

<script>
    var clipboard = new Clipboard('#copy_btn');
    clipboard.on('success', function(e)
    {
        $("#copy_success").show();
    });

    $("#url_btn").click(function()
    {
        var manager_id = $("#manager_id").text();
        if(manager_id != "")
        {
            $("#copy_success").hide();
            $("#copy_url").text(window.location.href + '&manager_id=' + manager_id);
        }
        else
        {
            $("#copy_success").hide();
            $("#copy_url").text(window.location.href);
        }
    });

    $("#fb_btn").click(function()
    {
        /*要分享的url為參數?u後面內容，可自訂*/
        $(this).attr("href","http://www.facebook.com/sharer.php?u=" + window.location.href);
    });

    $("span[name='add_cart_btn']").click(function()
    {
        var pid = $("#pid").text();
        var p_qty = $("#p_qty").val();
        var p_price = $("#price").text();
        if($(this).attr('b_type') == 0)
        {
            $.ajax
            ({
                url:"ajax.php", //接收頁
                type:"POST", //POST傳輸
                data:{type:"p_detial_cart",pid:pid,p_qty:p_qty,p_price:p_price}, // key/value
                dataType:"text", //回傳形態
                success:function(i) //成功就....
                {
                    if(i == 1)
                    {
                        alert('加入購物車成功');
                        $(this).attr('class','my_cart_btn my_add_cart');
                    }
                    else if(i == 2)
                    {
                        alert('請先登入才能使用此功能');
                    }
                },
                error:function()//失敗就...
                {
                    //alert("ajax失敗");
                }
            });
        }
        else if($(this).attr('b_type') == 1)
        {
            $.ajax
            ({
                url:"ajax.php", //接收頁
                type:"POST", //POST傳輸
                data:{type:"p_detial_favorite",pid:pid}, // key/value
                dataType:"text", //回傳形態
                success:function(i) //成功就....
                {
                    if(i == 1)
                    {
                        alert('商品已再追蹤清單中');
                    }
                    else if(i == 2)
                    {
                        alert('追蹤成功');
                        if($("span[name='add_cart_btn']").attr('b_type') == 1)
                        {
                            $(this).attr('class','my_cart_btn my_add_cart');
                        }
                    }
                    else if(i == 3)
                    {
                        alert('請先登入才能使用此功能');
                    }
                },
                error:function()//失敗就...
                {
                    //alert("ajax失敗");
                }
            });
            //$(this).toggleClass('my_add_cart');
        }
        else if($(this).attr('b_type') == 2)
        {
            $(this).toggleClass('my_add_cart');
        }
    });
    $('.modal-body').css('max-height', window.innerHeight).css('overflow', 'auto');
    $("html,body").scrollTop(750);
</script>