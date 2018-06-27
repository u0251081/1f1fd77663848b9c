<!-- 會員訂閱一位或多位行銷經理後，載入此頁面，可看到一位或多位行銷經理 -->
<section id="aa-promo">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="aa-promo-area">
                    <div class="row">

                        <!-- 行銷經理tab -->
                        <ul class="nav nav-tabs aa-products-tab">
                            <?php
                            //原本的$nav_row['id']是指商品類別的大類
                            //現在新的要改成行銷經理的no，再透過no去切換每個行銷經理專屬的商品內容
                            $nav_sql = "SELECT * FROM lower_limit WHERE member_id='$member_id' ORDER BY rand()";
                            $nav_res = mysql_query($nav_sql);
                            while($nav_row = mysql_fetch_array($nav_res))
                            {
                                if(strlen($nav_row['mg_member_id']) > 10)
                                {
                                    $s = "SELECT * FROM fb WHERE fb_id='".$nav_row['mg_member_id']."'";
                                    $s_res = mysql_query($s);
                                    $s_row = mysql_fetch_array($s_res);
                                    ?>
                                    <li class="tab_list"><a href="#tab_<?php echo $nav_row['manager_id']; ?>" data-toggle="tab"><?php echo $s_row['fb_name']; ?></a></li>
                                    <?php
                                }
                                else
                                {
                                    $s = "SELECT * FROM member WHERE member_no='".$nav_row['mg_member_id']."'";
                                    $s_res = mysql_query($s);
                                    $s_row = mysql_fetch_array($s_res);
                                    ?>
                                    <li class="tab_list"><a href="#tab_<?php echo $nav_row['manager_id']; ?>" data-toggle="tab"><?php echo $s_row['m_name']; ?></a></li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                        <!-- 行銷經理tab -->

                        <!--這裡循環商品內容-->
                        <div class="tab-content ax">
                            <?php
                            $nav_res = mysql_query($nav_sql);
                            while($nav_row = mysql_fetch_array($nav_res))
                            {
                                ?>
                                <div class="tab-pane fade in active" id="tab_<?php echo $nav_row['manager_id']; ?>">
                                    <ul class="aa-product-catg">
                                        <?php
                                        $sql = "SELECT * FROM sales_agents AS a JOIN product AS b ON a.p_id = b.id WHERE a.manager_id='".$nav_row['manager_id']."'";
                                        $res = mysql_query($sql);
                                        while($row = mysql_fetch_array($res))
                                        {
                                            ?>
                                            <div class="col-md-4 col-sm-4">
                                                <article class="aa-latest-blog-single">
                                                    <figure class="aa-blog-img">
                                                        <a class="aa-product-img" href="index.php?url=product_detail&product_id=<?php echo $row['p_id']; ?>">
                                                            <?php
                                                            $sql2 = "SELECT * FROM product_img WHERE p_id='".$row['p_id']."' AND is_main='1'";
                                                            $res2 = mysql_query($sql2);
                                                            $row2 = mysql_fetch_array($res2);
                                                            if($row2['id'] != "")
                                                            {
                                                                ?>
                                                                <img src="<?php echo "admin/".$row2['picture']; ?>" width="250" height="300">
                                                                <?php
                                                            }
                                                            ?>
                                                        </a>
                                                    </figure>
                                                    <div class="aa-blog-info">
                                                        <h3 class="aa-blog-title"><a href="index.php?url=product_detail&id=<?php echo $row['p_id']; ?>"><?php echo $row['p_name']; ?></a></h3>
                                                        <?php
                                                        $sql3 = "SELECT * FROM price WHERE p_id='".$row['p_id']."' AND sell_id='2' AND sell_account='".$nav_row['manager_id']."'";
                                                        $res3 = mysql_query($sql3);
                                                        $row3 = mysql_fetch_array($res3);
                                                        if($row3['id'] != "")
                                                        {
                                                            ?>
                                                            <span class="aa-product-price"><?php echo "網路價： NT$".$row3['web_price']; ?></span><br>
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
                                    </ul>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <!--這裡循環商品內容-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- / 會員訂閱一位或多位行銷經理後，載入此頁面，可看到一位或多位行銷經理 -->
<script>
    $(function ()
    {
        $(".tab_list").find("a").first().click();
    })
</script>