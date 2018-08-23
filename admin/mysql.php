<?php
date_default_timezone_set('Asia/Taipei'); //設定台北時區

function sql()
{
    $db_host = "127.0.0.1";
    $db_username = "vhost118066";
    $db_password = "First6011750";
    $db_link = mysql_connect($db_host, $db_username, $db_password);
    if (!$db_link) die("連線失敗");
    mysql_query("SET NAMES utf8", $db_link);
    mysql_select_db("vhost118066");
}

function get_sql_data($sql)
{
    if (strlen($sql) > 0) {
        $res = mysql_query($sql) or exit(mysql_error());
        $rtv = array();
        while ($row = mysql_fetch_array($res)) {
            $rtv[] = $row;
        }
        return $rtv;
    }
    return false;
}

function pdo_connect()
{
    $db_host = "127.0.0.1";
    $db_name = 'vhost118066';
    $db_username = "vhost118066";
    $db_password = "First6011750";
    $charset = 'UTF8';
    $dsn = '';
    $dsn .= 'mysql' . ':';
    $dsn .= 'host=' . $db_host . ';';
    $dsn .= 'dbname=' . $db_name . ';';
    $dsn .= 'charset=' . $charset . ';';
    try {
        $connection = new PDO($dsn, $db_username, $db_password);
        return $connection;
    } catch (PDOException $e) {
        print 'PDO connect to database fail:' . "\n" . $e->getMessage();
        exit();
    }
}

function pdo_select_sql($sql, $param = false)
{
    $dbh = pdo_connect();
    try {
        // if $dbh->beginTransaction();
        $sth = $dbh->prepare($sql);
        if (is_array($param)) {
            foreach ($param as $k => $v) {
                $sth->bindParam(':' . $k, $param[$k], PDO::PARAM_STR);
                // print 'key: '.$k.', value: '.$v."\n";
            }
        }
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $sth->execute();
        // then $dbh->commit();
        return $sth->fetchAll();
    } catch (PDOException $exception) {
        print $exception->getMessage();
        return false;
    }
}

function pdo_insert_and_update_sql($SQL, $Parameter = false, $model = 1)
{
    $dbh = pdo_connect();
    try {
        // if $dbh->beginTransaction();
        $sth = $dbh->prepare($SQL);
        if (is_array($Parameter)) {
            foreach ($Parameter as $k => $v) {
                $sth->bindParam(':' . $k, $Parameter[$k], PDO::PARAM_STR);
                // print 'key:'.$k.', value:'.$v."\n";
            }
        }
        $sth->execute();

        switch ($model) {
            case 1:
            default:
                if ($sth->rowCount() > 0 ) return true;
                else return false;
                break;
            case 2:
                if ($sth->rowCount() > 0 ) $status = true;
                else $status = false;
                $last_id = $dbh->lastInsertId();
                $rtValue['status'] = $status;
                $rtValue['lastId'] = $last_id;
                return $rtValue;
                break;
        }
        // then $dbh->commit();

    } catch (PDOException $exception) {
        print $exception->getMessage();
        return false;
    }
}

?>
