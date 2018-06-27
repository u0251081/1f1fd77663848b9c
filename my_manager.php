<style>
    #pay_check_div
    {
        margin-top: 13%;
    }

    @media screen and (max-width: 1280px)
    {
        #pay_check_div
        {
            margin-bottom: 30%;
        }

        #order_tb
        {
            font-size: 3.8vw;
        }
    }
</style>
<div class="container" id="pay_check_div">
    <div class="row">
        <table class="table table-bordered table-responsive table-condensed" id="order_tb">
            <thead>
            <tr>
                <th colspan="5"><h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">我的行銷經理</h3></th>
            </tr>
            <tr style="background: #DDDDDD;">
                <th>ID</th>
                <th>姓名</th>
                <th>訂閱時間</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(isset($member_id))
            {
                $sql = "SELECT * FROM lower_limit WHERE member_id='$member_id'";
                //echo $sql;
                $res = mysql_query($sql);
                while ($row = mysql_fetch_array($res))
                {
                    ?>
                    <tr id="tr<?php echo $row['id']; ?>">
                        <td><?php echo $row['manager_id']; ?></td>
                        <td>
                            <?php
                            if(strlen($row['mg_member_id']) > 10)
                            {
                                $manager_name_sql = "SELECT fb_name FROM fb WHERE fb_id='".$row['mg_member_id']."'";
                                $manager_name_res = mysql_query($manager_name_sql);
                                $manager_name_row = mysql_fetch_array($manager_name_res);
                                echo $manager_name_row['fb_name'];
                            }
                            else
                            {
                                $manager_name_sql = "SELECT m_name FROM member WHERE member_no='".$row['mg_member_id']."'";
                                $manager_name_res = mysql_query($manager_name_sql);
                                $manager_name_row = mysql_fetch_array($manager_name_res);
                                echo $manager_name_row['m_name'];
                            }
                            ?>
                        </td>
                        <td><?php echo $row['add_time']; ?></td>
                        <td><input type="button" class="btn btn-primary" value="取消訂閱" name="remove_mg" r_id="<?php echo $row['id']; ?>"></td>
                    </tr>
                    <?php

                }
            }
            ?>
            <tr align="right">
                <td colspan="5">
                    <div class="container">
                        <div class="row">
                            <input type="button" class="btn btn-default" value="返回" onclick="go_back();">&nbsp;&nbsp;
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(function ()
    {
        $("#aa-slider").hide();
        $("html,body").scrollTop(750);
    });

    function go_back()
    {
        location.href='index.php?url=member_center';
    }

    $("input[name='remove_mg']").click(function ()
    {
        var r_id = $(this).attr('r_id');
        if(confirm('您確定要取消訂閱該行銷經理嗎?'))
        {
            $.ajax
            ({
                url:"ajax.php", //接收頁
                type:"POST", //POST傳輸
                data:{type:"rem_mg", r_id:r_id}, // key/value
                dataType:"text", //回傳形態
                success:function(i) //成功就....
                {
                    if(i == 1)
                    {
                        $("#tr"+r_id).remove();
                    }
                    else
                    {
                        alert('意外的錯誤，請重新操作');
                    }
                },
                error:function()//失敗就...
                {
                }
            });
        }
        else
        {
            alert('再考慮一下吧');
        }
    });
</script>