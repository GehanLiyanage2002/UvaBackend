<?php
// api/register.php
require_once __DIR__ . '/../config/Cors.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../utils/Response.php';

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$name = trim($data['name'] ?? '');
$email = strtolower(trim($data['email'] ?? ''));
$password = $data['password'] ?? '';
$role = $data['role'] ?? 'member';

$auth = new AuthService();
$res = $auth->register($name, $email, $password, $role);

if ($res['ok'] ?? false) {
    Response::json(data: ['success' => true, 'message' => 'Registered successfully']);
}
Response::json(['success' => false, 'message' => $res['message'] ?? 'Registration failed'], 400);
