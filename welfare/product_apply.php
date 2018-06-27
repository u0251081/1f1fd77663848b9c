<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li><a href="index.php?url=manager_center">行銷經理專區</a></li>
                <li class="active">商品申請</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->

<?php

if(isset($_SESSION["member_no"]))
{
    $member_id = $_SESSION["member_no"];
}
else
{
    $member_id = $_SESSION["fb_id"];
}

$sql = "SELECT * FROM seller_manager WHERE manager_no='".$_SESSION['manager_no']."'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);

$product_id = array();
$p_sql = "SELECT * FROM product_class WHERE pid IN(SELECT `id` FROM `class` WHERE id IN(".$row['class_id'].") OR parent_id IN(".$row['class_id'].")) GROUP BY product_id";
$p_res = mysql_query($p_sql);
while ($p_row = mysql_fetch_array($p_res))
{
    $product_id[] = $p_row['product_id'];
}
?>
<div style="margin-top: 5%;">
    <div class="container panel">
        <h1 align="center">商品申請表</h1>
        <hr>
        <div class="row">
            <!-- edit form column -->
            <div class="col-md-12 personal-info">
                <form class="form-horizontal" role="form" method="post">

                    <div class="form-group">
                        <label class="col-lg-3 control-label">勾選代銷商品</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php
                        $product_id_str = implode(",", $product_id);
                        $product_sql = "SELECT * FROM product WHERE id IN(".$product_id_str.")";
                        $product_res = mysql_query($product_sql);
                        while($product_row = mysql_fetch_array($product_res))
                        {
                            ?>
                            <input type="checkbox" name="add_product_id[]" value="<?php echo $product_row['id']; ?>">&nbsp;<?php echo "<a href='index.php?url=product_detail&id=".$product_row['id']."' target=\"_blank\">".$product_row['p_name']."</a>&nbsp;&nbsp;"; ?>
                            <?php
                        }
                        ?>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-8" style="bottom:5px;">
                            <input type="submit" id="btn" name="btn" class="btn btn-primary btn-block" value="提交申請">
                            <input type="button" class="btn btn-default btn-block" onclick="location.href='index.php?url=manager_center'" value="返回行銷經理專區">
                        </div>
                    </div>
                    <br><br>
                </form>
            </div>
        </div>
    </div>
    <hr>
</div>

<!-- 彈出視窗 -->
<div class="modal fade" id="manager_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4>行銷經理規範</h4>
                <div>
                    aaaaa
                </div>
            </div>
        </div><!-- /.彈出視窗內容 -->
    </div><!-- /.彈出視窗結束 -->
</div>
<?php
@$add_product_id = $_POST['add_product_id'];
if(isset($_POST['btn']))
{
    for($i=0; $i<count($add_product_id); $i++)
    {
        $sql = "INSERT INTO sales_agents SET manager_id='".$_SESSION['manager_no']."', p_id='".$add_product_id[$i]."', sales_qty='0'";
        mysql_query($sql);
    }
    ?>
    <script>
        alert('提交完成，請等待營運方通知');
        location.href='index.php?url=manager_center';
    </script>
    <?php
}
?>
<script>
    $(function ()
    {
        $("#aa-slider").hide();
        $("html,body").scrollTop(70);
    });
</script>
