<?php
date_default_timezone_set('Asia/Taipei'); //設定台北時區

define('hostname', '127.0.0.1');
define('username', 'vhost118066');
define('password', 'First6011750');
define('database', 'vhost118066');
define('charset', 'utf8');

function sql()
{
    $db_link = mysql_connect(hostname, username, password);
    if (!$db_link) die("連線失敗");
    mysql_query("SET NAMES utf8", $db_link);
    mysql_select_db(database);
}

function db_connection()
{
    $dsn = 'mysql:';
    $hostname = hostname;
    $username = username;
    $password = password;
    $database = database;
    $charset = charset;
    $dsn .= "host={$hostname};";
    $dsn .= "dbname={$database};";
    $dsn .= "charset={$charset};";
    try {
        $db = new PDO($dsn, username, password);
        $result = $db;
    } catch (PDOException $exception) {
        $result = 'PDO connect to database fail: ' . "\n" . $exception->getMessage();
    }
    return $result;
}


function pdo_select_sql($SQL = '', $Parameter = false, $Debug = false)
{
    try {
        $dbh = db_connection();
        $sth = $dbh->prepare($SQL);
        if (gettype($Parameter) === 'array') {
            foreach ($Parameter as $key => $value) {
                $sth->bindParam(':'.$key,$Parameter[$key],PDO::PARAM_STR);
            }
        }
        $sth->execute();
        // $sth->setFetchMode(PDO::FETCH_ASSOC);
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $result = $e->getMessage();
    }
    return $result;
}

?>
