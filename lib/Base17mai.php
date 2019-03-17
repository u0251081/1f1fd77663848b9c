<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2018/8/12
 * Time: 下午 10:28
 */

namespace Base17Mai;

use PDO, PDOException, XTool\XAPDO;

require_once 'mysql.php';

class Base17mai
{
    protected static $DB = false;
    protected $RootDir = '';
    protected $XA = false;
    const DO_SELECT = 0;
    const DO_INSERT_NORMAL = 1;
    const DO_INSERT_WITHID = 2;
    const DO_UPDATE = 4;
    const DO_DELETE = 8;
    const PDO_PARSE_STR = PDO::PARAM_STR;
    const PDO_PARSE_INT = PDO::PARAM_INT;

    public function __construct()
    {
        $dsn = 'mysql:';
        $dsn .= 'host=' . dbhost . ';';
        $dsn .= 'dbname=' . dbname . ';';
        $dsn .= 'charset=' . charset . ';';
        $dsn .= 'port=3306;';
        $this->RootDir = dirname(__FILE__) . '/../';
        try {
            $connection = new PDO($dsn, dbuser, dbpasswd/*, [PDO::ATTR_PERSISTENT => true]*/);
            $this::$DB = $connection;
        } catch (PDOException $e) {
            print 'PDO connect to database fail:' . "\n" . $e->getMessage();
            exit();
        }

        $DBConfig = array(
            'driver' => 'mysql',
            'host' => dbhost,
            'port' => '3306',
            'username' => dbuser,
            'password' => dbpasswd,
            'database' => dbname,
            'character' => charset
        );

        $this->XA = new XAPDO($DBConfig);

    }

    public function testXA()
    {
        $result = $this->XA
            ->select('*')
            ->table('product')
            ->execute('select');
        return $result;
    }

    public function PAE($result = array()) // Print and Exit
    {
        if (isset($result['javascript'])) $this->switchSM($result['javascript']);
        print json_encode($result);
        exit();
    }

    protected function GetInformationFromTable($Columns = false, $Condition = false, $operator = 'and', $Table = false)
    {
        if (!is_string($Table)) return false;
        $columns = $Columns === false ? '*' : '';
        $columns = is_string($Columns) ? $Columns : $columns;
        $columns = $this->Check1DArray($Columns) ? implode(', ', $Columns) : $columns;
        $condition = $Condition === false ? 'true' : '';
        $condition = is_string($Condition) ? $Condition : $condition;
        $Para = array();
        if ($this->Check1DArray($Condition)) {
            $conditions = [];
            foreach ($Condition as $key => $value) {
                $Para[$key] = $value;
                $conditions[] = $key . ' = :' . $key;
            }
            $condition = count($conditions) > 0 ? implode(' ' . $operator . ' ', $conditions) : $condition;
        }
        $SQL = 'select ' . $columns . ' from ' . $Table . ' where ' . $condition . ';';
        $rst = $this->PDOOperator($SQL, $Para);
        return $rst;
    }

    private function switchSM(&$inputString)
    {
        $SM = '';
        if (isset($_COOKIE['imei'])) $SM = 'window.javatojs.showInfoFromJs';
        if (!isset($_COOKIE['imei'])) $SM = 'alert';
        $SM .= '$2';
        $result = preg_replace('/(alert|window\.javatojs\.showInfoFromJs)(\(.*\))/', $SM, $inputString);
        $inputString = $result;
    }

    public function ajaxCheckAJAXValid()
    {
        $this->PAE(array('javascript' => 'console.log("ajax is word");'));
    }

    public function SMR($str = '', $url = '') // Show Message and Redirect
    {
        $javascript = '';
        if ($_SESSION['device'] === 'mobile') $javascript .= "window.javatojs.showInfoFromJs('{$str}');";
        if ($_SESSION['device'] === 'desktop') $javascript .= "alert('{$str}');";
        if (!empty($url)) $javascript .= "location.href='{$url}';";
        return $javascript;
    }

