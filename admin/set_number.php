<?php
@$p_id = $_GET['p_id']; //商品id
@$m_id = $_GET['m_id']; //經理會員編號
@$mg_id = $_GET['mg_id']; //經理編號
?>
<div class="widget">
    <h4 class="widgettitle">設置給行銷經理的商品數量</h4>
    <div class="widgetcontent">
        <form class="stdform stdform2" method="post">
            <p>
                <label>經理姓名</label>
                <span class="field" style="font-size: large;">
                    <?php
                        if(strlen($m_id) > 10)
                        {
                            $manager_sql = "SELECT fb_name FROM fb WHERE fb_id='$m_id'";
                            $manager_res = mysql_query($manager_sql);
                            $manager_row = mysql_fetch_array($manager_res);
                            echo $manager_row['fb_name'];
                        }
                        else
                        {
                            $manager_sql = "SELECT m_name FROM member WHERE member_no='$m_id'";
                            $manager_res = mysql_query($manager_sql);
                            $manager_row = mysql_fetch_array($manager_res);
                            echo $manager_row['m_name'];
                        }
                    ?>
                </span>
            </p>
            <p>
                <label>商品名稱</label>
                <span class="field" style="font-size: large;">
                    <?php
                    $product_sql = "SELECT p_name FROM product WHERE id='$p_id'";
                    $product_res = mysql_query($product_sql);
                    $product_row = mysql_fetch_array($product_res);
                    echo $product_row['p_name'];
                    ?>
                </span>
            </p>
            <p>
                <label>數量設定</label>
                <span class="field" style="font-size: large;"><input type="text" name="product_qty_set"></span>
            </p>
            <p class="stdformbutton">
                <input type="submit" name="btn" class="btn btn-primary" value="提交">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" onclick="window.location.href='home.php?url=sales_agents';" class="btn" value="返回">
            </p>
        </form>
    </div><!--widgetcontent-->
</div><!--widget-->

<?php
@$product_qty_set = $_POST['product_qty_set'];
if(isset($_POST['btn']))
{
    $sql = "UPDATE sales_agents SET sales_qty='$product_qty_set' WHERE manager_id='$mg_id' AND p_id='$p_id'";
    mysql_query($sql);
    ?>
    <script>
        alert('配置完成');
        location.href='home.php?url=sales_agents';
    </script>
    <?php
}
?>