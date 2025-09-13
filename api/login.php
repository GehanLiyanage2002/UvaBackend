<?php
// api/login.php
require_once __DIR__.'/../config/Cors.php';
require_once __DIR__.'/../services/AuthService.php';
require_once __DIR__.'/../utils/Response.php';

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$email = strtolower(trim($data['email'] ?? ''));
$password = $data['password'] ?? '';
$remember = (bool)($data['remember'] ?? false);

$auth = new AuthService();
$res = $auth->login($email, $password, $remember);

if ($res['ok'] ?? false) {
    // Return token + user for your React localStorage
    Response::json([
        'success'=>true,
        'token'=>$res['token'],
        'user'=>$res['user']
    ]);
}
Response::json(['success'=>false, 'message'=>$res['message'] ?? 'Login failed'], 401);
