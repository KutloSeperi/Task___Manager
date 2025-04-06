<?php
require __DIR__ . '/app/Core/Session.php';

$session = new App\Core\Session();

// Test setting values
$session->set('test_key', 'session_works');
$session->set('user_id', 1);

// Test getting values
echo $session->get('test_key') . "<br>"; // Should show "session_works"
echo $session->get('non_existent', 'default_val') . "<br>"; // Should show "default_val"

// Test isLoggedIn
echo $session->isLoggedIn() ? "Logged IN" : "Logged OUT"; // Should show "Logged IN"

// Test remove
$session->remove('test_key');
echo $session->get('test_key') ?? 'removed'; // Should show "removed"

// Test destroy
$session->destroy();
echo $session->isLoggedIn() ? "Still logged" : "Destroyed"; // Should show "Destroyed"