<?php
require_once __DIR__ . '/../../config/Cors.php';
require_once __DIR__ . '/../../utils/Response.php';

session_start();

// ===== Default Coordinator Credentials =====
$defaultEmail    = 'coordinator@uwu.ac.lk';
$defaultPassword = 'Coord@123'; // change if needed

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true) ?? [];
$email    = strtolower(trim($data['email'] ?? ''));
$password = $data['password'] ?? '';

if (!$email || !$password) {
    Response::json(['success' => false, 'message' => 'All fields are required'], 400);
}

// Check email format
if (!preg_match('/@uwu\.ac\.lk$/', $email)) {
    Response::json(['success' => false, 'message' => 'Please use a valid UWU coordinator email'], 400);
}

// Validate credentials
if ($email !== $defaultEmail || $password !== $defaultPassword) {
    Response::json(['success' => false, 'message' => 'Invalid coordinator credentials'], 401);
}

// Regenerate session for safety
session_regenerate_id(true);

// Store in session
$_SESSION['uid']   = 1; // no DB id, but you can use fixed
$_SESSION['role']  = 'coordinator';
$_SESSION['email'] = $defaultEmail;

// Optionally set a simple cookie so frontend can detect session
setcookie('pms_has_session', '1', time() + 60*60*24*30, '/', '', false, false);

Response::json([
    'success' => true,
    'message' => 'Coordinator login successful',
    'user' => [
        'email' => $defaultEmail,
        'role'  => 'coordinator'
    ]
]);
