<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2018/8/12
 * Time: 下午 08:49
 */


define('dbhost','127.0.0.1');
define('dbuser','vhost118066');
define('dbpasswd','First6011750');
define('dbname','vhost118066');
define('charset','UTF8');

/*
 * $db_host = "127.0.0.1";
 * $db_username = "vhost118066";
 * $db_password = "First6011750";
 * $db_link = mysql_connect($db_host, $db_username, $db_password);
 * if (!$db_link) die("連線失敗");
 * mysql_query("SET NAMES utf8", $db_link);
 * mysql_select_db("vhost118066");
 *
 * require 這個語法通常使用在程式檔案的一開頭，載入程式時，會先讀取require引入的檔案，使其變成程式的一部分。
 *         適合用來引入靜態的內容，例如版權宣告。
 *         如果 require 進來的檔案發生錯誤的話，會顯示錯誤，立刻終止程式，不再往下執行。
 *
 * include 這個函式的功能跟require一樣，只不過通常使用在程式中的流程敘述中，例如if…else…、while、for等敘述中。
 *         適合用來引入動態的程式碼。
 *         如果 include 進來的檔案發生錯誤的話，會顯示警告，不會立刻停止。
 *
 * require_once、include_once 使用方法跟require、include一樣，差別在於在引入檔案前，會先檢查檔案是否已經被引入過了，
 * 若有，就不會再重複引入。
 */
