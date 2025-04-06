<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    echo "✅ Database connection successful!<br>";
    echo "MySQL Version: " . $db->getAttribute(\PDO::ATTR_SERVER_VERSION);
} catch (Exception $e) {
    die("❌ Database error: " . $e->getMessage());
}