    protected function PDOOperator($SQL = '', $Parameter = Array(), $Mode = Base17mai::DO_SELECT, $Debug = false)
    {
        $dbh = $this::$DB;
        $message = Array();
        try {
            # if start a transaction
            if ($Mode !== $this::DO_SELECT) $dbh->beginTransaction();
            // prepare SQL
            $sth = $dbh->prepare($SQL);
            $message['SQL'] = $SQL;

            // prepare Parameter
            if (is_array($Parameter)) {
                foreach ($Parameter as $k => $v) {
                    // 不可直接用 $v 因為它的值會變
                    // alternative: $sth->bindValue(':' . $k, $v, PDO::PARAM_STR);
                    if (isset($v['PARAM_TYPE']))
                        $sth->bindParam(':' . $k, $v['VALUE'], $v['PARAM_TYPE']);
                    else
                        $sth->bindParam(':' . $k, $Parameter[$k], PDO::PARAM_STR);
                    $message['Parameter'][$k] = $Parameter[$k];
                }
            }

            // set output for select
            // if ($Mode === $this::DO_SELECT) $sth->setFetchMode(PDO::FETCH_ASSOC);
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            $sth->execute();
            $effect_row = $sth->rowCount();
            $last_id = $dbh->lastInsertId();
            $result_arr = $sth->fetchAll();
            $message['effect'] = $effect_row;
            $message['lastID'] = $last_id;
            $message['result'] = $result_arr;

            # it is must to be commit
            if ($Mode !== $this::DO_SELECT) $dbh->commit();

            // prepare for result
            switch ($Mode) {
                default:
                case $this::DO_SELECT:
                    $result = $result_arr;
                    break;
                case $this::DO_INSERT_NORMAL:
                case $this::DO_UPDATE:
                case $this::DO_DELETE:
                    if ($effect_row > 0) $result = true;
                    else $result = false;
                    break;
                case $this::DO_INSERT_WITHID:
                    if ($effect_row > 0) $result = $last_id;
                    else $result = false;
                    break;
            }
        } catch (PDOException $exception) {
            $message['exception'] = $exception->getMessage();
            $result = false;
        }
        if ($Debug) print json_encode($message);
        return $result;
    }

    protected function GenerateSQLColumn($GLUE = ', ', $Parameter = [], $isCondition = false)
    {
        if (is_array($Parameter)) {
            $rst = [];
            foreach ($Parameter as $key => $value) {
                if (isset($valeu['PARAM_TYPE']) && $value['VALUE'] === null && $isCondition) $rst[] = $key . ' is :' . $key;
                else $rst[] = $key . ' = :' . $key;
            }
            $result = implode($GLUE, $rst);
            return $result;
        } else {
            return false;
        }
    }

    protected function checkExistsDataInTable($Parameter = Array(), $Table = '')
    {
        if (is_array($Parameter) && !empty($Parameter) && strlen($Table) > 0) {
            $condition = array();
            foreach ($Parameter as $key => $value) {
                if (isset($value['PARAM_TYPE']) && $value['VALUE'] === null) $condition[] = $key . ' is :' . $key;
                else $condition[] = $key . ' = :' . $key;
            }
            $conditionSQL = implode(' and ', $condition);
            $SQL = "select count(*) as chk from {$Table} where {$conditionSQL};";
            $resultArray = $this->PDOOperator($SQL, $Parameter);
            if (isset($resultArray[0]['chk']) && $resultArray[0]['chk'] > 0) $result = true;
            else $result = false;
            return $result;
        } else {
            return false;
        }
    }

    protected function generateFileName($path = '', $prefix = '')
    {
        do {
            $result = $this->generateRandomString();
        } while (file_exists($path . '/' . $prefix . $result));
        return $prefix . $result;
    }

    protected function generateRandomString($source = '', $length = 10, $special = false)
    {
        if (gettype($source) === 'string') {
            if ($source === '') $source = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            if ($special === true) $source .= '!@#$%^&*()_+-=/|?><.,"\'\\:;';
            $tableLength = strlen($source) - 1;
            $result = '';
            for ($i = 0; $i < $length; $i++) {
                $result .= $source[rand(0, $tableLength)];
            }
            return $result;
        } else {
            return false;
        }
    }

