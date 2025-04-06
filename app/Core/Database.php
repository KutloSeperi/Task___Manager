<?php
/**
 * Database - Singleton database connection handler
 * 
 * Manages PDO database connections using the singleton pattern
 * to ensure only one connection exists per request.
 * 
 * @author Kutlo Sepesi
 * @package App\Core
 */
namespace App\Core;

class Database 
{
    /** @var Database|null $instance Singleton instance */
    private static $instance = null;
    
    /** @var \PDO $connection Active PDO connection */
    private $connection;
    
    /**
     * Private constructor to prevent direct instantiation
     * 
     * @throws \PDOException On connection failure
     */
    private function __construct() 
    {
        $host = 'localhost';
        $dbname = 'task_manager';
        $username = 'root'; // default for XAMPP
        $password = '';     // default for XAMPP
        
        try {
            $this->connection = new \PDO(
                "mysql:host=$host;dbname=$dbname", 
                $username, 
                $password
            );
            $this->connection->setAttribute(
                \PDO::ATTR_ERRMODE, 
                \PDO::ERRMODE_EXCEPTION
            );
        } catch (\PDOException $e) {
            throw new \PDOException(
                "Database connection failed: " . $e->getMessage()
            );
        }
    }
    
    /**
     * Get singleton database instance
     * 
     * @return \PDO Active PDO connection
     */
    public static function getInstance() 
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}