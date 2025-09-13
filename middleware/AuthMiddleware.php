<?php
// middleware/AuthMiddleware.php
require_once __DIR__.'/../services/AuthService.php';
require_once __DIR__.'/../utils/Response.php';

class AuthMiddleware {
    public static function requireAuth(?array $roles = null): array {
        $auth = new AuthService();
        $user = $auth->currentUserFromSessionOrJwt();
        if (!$user) {
            Response::json(['success'=>false, 'message'=>'Unauthorized'], 401);
        }
        if ($roles && !in_array($user['role'], $roles, true)) {
            Response::json(['success'=>false, 'message'=>'Forbidden: insufficient role'], 403);
        }
        return $user;
    }
}
