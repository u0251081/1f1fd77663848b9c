<?php
//這裡是從手機跳到第二個activity後先載入一些資訊，供webview開啟網頁後使用
@$type = $_GET['type'];

switch($type)
{
    case 'set_login':
        set_login();
        break;
}
function set_login()
{
    @$imei = $_GET['imei'];
    if(isset($imei))
    {
        $sql = "SELECT * FROM member WHERE imei='$imei'";
        $res = mysql_query($sql);
        $row = mysql_fetch_array($res);

        if($row['id'] != "")
        {
            $sql2 = "SELECT manager_no FROM seller_manager WHERE member_id='".$row['member_no']."' AND manager_status='1'";
            $res2 = mysql_query($sql2);
            $row2 = mysql_fetch_array($res2);

            $_SESSION['manager_no'] = $row2['manager_no'];
            $_SESSION["imei"] = $imei;
            $_SESSION['member_no'] = $row['member_no'];
            $_SESSION['front_id'] = $row['id'];
            $_SESSION['front_identity'] = 'member';
            $_SESSION['device'] = 'mobile';
        }
        else
        {
            $sql2 = "SELECT * FROM fb WHERE imei='$imei'";
            $res2 = mysql_query($sql2);
            $row2 = mysql_fetch_array($res2);

            if($row2['id'] != "")
            {
                $sql3 = "SELECT manager_no FROM seller_manager WHERE member_id='".$row2['fb_id']."' AND manager_status='1'";
                $res3 = mysql_query($sql3);
                $row3 = mysql_fetch_array($res3);

                $_SESSION['manager_no'] = $row3['manager_no'];
                $_SESSION["imei"] = $imei;
                $_SESSION['front_id'] = $row2['id'];
                $_SESSION["fb_id"] = $row2['fb_id'];
                $_SESSION['front_identity'] = 'member';
                $_SESSION['device'] = 'mobile';
            }
        }
    }
    to_page('index.php');
}

function to_page($page_name)
{
    if($page_name)
    {
        ?>
        <script>
            location.href='<?php echo $page_name; ?>';
        </script>
        <?php
    }
}

?>