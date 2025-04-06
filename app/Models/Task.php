<?php
/**
 * Task - Handles all database operations for tasks
 * 
 * Provides CRUD operations with user-specific access control
 * and proper error handling.
 * 
 * @author Kutlo Sepesi
 * @package App\Models
 */
namespace App\Models;

class Task
{
    /** @var \PDO $db Database connection */
    private $db;
    
    /** @var int $userId Current user ID for ownership checks */
    private $userId;

    /**
     * Initialize Task model with database connection
     * 
     * @param \PDO $db Database connection
     * @param int $userId Authenticated user ID
     */
    public function __construct(\PDO $db, $userId)
    {
        $this->db = $db;
        $this->userId = $userId;
    }

    /**
     * Get all tasks for the current user
     * 
     * @return array List of tasks or empty array on failure
     */
    public function fetchAllByUserId()
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, title, description, due_date, status, created_at 
                 FROM tasks 
                 WHERE user_id = ?
                 ORDER BY created_at DESC"
            );
            $stmt->execute([$this->userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Fetch tasks error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new task
     * 
     * @param string $title Task title
     * @param string $description Task description
     * @param string|null $dueDate Optional due date (YYYY-MM-DD)
     * @return bool True on success
     */
    public function create($title, $description, $dueDate = null)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tasks (user_id, title, description, due_date, status)
                 VALUES (?, ?, ?, ?, 'Pending')"
            );
            return $stmt->execute([
                $this->userId,
                htmlspecialchars($title),
                htmlspecialchars($description),
                $dueDate
            ]);
        } catch (\PDOException $e) {
            error_log("Task create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a task with ownership verification
     * 
     * @param int $taskId Task ID to delete
     * @return bool True if task was deleted
     */
    public function delete($taskId)
    {
        try {
            // Verify ownership in single query (more efficient)
            $stmt = $this->db->prepare(
                "DELETE FROM tasks 
                 WHERE id = ? AND user_id = ?"
            );
            $stmt->execute([$taskId, $this->userId]);
            
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Task delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update task status
     * 
     * @param int $taskId Task ID
     * @param string $status New status (Pending/In Progress/Completed)
     * @return bool True on success
     */
    public function updateStatus($taskId, $status)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE tasks 
                 SET status = ? 
                 WHERE id = ? AND user_id = ?"
            );
            return $stmt->execute([$status, $taskId, $this->userId]);
        } catch (\PDOException $e) {
            error_log("Task status update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update task details
     * 
     * @param int $taskId Task ID
     * @param string $title New title
     * @param string $description New description
     * @param string|null $dueDate New due date (YYYY-MM-DD)
     * @return bool True on success
     */
    public function update($taskId, $title, $description, $dueDate = null) 
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE tasks 
                 SET title = ?, description = ?, due_date = ?
                 WHERE id = ? AND user_id = ?"
            );
            return $stmt->execute([
                htmlspecialchars($title),
                htmlspecialchars($description),
                $dueDate,
                $taskId,
                $this->userId
            ]);
        } catch (\PDOException $e) {
            error_log("Task update error: " . $e->getMessage());
            return false;
        }
    }
}