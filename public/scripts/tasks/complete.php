<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Core\Database;
use App\Controllers\TaskController;

header('Content-Type: application/json');


$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (empty($data['task_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing task_id parameter']);
    exit;
}

try {
    $db = Database::getInstance();
    $controller = new TaskController($db);
    $controller->markComplete();
} catch (Exception $e) {
    http_response_code(500);
   
}