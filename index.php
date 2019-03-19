<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const BASE_DIR = __DIR__;
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/App/App.php';

App::init()->bootstrap();