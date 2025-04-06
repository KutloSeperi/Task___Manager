<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

header('Content-Type: application/json');
session_start();

use App\Controllers\TaskController;

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        throw new Exception('Invalid request method');
    }

    $taskId = (int)$_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);
    
    $taskController = new TaskController();
    $response['success'] = $taskController->update($taskId, $data);
    $response['message'] = $response['success'] ? 'Task updated' : $taskController->getError();
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);