<?php
session_start();

define('BASE_URL', 'http://localhost/Projet_TDW/');
define('ASSETS_URL', BASE_URL . 'public/assets/');

require_once '../app/core/Model.php';
require_once '../app/core/View.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Router.php';
require_once '../app/config/Database.php';

$router = new Router();
?>