<?php
/**
 * User - Handles user authentication and database operations
 * 
 * Provides user registration, login verification, and data retrieval
 * with secure password handling and error logging.
 * 
 * @author Kutlo Sepesi
 * @package App\Models
 */
namespace App\Models;

use App\Core\Database;

class User
{
    /** @var Database $db Database connection instance */
    private $db;
    
    /**
     * Initialize User model with database connection
     * Uses singleton pattern to get database instance
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new user account with secure password storage
     * 
     * @param string $username User's unique identifier
     * @param string $email User's email address
     * @param string $password Clear text password for hashing
     * @return bool True if account creation succeeded
     */
    public function create($username, $email, $password)
    {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare(
                "INSERT INTO users (username, email, password_hash) 
                 VALUES (:username, :email, :password_hash)"
            );
            
            // Sanitize inputs and bind parameters
            $stmt->bindValue(':username', htmlspecialchars($username));
            $stmt->bindValue(':email', htmlspecialchars($email));
            $stmt->bindParam(':password_hash', $hashedPassword);
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("User creation error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find user by username
     * 
     * @param string $username Username to search for
     * @return array|false User record as associative array or false if not found
     */
    public function findByUsername($username)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM users 
                 WHERE username = :username"
            );
            $stmt->bindValue(':username', htmlspecialchars($username));
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("User lookup error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verify user credentials against stored hash
     * 
     * @param string $username Supplied username
     * @param string $password Supplied password
     * @return array|false User data if valid, false otherwise
     */
    public function verifyCredentials($username, $password)
    {
        try {
            $user = $this->findByUsername($username);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                return $user;
            }
            
            return false;
        } catch (\PDOException $e) {
            error_log("Credential verification error: " . $e->getMessage());
            return false;
        }
    }
}