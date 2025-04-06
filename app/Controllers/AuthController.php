<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Session;

/**
 * AuthController - Handles user authentication and registration
 * 
 * @author Kutlo Sepesi
 * @package App\AuthController
 */
class AuthController {
    /** @var User $userModel Instance of User model */
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Register a new user account
     * 
     * @param string $username 
     * @param string $email 
     * @param string $password 
     * @param string $confirmPassword 
     * @return array ['success' => bool, 'message' => string]
     */
    public function register($username, $email, $password, $confirmPassword) {
        // Input validation
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            return $this->buildResponse(false, 'All fields are required');
        }
        
        if ($password !== $confirmPassword) {
            return $this->buildResponse(false, 'Passwords do not match');
        }
        
        if (strlen($password) < 6) {
            return $this->buildResponse(false, 'Password must be at least 6 characters');
        }
        
        // Check for existing user
        if ($this->userModel->findByUsername($username)) {
            return $this->buildResponse(false, 'Username already taken');
        }
        
        // Attempt user creation
        if ($this->userModel->create($username, $email, $password)) {
            return $this->buildResponse(true, 'Registration successful');
        }
        
        return $this->buildResponse(false, 'Registration failed');
    }
    
    /**
     * Authenticate and log in a user
     * 
     * @param string $username
     * @param string $password
     * @return array ['success' => bool, 'message' => string]
     */
    public function login($username, $password) {
        $user = $this->userModel->verifyCredentials($username, $password);
        
        if ($user) {
            Session::set('user_id', $user['id']);
            Session::set('username', $user['username']);
            return $this->buildResponse(true, 'Login successful');
        }
        
        return $this->buildResponse(false, 'Invalid username or password');
    }
    
    /**
     * Terminate user session
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    public function logout() {
        Session::destroy();
        return $this->buildResponse(true, 'Logged out successfully');
    }
    
    /**
     * Standardize API response format
     * 
     * @param bool $success
     * @param string $message
     * @return array
     */
    private function buildResponse($success, $message) {
        return [
            'success' => $success,
            'message' => $message
        ];
    }
}