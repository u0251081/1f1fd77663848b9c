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