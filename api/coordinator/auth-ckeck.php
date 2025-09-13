<?php
require_once __DIR__ . '/../../config/Cors.php';
require_once __DIR__ . '/../../utils/Response.php';

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coordinator') {
    Response::json(['success' => false, 'message' => 'Unauthorized'], 401);
}

Response::json([
    'success' => true,
    'user' => [
        'email' => $_SESSION['email'] ?? '',
        'role'  => $_SESSION['role']
    ]
]);
