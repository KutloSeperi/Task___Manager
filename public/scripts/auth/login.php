<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Controllers\AuthController;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$authController = new AuthController();
$response = $authController->login(
    $data['username'] ?? '',
    $data['password'] ?? ''
);

echo json_encode($response);