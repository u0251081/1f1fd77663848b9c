<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2018/8/13
 * Time: 上午 02:54
 */

define('BaseSecurity', 'this is 17mai');
define('ClassPath', 'lib');
define('NS', 'Base17Mai');

require_once 'vendor/autoload.php';

function GET($index = false, $default = array())
{
    if (gettype($index) === 'string') {
        return (isset($_GET[$index])) ? $_GET[$index] : $default;
    }
    if ($index === false) {
        return $_GET;
    }
    return false;
}

function POST($index = false, $default = array())
{
    if (gettype($index) === 'string') {
        return (isset($_POST[$index])) ? $_POST[$index] : $default;
    }
    if ($index === false) {
        return $_POST;
    }
    return false;
}

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (isset($_SESSION['checkCode']) && $_SESSION['checkCode'] === BaseSecurity) {
    $debugForAJAXOP = false;
    $class = POST('G', '');
    $method = POST('U', '');
    if (strlen($class) < 1 || strlen($method) < 1) exit();
    $className = NS . '\\' . $class;
    $methodName = 'ajax' . $method;
    if (dynamicClassMethod($className, $methodName, GET(), POST())) exit();
    else print json_encode(array('javascript' => 'showMessage(\'在 ' . $class . ' 裡沒有 ' . $method . ' 方法!!!\');'));
}

function dynamicClassMethod($class, $method, $GET = array(), $POST = array())
{
    // check class exists
    if (class_exists($class)) {
        // instance object
        $object = new $class();
        // check method is exists in class
        if (method_exists($object, $method)) {
            $object->$method($GET, $POST);
            return true;
        }
    }
    return false;
}

function generateFileName()
{
    do {
        $result = generateRandomString();
    } while (file_exists($result));
    return $result;
}

function generateRandomString($source = '', $length = 10, $special = false)
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

?>