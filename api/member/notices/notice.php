<?php
// api/member/notices/notice.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/NoticeService.php';


try {
    $user = AuthMiddleware::requireAuth(['member']);
    $memberId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new NoticeService();
    
    switch ($method) {
        case 'GET':
            handleGetRequests($svc, $memberId, $action);
            break;
        default:
            Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }

} catch (Throwable $e) {
    error_log('[projects] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => 'Server error'], 500);
}

function handleGetRequests($svc, $memberId, $action) {
    switch ($action) {
        case 'list':
            // GET /api/member/notices/notice.php?action=list&status=active&priority=high&is_public=1&page=1&limit=10 (all optional)
            $result = $svc->getAllNotices();
            Response::json(['success' => true, 'data' => $result['notices'],]);
            break;
            
        case 'view':
            // GET /api/member/notices/notice.php?action=view&id=1
            $noticeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($noticeId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid notice id'], 400);
            }
            
            $notice = $svc->getNoticeById($noticeId);
            if (!$notice) {
                Response::json(['success' => false, 'message' => 'Notice not found'], 404);
            }
            
            Response::json(['success' => true, 'notice' => $notice]);
            break;
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for GET request'], 400);
    }
}