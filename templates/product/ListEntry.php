<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2019/1/23
 * Time: 下午 01:55
 */

$detail = 'index.php?url=product_detail&id=' . $id;
?>
    <div class="col-md-4 col-sm-4">
        <article class="aa-latest-blog-single" style="height: 450px;">
            <figure class="aa-blog-img">
                <a class="aa-product-img" href="<?= $detail ?>">
                    <img src="admin/<?= $image ?>" alt="<? $PName ?>" width="250" height="300">
                </a>
            </figure>
            <div class="aa-blog-info">
                <h3 class="aa-blog-title">
                    <a href="<?= $detail ?>"><?= $PName ?></a>
                </h3>
                <span class="aa-product-price">定價： NT$<?= $unitPrice ?></span>
                <br>
                <span class="aa-product-price">點數： <?= $bonus ?></span>
                <br>
                <p class="aa-product-descrip"><?= $description ?></p>

                <?= $trackStatus ?>
                <a href="javascript:void(0);" id="cart_btn<?= $id ?>"
                   onclick="add_cart(<?= $id ?>)">
                    <img src="img/icon/clean_cart.png">
                </a>
            </div>
        </article>
        <br>
    </div>
<?php