    protected function generateRandom($length = 10)
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $num = rand(0, 9);
            $result .= $num;
        }
        return $result;
    }

    protected function NewDateInterval($inputFormat = 'P0Y0M0W0DT0H0M0S', $outputFormat = 'Y-m-d H:i:s')
    {
        $now = new \DateTime('now');
        $interval = new \DateInterval($inputFormat);
        $result['from'] = $now->format($outputFormat);
        $result['to'] = $now->add($interval)->format($outputFormat);
        return $result;
    }

    private function TransactionExample()
    {

        /**
         * Connect to MySQL and instantiate the PDO object.
         * Set the error mode to throw exceptions and disable emulated prepared statements.
         */
        $pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '', array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ));

        //We are going to assume that the user with ID #1 has paid 10.50.
        $userId = 1;
        $paymentAmount = 10.50;

        //We start our transaction.
        $pdo->beginTransaction();

        //We will need to wrap our queries inside a TRY / CATCH block.
        //That way, we can rollback the transaction if a query fails and a PDO exception occurs.
        try {

            //Query 1: Attempt to insert the payment record into our database.
            $sql = "INSERT INTO payments (user_id, amount) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                    $userId,
                    $paymentAmount,
                )
            );

            //Query 2: Attempt to update the user's profile.
            $sql = "UPDATE users SET credit = credit + ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                    $paymentAmount,
                    $userId
                )
            );

            //We've got this far without an exception, so commit the changes.
            $pdo->commit();

        } //Our catch block will handle any exceptions that are thrown.
        catch (PDOException $e) {
            //An exception has occured, which means that one of our database queries
            //failed.
            //Print out the error message.
            echo $e->getMessage();
            //Rollback the transaction.
            $pdo->rollBack();
        }
    }

    public function constructAdminMenu()
    {
        $result = $this->getAdminMenuItem();
        $result = $this->adminMenuToHTML($result);

        return $result;
    }

    private function adminMenuToHTML($data = array())
    {
        if (is_array($data)) {
            $result = '';
            foreach ($data as $value) {
                $liClass = '';
                $subSet = '';
                if ($value['name'] === '搜尋') print $subSet;
                if (isset($value['child'])) {
                    $liClass = " class='dropdown'";
                    $subSet = '<ul>' . $this->adminMenuToHTML($value['child']) . '</ul>';
                }
                $link = "<a href=\"{$value['url']}\">{$value['name']}</a>";
                $result .= "<li{$liClass}>{$link}{$subSet}</li>";
            }
            return $result;
        }
    }

    private function getAdminMenuItem($ID = '1')
    {
        $SQL = 'select * from fn_set ';
        if (!empty($ID)) $condition = "where parent_id = '{$ID}' and id != '{$ID}'";
        else $condition = "where parent_id = id ";
        $order = 'order by `sort`;';
        $result = $this->PDOOperator($SQL . $condition . $order);
        foreach ($result as $key => $value) {
            $cond = ['parent_id' => $value['id']];
            $chk = $this->checkExistsDataInTable($cond, 'fn_set');
            $chkStr = ($chk) ? 'true' : 'false';
            $result[$key]['chk'] = $chkStr;
            if ($chk) {
                $result[$key]['child'] = $this->getAdminMenuItem($value['id']);
            }
        }
        return $result;
    }

    protected function MaskSecret($String = '')
    {
        $len = (int)(strlen($String) / 3);
        $replacement = '';
        $not = 3 - $len % 3;
        $start = $len + $not % 3;
        $start = $start > 6 ? 6 : $start;
        for ($i = 0; $i < (int)(strlen($String) - $len * 2); $i++) $replacement .= '*';
        $result = substr_replace($String, $replacement, (int)$start, -(int)$len);
        return $result;
    }

    protected function Check1DArray($input = array(), $AllowOBJ = false)
    {
        if (!is_array($input)) return false;
        foreach ($input as $item) {
            if (is_array($item)) return false;
            if ($AllowOBJ === false and is_object($item)) return false;
        }
        return true;
    }

    private function breadcrumb()
    {
        ?>
        <!--麵包導航開始-->
        <!--            <ul class="breadcrumbs">-->
        <!--                <li>-->
        <!--                    <a href="dashboard.html">-->
        <!--                        <i class="iconfa-home"></i>-->
        <!--                    </a><span class="separator"></span>-->
        <!--                </li>-->
        <!--                <li>-->
        <!--                    標題-->
        <!--                </li>-->
        <!--            </ul>-->
        <!--麵包導航結束-->
        <!--            <div class="pageheader">-->
        <!--                <form action="" method="post" class="searchbar">-->
        <!--                    <input type="text" name="keyword" placeholder="請輸入關鍵字" />-->
        <!--                </form>-->
        <!--                <div class="pageicon"><span class="iconfa-laptop"></span></div>-->
        <!--                <div class="pagetitle">-->
        <!--                    <h1>-->
        <!--                        標題-->
        <!--                    </h1>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--pageheader-->
        <?php

    }
}

?>