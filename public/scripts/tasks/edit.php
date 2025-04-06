<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Core\Database;
use App\Controllers\TaskController;

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $controller = new TaskController($db);
    $controller->update();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}