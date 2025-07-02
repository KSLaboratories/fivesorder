<?php

session_start();

// ! check if session empty
if($_SESSION['acc_pss'] == ''){
    header('Location: /login');
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__. '/func.php';

// load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();