<?php
// api/member/notices/notice.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/MemberService.php';


try {
    $user = AuthMiddleware::requireAuth(['manager','coordinator','member']);
    $memberId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new MemberService();
    
    switch ($method) {
        case 'GET':
            handleGetRequests($svc, $memberId, $action);
            break;
        default:
            Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }

} catch (Throwable $e) {
    error_log('[members] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => 'Server error'], 500);
}

function handleGetRequests($svc, $memberId, $action) {
    switch ($action) {
        case 'list':
            // GET /api/manager/members/member.php?action=list
            $result = $svc->getAllMembers();
            Response::json(['success' => true, 'data' => $result['members'],]);
            break;
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for GET request'], 400);
    }
}