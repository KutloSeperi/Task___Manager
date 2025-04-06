<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\Database;
use App\Models\User;

// Initialize
Database::getInstance();

// Test data
$testUsername = 'test_user_' . time();
$testPassword = 'secure_password_123';

// Cleanup first
$db = Database::getInstance()->getConnection();
$db->exec("DELETE FROM users WHERE username = '$testUsername'");

echo "<h1>User Model Test</h1>";

// Test 1: Create User
try {
    $user = new User();
    $user->setUsername($testUsername);
    $user->setEmail("$testUsername@example.com");
    $user->setPassword($testPassword);
    
    if ($user->save()) {
        echo "✅ User saved successfully<br>";
    } else {
        die("❌ User save failed");
    }
} catch(Exception $e) {
    die("❌ Save test failed: " . $e->getMessage());
}

// Test 2: Find User
try {
    $foundUser = User::findByUsername($testUsername);
    
    if ($foundUser && $foundUser->getUsername() === $testUsername) {
        echo "✅ User retrieval successful<br>";
    } else {
        die("❌ User retrieval failed");
    }
} catch(Exception $e) {
    die("❌ Find test failed: " . $e->getMessage());
}

// Test 3: Password Verification
try {
    if ($foundUser->verifyPassword($testPassword)) {
        echo "✅ Password verification successful<br>";
    } else {
        die("❌ Password verification failed");
    }
} catch(Exception $e) {
    die("❌ Password test failed: " . $e->getMessage());
}

// Test 4: Duplicate Prevention
try {
    $duplicateUser = new User();
    $duplicateUser->setUsername($testUsername);
    $duplicateUser->setEmail("duplicate@example.com");
    $duplicateUser->setPassword('another_password');
    
    if ($duplicateUser->save()) {
        die("❌ Duplicate prevention failed");
    } else {
        echo "✅ Duplicate prevention working<br>";
    }
} catch(Exception $e) {
    echo "✅ Duplicate prevention working (exception caught)<br>";
}

// Cleanup
$db->exec("DELETE FROM users WHERE username = '$testUsername'");
echo "🧹 Test data cleaned";