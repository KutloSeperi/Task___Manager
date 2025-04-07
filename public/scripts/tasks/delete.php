<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Controllers\TaskController;
use App\Core\Database;
use App\Core\Session;

$input = json_decode(file_get_contents('php://input'), true);

$session = new Session();
$session->start();

if (!$session->get('user_id')) {
    header('HTTP/1.1 401 Unauthorized');
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$taskId = $input['task_id'] ?? null; 

if (!$taskId) {
    header('Content-Type: application/json');
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['success' => false, 'message' => 'Task ID required']));
}

try {
    $db = Database::getInstance();
    $taskController = new TaskController($db);
    $success = $taskController->delete($taskId);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Task deleted' : 'Delete failed'
    ]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Server error']);
}