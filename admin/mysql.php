<?php
date_default_timezone_set('Asia/Taipei'); //設定台北時區

function sql()
{
	$db_host="127.0.0.1";
	$db_username="vhost118066";
	$db_password="First6011750";
	$db_link=mysql_connect($db_host,$db_username,$db_password);
	if(!$db_link)die("連線失敗");
	mysql_query("SET NAMES utf8",$db_link);
	mysql_select_db("vhost118066");
}
?>
