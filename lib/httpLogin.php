<?php
/**
 * Created by PhpStorm.
 * User: andychen
 * Date: 8/24/18
 * Time: 6:04 PM
 */

function checkForLogin() {
    if (!isset($_SERVER['PHP_AUTH_USER'])) return false;
    if (!isset($_SERVER['PHP_AUTH_PW'])) return false;
    if ($_SERVER['PHP_AUTH_USER'] === 'username' && $_SERVER['PHP_AUTH_PW'] === 'password') return true;
    return false;
}
if (isset($_GET['logout'])) {
    unset($_SERVER['PHP_AUTH_USER']);
    unset($_SERVER['PHP_AUTH_PW']);
}
if (!checkForLogin()) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text to send if user hits Cancel button';
    exit;
} else {
    echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
    echo "<p>You entered {$_SERVER['PHP_AUTH_PW']} as your password.</p>";
}

unset($_SERVER['PHP_AUTH_USER']);
unset($_SERVER['PHP_AUTH_PW']);