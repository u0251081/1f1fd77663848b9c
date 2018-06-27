<!-- 特價商品 -->
<?php echo 123; ?>
<section id="aa-promo">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="aa-promo-area">
                    <div class="row">
                        <!--這裡循環商品內容-->
                        <div class="tab-content ax">
                            <!-- Start men product category -->
                            <?php
                            if(isset($_GET['nav_id']))
                            {
                                $nav_sql = "SELECT * FROM `class` WHERE id IN(".$_GET['nav_id'].")";
                            }
                            $nav_res = mysql_query($nav_sql);
                            while(@$nav_row = mysql_fetch_array($nav_res))
                            {
                                ?>
                                <div class="tab-pane fade in active">
                                    <ul class="aa-product-catg">

                                        <?php
                                        $sql = "SELECT * FROM product AS a JOIN product_class AS b ON a.id = b.product_id AND b.pid='".$nav_row['id']."'";
                                        $res = mysql_query($sql);
                                        while(@$row = mysql_fetch_array($res))
                                        {
                                            ?>
                                            <div class="col-md-4 col-sm-4">
                                                <article class="aa-latest-blog-single">
                                                    <figure class="aa-blog-img">
                                                        <a class="aa-product-img" href="index.php?url=product_detail&product_id=<?php echo $row['id']; ?>">
                                                            <?php
                                                            $sql2 = "SELECT * FROM product_img WHERE p_id='".$row['id']."' AND is_main='1'";
                                                            $res2 = mysql_query($sql2);
                                                            @$row2 = mysql_fetch_array($res2);
                                                            if($row2['id'] != "")
                                                            {
                                                                ?>
                                                                <img src="admin/<?php echo $row2['picture']; ?>" width="250" height="300">
                                                                <?php
                                                            }
                                                            ?>
                                                        </a>
                                                    </figure>
                                                    <div class="aa-blog-info">
                                                        <h3 class="aa-blog-title"><a href="index.php?url=product_detail&id=<?php echo $row['id']; ?>"><?php echo $row['p_name']; ?></a></h3>
                                                        <?php
                                                        $sql3 = "SELECT * FROM price WHERE p_id='".$row['id']."' AND sell_id='1'";
                                                        $res3 = mysql_query($sql3);
                                                        $row3 = mysql_fetch_array($res3);
                                                        if($row3['id'] != "")
                                                        {
                                                            ?>
                                                            <span class="aa-product-price"><?php echo "網路價： NT$".$row3['web_price']; ?></span><br>

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
                                        ?>
                                        <!-- start single product item -->
                                    </ul>
                                </div>
                                <?php
                            }
                            ?>
                            <!-- / men product category -->
                        </div>
                        <!--這裡循環商品內容-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- / 特價商品 -->