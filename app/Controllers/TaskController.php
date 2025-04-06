<?php
/**
 * TaskController - Handles all task-related operations
 * 
 * @author Kutlo Sepesi
 * @package App\Controllers
 */
namespace App\Controllers;

use App\Models\Task;
use App\Core\Session;

class TaskController
{
    /** @var Task $taskModel Instance of Task model */
    private $taskModel;
    
    /** @var Session $session Session handler instance */
    private $session;

    /**
     * Initialize controller with database connection
     * 
     * @param \PDO $db Database connection
     */
    public function __construct(\PDO $db)
    {
        $this->session = new Session();
        $this->session->start();
        
        // Verify authentication
        if (!$this->session->isLoggedIn()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            exit;
        }
        
        // Initialize task model with current user ID
        $this->taskModel = new Task($db, $this->session->get('user_id'));
    }

    /**
     * Standardized JSON response formatter
     * 
     * @param mixed $data Response payload
     * @param int $statusCode HTTP status code
     */
    private function jsonResponse($data, $statusCode = 200)
    {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }

    /**
     * Create a new task
     * 
     * @return JSON response with success/error message
     */
    public function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        if (empty($data['title']) || empty($data['description'])) {
            $this->jsonResponse(['error' => 'Title and Description are required'], 400);
            return;
        }

        $success = $this->taskModel->create(
            htmlspecialchars($data['title']),
            htmlspecialchars($data['description']),
            $data['due_date'] ?? null
        );

        $this->jsonResponse([
            'success' => $success,
            'message' => $success ? 'Task created' : 'Failed to create task'
        ], $success ? 200 : 500);
    }

    /**
     * Fetch all tasks for current user
     * 
     * @return JSON response with task list
     */
    public function fetch()
    {
        try {
            $tasks = $this->taskModel->fetchAllByUserId();
            
            // Format dates for display
            foreach ($tasks as &$task) {
                $task['due_date'] = $task['due_date'] 
                    ? date('M j, Y', strtotime($task['due_date'])) 
                    : 'No due date';
                $task['created_at'] = date('M j, Y', strtotime($task['created_at']));
            }
            
            $this->jsonResponse(['data' => $tasks]);
        } catch (\Exception $e) {
            error_log('Fetch tasks error: ' . $e->getMessage());
            $this->jsonResponse(['error' => 'Failed to fetch tasks'], 500);
        }
    }

    /**
     * Delete a task
     * 
     * @return JSON response with success/error message
     */
    public function delete()
    {
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (empty($data['task_id'])) {
            $this->jsonResponse([
                'success' => false, 
                'error' => 'Task ID required'
            ], 400);
            return;
        }
    
        try {
            $success = $this->taskModel->delete($data['task_id']);
            
            if ($success) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Task deleted successfully'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Task not found or not owned by user'
                ], 404);
            }
        } catch (\Exception $e) {
            error_log('Delete task error: ' . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'error' => 'Failed to delete task'
            ], 500);
        }
    }

    /**
     * Mark task as complete
     * 
     * @return JSON response with success/error message
     */
    public function markComplete()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['task_id'])) {
            $this->jsonResponse(['error' => 'Task ID required'], 400);
            return;
        }

        $success = $this->taskModel->updateStatus($data['task_id'], 'Completed');
        $this->jsonResponse([
            'success' => $success,
            'message' => $success ? 'Task marked complete' : 'Failed to update task'
        ]);
    }

    /**
     * Update existing task
     * 
     * @return JSON response with success status
     */
    public function update() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['task_id'])) {
            $this->jsonResponse(['error' => 'Task ID required'], 400);
            return;
        }
    
        $success = $this->taskModel->update(
            $data['task_id'],
            htmlspecialchars($data['title'] ?? ''),
            htmlspecialchars($data['description'] ?? ''),
            $data['due_date'] ?? null
        );
    
        $this->jsonResponse(['success' => $success]);
    }
}