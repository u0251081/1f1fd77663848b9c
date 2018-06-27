<!-- 商品 -->
<?php ?>
<section id="aa-promo" class="product_content">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="aa-promo-area">
                    <div class="row">
                       
                        <div class="tab-content ax">
                           
                            <?php
                            $sql = "SELECT * FROM supplier";
                            $res = mysql_query($sql);
                            while($row = mysql_fetch_array($res))
                            {
                                ?>
                                <div class="col-md-4 col-sm-4">
                                    <article class="aa-latest-blog-single" style="height:350px;">
                                        <figure class="aa-blog-img">
                                            <a class="aa-product-img" href="index.php?url=introduce&supplier_id=<?php echo $row['id']; ?>">
                                                <?php
                                                $sql2 = "SELECT big_img FROM supplier WHERE id='".$row['id']."'";
                                                $res2 = mysql_query($sql2);
                                                $row2 = mysql_fetch_array($res2);
                                                if($row2['big_img'] != "")
                                                {
                                                    ?>
                                                    <img src="admin/<?php echo $row2['big_img']; ?>" width="250" height="300">
                                                    <?php
                                                }
                                                ?>
                                            </a>
                                        </figure>


                                        <div class="supplier-img">
                                            <a class="supplier-img" href="index.php?url=introduce&supplier_id=<?php echo $row['id']; ?>">
                                          <?php
                                                $sql3 = "SELECT small_img FROM supplier WHERE id='".$row['id']."'";
                                                $res3 = mysql_query($sql3);
                                                $row3 = mysql_fetch_array($res3);
                                                if($row3['small_img'] != "")
                                                {
                                                    ?>
                                           <img src="admin/<?php echo $row3['small_img']; ?>">
                                                    <?php
                                                }

                                                ?>
                                            </a>
                                        </div>
                                        <div class="aa-blog-info">
                                            <center><h3 class="aa-blog-title" style="color: red;"><a href="index.php?url=introduce&supplier_id=<?php echo $row['id']; ?>" style="color: red;"><?php echo $row['supplier_title']; ?></a></h3></center>
                                            <?php
                                            $sql3 = "SELECT * FROM supplier WHERE id='".$row['id']."'";
                                            $res3 = mysql_query($sql3);
                                            $row3 = mysql_fetch_array($res3);
                                            if($row3['id'] != "")
                                            {
                                                ?>
                                                <center><span class="aa-product-price" style="color:#228b22; margin-bottom: 20px;"><?php echo $row3['supplier_name']; ?></span><br>
                                                    <span class="aa-product-price"><?php echo $row3['info']; ?></span>
                                                </center>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </article>
                                    <hr>
                                    <br>
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
<!-- / 商品 -->