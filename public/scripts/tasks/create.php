<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Core\Database;
use App\Controllers\TaskController;

// 1) Autoloaded classes via Composer
// 2) Grab PDO
$db = Database::getInstance();

// 3) Instantiate & call
$controller = new TaskController($db);
$controller->create();
