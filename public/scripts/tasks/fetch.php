<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Core\Database;
use App\Controllers\TaskController;

// Initialize database connection
$db = Database::getInstance();

// Create TaskController instance
$controller = new TaskController($db);

// Call the fetch method (not fetchAllTasks - we renamed it to just fetch())
$controller->fetch();