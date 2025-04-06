<?php
/**
 * Session - Handles all session management operations
 * 
 * Provides secure session handling with cookie management
 * and user authentication state tracking.
 * 
 * @author Kutlo Sepesi
 * @package App\Core
 */
namespace App\Core;

class Session 
{
    /**
     * Initialize the session if not already started
     * 
     * @return void
     */
    public static function start() 
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Set a session value
     * 
     * @param string $key Session key
     * @param mixed $value Value to store
     * @return void
     */
    public static function set($key, $value) 
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get a session value
     * 
     * @param string $key Session key
     * @return mixed|null Returns value or null if not found
     */
    public static function get($key) 
    {
        self::start();
        return $_SESSION[$key] ?? null;
    }
    
    /**
     * Destroy the current session
     * 
     * Clears all session data and removes session cookie
     * 
     * @return void
     */
    public static function destroy()
    {
        // Clear session data
        $_SESSION = [];

        // Invalidate session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy server-side session
        session_destroy();
    }
    
    /**
     * Check if user is authenticated
     * 
     * @return bool True if user_id exists in session
     */
    public static function isLoggedIn() 
    {
        return self::get('user_id') !== null;
    }
}