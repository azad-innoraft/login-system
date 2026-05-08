<?php

define("BASE_PATH", dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();


require_once BASE_PATH . '/app/core/Database.php';
require_once BASE_PATH . '/app/model/User.php';
require_once BASE_PATH . '/app/controllers/MailController.php';
require_once BASE_PATH . '/app/controllers/AuthController.php';
require_once BASE_PATH . '/app/controllers/FormController.php';
require_once BASE_PATH . '/app/core/Router.php';
