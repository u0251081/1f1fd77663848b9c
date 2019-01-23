<?php
/**
 * Created by PhpStorm.
 * User: andychen
 * Date: 8/20/18
 * Time: 3:09 AM
 */

namespace Base17Mai;

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

function take($index = false, $default = '', $type = 'GET')
{
    $target = false;
    switch ($type) {
        case 'GET':
        case 'get':
            $sample = $_GET;
            break;
        case 'POST':
        case 'post':
            $sample = $_POST;
            break;
        case 'SESSION':
        case 'session':
            $sample = $_SESSION;
            break;
        default:
            return false;
            break;
    }

    if (gettype($index) === 'string') {
        $target = (isset($sample[$index])) ? $sample[$index] : $default;
    }

    if (gettype($index) === 'array') {
        foreach ($index as $value) {
            if (gettype($value) === 'string')
                $sample = (isset($sample[$value])) ? $sample[$value] : '';
            else
                return false;
        }
        $target = $sample;
    }

    if ($index === false) $target = $sample;

    return $target;
}

function writeContent($FileName = '', $WriteContent = '', $type = 'write')
{
    switch ($type) {
        case 'write':
        default:
            $handler = fopen($FileName, 'w+');
            break;
        case 'append':
            $handler = fopen($FileName, 'a+');
            break;
    }

    $result = fwrite($handler, $WriteContent . "\n");
    fclose($handler);
    return $result;
    /*
    if ($result === false) print 'some error is occur' . "\n";
    else print "\n" . $result . ' byte has been written' . "\n";
    rewind($handler); // 重置指標
    print 'check file content ...' . "\n";
    $content = fread($handler, filesize(FileName));
    print $content . "\n";
    */
}

function displayToString($filename = '', $data = array(), $debug = false)
{
    $readPath = defined('TEMPLATEPATH') ? TEMPLATEPATH . '/' . $filename : './' . $filename;
    if ($debug) {
        print '$filename: ' . $filename . "\n";
        print '$data: ' . print_r($data, true) . "\n";
    }
    if (file_exists($readPath)) {
        ob_start();
        foreach ($data as $key => $value) {
            if (is_numeric($key)) continue;
            $value = is_string($value) ? '\'' . $value . '\'' : $value;
            $value = is_array($value) ? print_r($value, true) : $value;
            eval('$' . $key . ' = ' . $value . ';');
        }
        include $readPath;
        foreach ($data as $key => $value) {
            if (is_numeric($key)) continue;
            eval('unset($' . $key . ');');
        }
        $result = ob_get_contents();
        ob_end_clean();
        /*
         * <<<HTMLSTR
         *
         * HTMLSTR;
         */
    } else {
        $result = $filename . ' not found';
    }
    return $result;
}