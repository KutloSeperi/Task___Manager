<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\Database;
use App\Core\Session;
use App\Controllers\AuthController;
use App\Controllers\TaskController;
use App\Models\User;

// Initialize
Database::getInstance();
$db = Database::getInstance()->getConnection();
$db->beginTransaction();

// Create and login test user
$user = new User();
$user->setUsername('task_controller_test');
$user->setEmail('task_controller@example.com');
$user->setPassword('password123');
$user->save();

$auth = new AuthController();
$auth->login('task_controller_test', 'password123');

echo "<h1>Task Controller Test</h1>";

try {
    $taskController = new TaskController();

    // Test 1: Create Task
    $taskData = [
        'title' => 'Controller Test Task',
        'description' => 'Test description',
        'due_date' => '2024-12-31',
        'status' => 'Pending'
    ];

    if ($taskController->create($taskData)) {
        echo "✅ Task creation successful<br>";
    } else {
        die("❌ Task creation failed: " . $taskController->getError());
    }

    // Test 2: Get All Tasks
    $tasks = $taskController->getAll();
    if (!empty($tasks)) {
        $task = $tasks[0];
        echo "✅ Task retrieval successful (ID: {$task->getId()})<br>";
    } else {
        die("❌ Task retrieval failed");
    }

    // Test 3: Update Task
    $updateData = [
        'title' => 'Updated Title',
        'status' => 'In Progress'
    ];
    
    if ($taskController->update($task->getId(), $updateData)) {
        echo "✅ Task update successful<br>";
    } else {
        die("❌ Task update failed: " . $taskController->getError());
    }

    // Test 4: Delete Task
    if ($taskController->delete($task->getId())) {
        echo "✅ Task deletion successful<br>";
    } else {
        die("❌ Task deletion failed: " . $taskController->getError());
    }

} catch (Exception $e) {
    die("🚨 Critical error: " . $e->getMessage());
} finally {
    $db->rollBack();
    echo "🧹 Test data rolled back";
}