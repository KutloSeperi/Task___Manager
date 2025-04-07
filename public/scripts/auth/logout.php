<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Core\Session;

Session::start();
Session::destroy();


if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    die();
}


header('Location: index.php');
exit;