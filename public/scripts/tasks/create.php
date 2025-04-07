<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Core\Database;
use App\Controllers\TaskController;


$db = Database::getInstance();

$controller = new TaskController($db);
$controller->create();
