<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li><a href="index.php?url=manager_center">行銷經理專區</a></li>
                <li class="active">商品管理</li>
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
                                        <th>商品圖片</th>
                                        <th>商品名稱</th>
                                        <th>商品成本</th>
                                        <th>商品總數量</th>
                                        <th width=10%">販售金額</th>
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

                                    $sql = "SELECT * FROM sales_agents AS a JOIN product AS b ON a.p_id = b.id WHERE a.manager_id ='".$_SESSION['manager_no']."'";
                                    $res = mysql_query($sql);
                                    while($row = mysql_fetch_array($res))
                                    {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $img = "SELECT picture FROM product_img WHERE p_id='".$row['p_id']."' AND is_main='1'";
                                                $img_res = mysql_query($img);
                                                $img_row = mysql_fetch_array($img_res);
                                                ?>
                                                <img src="admin/<?php echo $img_row['picture']; ?>">
                                            </td>
                                            <td>
                                                <a class="aa-cart-title" href="index.php?url=product_detail&id=<?php echo $row['p_id']; ?>">
                                                    <?php
                                                    $product = "SELECT p_name FROM product WHERE id='".$row['p_id']."'";
                                                    $product_res = mysql_query($product);
                                                    $product_row = mysql_fetch_array($product_res);
                                                    echo $product_row['p_name'];
                                                    ?>
                                                </a>
                                                <span name="pid" style="display: none;"><?php echo $row['p_id']; ?></span>
                                            </td>
                                            <td name="price">
                                                <?php
                                                    $price_sql = "SELECT price FROM price WHERE p_id='".$row['p_id']."'";
                                                    $price_res = mysql_query($price_sql);
                                                    $price_row = mysql_fetch_array($price_res);
                                                    echo $price_row['price'];
                                                ?>
                                            </td>
                                            <td><?php echo $row['sales_qty']; ?></td>
                                            <td>
                                                <?php
                                                $sql2 = "SELECT web_price FROM price WHERE p_id='".$row['p_id']."' AND sell_id='2' AND sell_account='".$_SESSION['manager_no']."'";
                                                $res2 = mysql_query($sql2);
                                                $row2 = mysql_fetch_array($res2);
                                                ?>
                                                <input style="width:100px;" type="text" name="mg_sell_price[]" value="<?php echo $row2['web_price']; ?>">
                                                <input type="hidden" name="p_id[]" value="<?php echo $row['p_id']; ?>">
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="6">
                                            <input class="aa-cart-view-btn" id="btn" type="submit" value="設定金額">
                                        </td>
                                    </tr>
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
<?php
@$p_id = $_POST['p_id']; //商品ID
@$mg_sell_price = $_POST['mg_sell_price']; //行銷經理填寫的商品販售金額
for($i=0; $i<count($p_id); $i++)
{
    if($mg_sell_price[$i] != "")
    {
        $sql = "SELECT id FROM price WHERE p_id='".$p_id[$i]."' AND sell_id='2' AND sell_account='".$_SESSION['manager_no']."'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);
        if($row['id'] == "")
        {
            $s = "INSERT INTO price SET p_id='".$p_id[$i]."', web_price='".$mg_sell_price[$i]."', sell_id='2', sell_account='".$_SESSION['manager_no']."'";
            mysql_query($s);
            ?>
            <script>
                alert('設定完成');
                location.href='index.php?url=m_product_mg';
            </script>
            <?php
        }
        else
        {
            $u = "UPDATE price SET web_price='".$mg_sell_price[$i]."' WHERE sell_id='2' AND p_id='".$p_id[$i]."' AND sell_account='".$_SESSION['manager_no']."'";
            mysql_query($u);
            ?>
            <script>
                alert('設定完成');
                location.href='index.php?url=m_product_mg';
            </script>
            <?php
        }
    }
    else
    {
        continue;
    }
}
?>
<script>
    $(function ()
    {
        $("#aa-slider").hide();
        $("html,body").scrollTop(70);
    });
</script>