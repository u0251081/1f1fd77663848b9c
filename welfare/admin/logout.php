<?php
@session_start();
$identity = $_SESSION['identity'];
if (isset($identity)) $identity = $identity;
else $identity = NULL;

if($identity == 'admin')
{
    unset($_SESSION['id']);
    unset($_SESSION['identity']);
    //@session_destroy();
    ?>
    <script>
	   window.location.href="admin_login.php";
    </script>
    <?php
}
/*else if($identity == 'supplier' || $identity == 'store')
{
    @session_destroy();
    ?>
    <script>
	   window.location.href="index.php";
    </script>
    <?php
}*/
?>
