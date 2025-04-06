<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\Database;
use App\Core\Session;
use App\Models\User;
use App\Controllers\AuthController;

// Initialize
Database::getInstance();
$session = new Session();
$timestamp = time();
$testUsername = "testuser_$timestamp";
$testPassword = 'secure_password_123';

// Cleanup
$db = Database::getInstance()->getConnection();
$db->exec("DELETE FROM users WHERE username LIKE 'testuser_%'");

echo "<h1>AuthController Test</h1>";

// Test 1: Registration
try {
    $user = new User();
    $user->setUsername($testUsername);
    $user->setEmail("$testUsername@example.com");
    $user->setPassword($testPassword);

    $auth = new AuthController();
    
    if ($auth->register($user)) {
        echo "✅ Registration successful<br>";
    } else {
        die("❌ Registration failed: " . $auth->getError());
    }
} catch(Exception $e) {
    die("🚨 Registration error: " . $e->getMessage());
}

// Test 2: Duplicate Registration
try {
    $duplicateUser = new User();
    $duplicateUser->setUsername($testUsername);
    $duplicateUser->setEmail("duplicate@example.com");
    $duplicateUser->setPassword('wrong_password');
    
    if ($auth->register($duplicateUser)) {
        die("❌ Duplicate registration allowed");
    } else {
        echo "✅ Duplicate prevention working: " . $auth->getError() . "<br>";
    }
} catch(Exception $e) {
    echo "✅ Duplicate prevention working (exception)<br>";
}

// Test 3: Login with valid credentials
try {
    if ($auth->login($testUsername, $testPassword)) {
        echo "✅ Login successful<br>";
        echo "Session User ID: " . $session->get('user_id') . "<br>";
    } else {
        die("❌ Valid login failed: " . $auth->getError());
    }
} catch(Exception $e) {
    die("🚨 Login error: " . $e->getMessage());
}

// Test 4: Login with invalid password
try {
    if ($auth->login($testUsername, 'wrong_password')) {
        die("❌ Invalid login allowed");
    } else {
        echo "✅ Invalid password rejected: " . $auth->getError() . "<br>";
    }
} catch(Exception $e) {
    echo "✅ Invalid password rejected (exception)<br>";
}

// Cleanup
$db->exec("DELETE FROM users WHERE username = '$testUsername'");
$session->destroy();
echo "🧹 Test data cleaned";