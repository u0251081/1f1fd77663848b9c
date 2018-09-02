<?php

define('BaseSecurity', 'this is 17mai');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$_SESSION['checkCode'] = BaseSecurity;

include 'admin/mysql.php';
sql();
@$status = $_GET['status'];

switch ($status)
{
    case 'set':
		test();
	break;
	
	case 'reg':
		reg();
	break;
}

function test()
{
	@$IMEI = $_GET['IMEI'];
	if(isset($IMEI))
	{
		$sql = "SELECT * FROM member WHERE imei='$IMEI'";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		if($row['id'] != "")
		{
			echo "[{'status':'pass'}]";
		}
		else
		{
			$sql2 = "SELECT * FROM fb WHERE imei='$IMEI'";
			$res2 = mysql_query($sql2);
			$row2 = mysql_fetch_array($res2);
			if($row2['id'] != "")
			{
				echo "[{'status':'pass'}]";
			}
			else
			{
				echo "[{'status':'fail'}]";
			}
		}
	}
}

//註冊FB帳號
function reg()
{
	@$IMEI = $_GET['IMEI'];
	@$FBID = $_GET['FBID'];
	@$email = $_GET['email'];
	@$name = $_GET['name'];
	$sql = "SELECT * FROM fb WHERE imei = '$IMEI'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	if($row['id'] != "")
	{
		echo "[{'status':'pass'}]";
	}
	else
	{
		$sql = "INSERT INTO fb SET fb_email='$email', fb_name='$name', fb_id='$FBID', imei='$IMEI', `status`='1', `identity`='member', registration_time='".date('Y-m-d H:i:s')."'";
		mysql_query($sql);
		echo "[{'status':'pass'}]";
	}
}