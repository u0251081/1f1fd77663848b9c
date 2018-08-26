<?php

use Base17Mai\Member;
use function Base17Mai\take;

@$pid = $_GET['pid'];
$product_sql = "SELECT * FROM product_class AS a JOIN product AS b using(productID) WHERE a.pid='$pid' AND b.Prelease = 1;";
$product_res = mysql_query($product_sql);
?>
<!-- product category -->
<section id="aa-product-category" class='product_content3'>
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-8 col-md-push-3">
                <div class="aa-product-catg-content">
                    <div class="aa-product-catg-body ax">
                        <?php
                        if (mysql_num_rows($product_res) > 0) {
                            while (@$product_row = mysql_fetch_array($product_res)) {
                                $trackStatus = (new Member())->checkTrackStatus($member_id, $product_row['id']);
                                if ($product_row['id'] != "") {
                                    ?>
                                    <div class="col-md-4 col-sm-4">
                                        <article class="aa-latest-blog-single" style="height: 450px;">
                                            <figure class="aa-blog-img">
                                                <a class="aa-product-img"
                                                   href="index.php?url=product_detail&product_id=<?php echo $product_row['productID']; ?>">
                                                    <?php
                                                    $sql = "SELECT * FROM productimage WHERE productID='" . $product_row['productID'] . "' AND Cover = 1;";
                                                    $res = mysql_query($sql);
                                                    $row = mysql_fetch_array($res);
                                                    if ($row['id'] != "") {
                                                        ?>
                                                        <img src="admin/<?php echo $row['picture']; ?>" width="250"
                                                             height="300">
                                                        <?php
                                                    }
                                                    ?>
                                                </a>
                                            </figure>
                                            <div class="aa-blog-info">
                                                <h3 class="aa-blog-title">
                                                    <a href="index.php?url=product_detail&id=<?php echo $product_row['id']; ?>&pid=<?php echo $pid; ?>"><?php echo $product_row['PName']; ?></a>
                                                </h3>
                                                <?php
                                                // $sql = "SELECT * FROM price WHERE p_id='" . $product_row['product_id'] . "' AND sell_id='1'";
                                                // $res = mysql_query($sql);
                                                // $row = mysql_fetch_array($res);
                                                if ($product_row['id'] != "") {
                                                    ?>
                                                    <span class="aa-product-price"><?php echo "定價： NT$" . $product_row['unitPrice']; ?></span>
                                                    <br>
                                                    <span class="aa-product-price"><?php echo "點數： " . $product_row['bonus']; ?></span>
                                                    <br>
                                                    <p class="aa-product-descrip"><?php echo $product_row['productInformation']; ?></p>

                                                    <?= $trackStatus ?>


                                                    <a href="javascript:void(0);"
                                                       id="cart_btn<?php echo $product_row['id']; ?>"
                                                       onclick="add_cart(<?php echo $product_row['id']; ?>)">
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
                        } else {
                            echo '<center><span style="color:red;">很抱歉，此分類暫無商品</span></center>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!--商品頁面左邊選單-->
            <div class="col-lg-3 col-md-3 col-sm-4 col-md-pull-9">
                <aside class="aa-sidebar">
                    <!-- single sidebar -->
                    <div class="aa-sidebar-widget hidden-xs">
                        <h3>其他類別</h3>
                        <ul class="aa-catg-nav">
                            <?php
                            $sql = "SELECT * FROM class WHERE id='$pid' AND id = parent_id";
                            $res = mysql_query($sql);
                            $row = mysql_fetch_array($res);
                            if ($row['id'] != "") {
                                $real_class_sql = "SELECT * FROM class WHERE NOT id='$pid' AND id = parent_id";
                                $real_class_res = mysql_query($real_class_sql);
                                while ($real_class_row = mysql_fetch_array($real_class_res)) {
                                    ?>
                                    <li>
                                        <a href="index.php?url=product&pid=<?php echo $real_class_row['id']; ?>"><?php echo $real_class_row['name']; ?></a>
                                    </li>
                                    <?php
                                }
                            } else {
                                $sql = "SELECT * FROM class WHERE id='$pid' AND id != parent_id";
                                $res = mysql_query($sql);
                                $row = mysql_fetch_array($res);
                                if ($row['id'] != "") {
                                    $real_class_sql = "SELECT * FROM class WHERE NOT id='$pid' AND parent_id='" . $row['parent_id'] . "' AND id != parent_id";
                                    $real_class_res = mysql_query($real_class_sql);
                                    if (mysql_num_rows($real_class_res) > 0) {
                                        while ($real_class_row = mysql_fetch_array($real_class_res)) {
                                            ?>
                                            <li>
                                                <a href="index.php?url=product&pid=<?php echo $real_class_row['id']; ?>"><?php echo $real_class_row['name']; ?></a>
                                            </li>
                                            <?php
                                        }
                                    } else {
                                        echo '很抱歉，已無其他相關類別';
                                    }
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="aa-sidebar-widget hidden-xs">
                        <h3>瀏覽紀錄</h3>
                        <div class="aa-recently-views" style="height: 700px;">
                            <ul>
                                <?php
                                @$aa = implode(",", $_SESSION['history_ary']);
                                //echo $aa;
                                $sql = "SELECT *,a.id AS aid FROM product AS a JOIN product_img AS b ON a.id = b.p_id WHERE a.id IN(" . $aa . ") AND is_main='1' AND a.added='1' LIMIT 0,5";
                                $res = mysql_query($sql);
                                while (@$row = mysql_fetch_array($res)) {
                                    ?>
                                    <li>
                                        <a href="index.php?url=product_detail&product_id=<?php echo $row['aid']; ?>"
                                           class="aa-cartbox-img">
                                            <img src="admin/<?php echo $row['picture']; ?>">
                                        </a>
                                        <div class="aa-cartbox-info">
                                            <h4><?php echo $row['p_name']; ?></h4>
                                            <h4><?php echo $row['p_introduction']; ?></h4>
                                        </div>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
            <!--商品頁面左邊選單-->
        </div>
    </div>
</section>
<!-- / product category -->

<script>
    function favorite(id) {
        ajax17mai('Member', 'Favorite', {}, {memberNO: '<?= $member_id ?>', productID: id});
    }

    function add_favorite(id) {
        $.ajax
        ({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "favorite", pid: id}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                if (i == 1) {
                    $("#fav_btn" + id).find("img").attr('src', 'img/icon/add.png');
                }
                else if (i == 0) {
                    $("#fav_btn" + id).find("img").attr('src', 'img/icon/clean.png');
                }
                else {
                    if ($(window).width() < 767) {
                        window.javatojs.showInfoFromJs('請先登入或成為會員，才能使用此功能');
                    }
                    else {
                        alert('請先登入或成為會員，才能使用此功能');
                    }
                }
            },
            error: function ()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    }

    function add_cart(id) {
        $.ajax
        ({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "cart", pid: id}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                if (i == 1) {
                    $("#cart_btn" + id).find("img").attr('src', 'img/icon/add_cart.png');
                }
                else if (i == 0) {
                    $("#cart_btn" + id).find("img").attr('src', 'img/icon/clean_cart.png');
                }
                else {
                    if ($(window).width() < 767) {
                        window.javatojs.showInfoFromJs('請先登入或成為會員，才能使用此功能');
                    }
                    else {
                        alert('請先登入或成為會員，才能使用此功能');
                    }
                }
            },
            error: function ()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    }

    $(function () {
        $.ajax
        ({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "favorite_search"}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                var id = i.split(",");
                for (var n = 0; n < id.length; n++) {
                    $("#fav_btn" + id[n]).find("img").attr('src', 'img/icon/add.png');
                }
            },
            error: function ()//失敗就...
            {
                //alert("ajax失敗");
            }
        });

        $.ajax
        ({
            url: "ajax.php", //接收頁
            type: "POST", //POST傳輸
            data: {type: "cart_search"}, // key/value
            dataType: "text", //回傳形態
            success: function (i) //成功就....
            {
                var id = i.split(",");
                for (var n = 0; n < id.length; n++) {
                    $("#cart_btn" + id[n]).find("img").attr('src', 'img/icon/add_cart.png');
                }
            },
            error: function ()//失敗就...
            {
                //alert("ajax失敗");
            }
        });
    });
</script>