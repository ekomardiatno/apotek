<?php

ob_start();

date_default_timezone_set("Asia/Jakarta");

session_start();

require_once __DIR__.'/vendor/autoload.php';
$dotenv = new Dotenv\Dotenv('./');
$dotenv->load();

require_once __DIR__.'/private/App/init.php';
$app = new App;
$app->route();

ob_end_flush();
