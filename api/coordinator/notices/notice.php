<?php

// api/manager/notices.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/NoticeService.php';

try {
    $user = AuthMiddleware::requireAuth(['coordinator','manager']);
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new NoticeService();
    
    $userRole = $user['role'] ?? '';

    if (($userRole === 'manager' || $userRole === 'member') && $method !== 'GET') {
        Response::json(['success' => false, 'message' => 'Managers can only view notices'], 403);
    }

    switch ($method) {
        case 'GET':
            handleGetRequests($svc, $action);
            break;
            
        case 'POST':
            handlePostRequests($svc, $action);
            break;
            
        case 'PUT':
            handlePutRequests($svc, $action);
            break;
            
        case 'DELETE':
            handleDeleteRequests($svc, $action);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }

} catch (Throwable $e) {
    error_log('[notices] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => $e->getMessage()], 500);
}

function handleGetRequests($svc, $action) {
    switch ($action) {
        case 'list':
            // GET /api/coordinator/notices/notice.php?action=list&status=active&priority=high&is_public=1&page=1&limit=10 (all optional)
            $result = $svc->getAllNotices();
            Response::json(['success' => true, 'data' => $result['notices'],]);
            break;
            
        case 'view':
            // GET /api/coordinator/notices/notice.php?action=view&id=1
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

function handlePostRequests($svc, $action) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($action) {
        case 'create':
            // POST /api/coordinator/notices/notice.php?action=create
            // Body: {"title": "Important Notice", "content": "This is notice content", "priority": "high", "status": "active", "expires_at": "2024-12-31 23:59:59", "is_public": true}
            $title = trim($data['title'] ?? '');
            $content = trim($data['content'] ?? '');
            $priority = $data['priority'] ?? 'normal';
            $status = $data['status'] ?? 'active';
            $expiresAt = $data['expires_at'] ?? null;
            $isPublic = $data['is_public'] ?? true;
            $createdBy = isset($GLOBALS['user']['id']) ? (int)$GLOBALS['user']['id'] : 1; // Get from auth middleware
            
            if (empty($title)) {
                Response::json(['success' => false, 'message' => 'Title is required'], 400);
            }
            
            if (empty($content)) {
                Response::json(['success' => false, 'message' => 'Content is required'], 400);
            }
            
            $result = $svc->createNotice($title, $content, $priority, $status, $expiresAt, (bool)$isPublic, $createdBy);
            
            if ($result['ok'] ?? false) {
                Response::json(['success' => true, 'notice' => $result['notice']]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Failed to create notice'], 400);
            break;
            
        case 'update':
            // POST /api/coordinator/notices/notice.php?action=update
            // Body: {"id": 1, "title": "Updated Notice", "content": "Updated content", ...}
            $noticeId = isset($data['id']) ? (int)$data['id'] : 0;
            $title = array_key_exists('title', $data) ? $data['title'] : null;
            $content = array_key_exists('content', $data) ? $data['content'] : null;
            $priority = array_key_exists('priority', $data) ? $data['priority'] : null;
            $status = array_key_exists('status', $data) ? $data['status'] : null;
            $expiresAt = array_key_exists('expires_at', $data) ? $data['expires_at'] : null;
            $isPublic = array_key_exists('is_public', $data) ? (bool)$data['is_public'] : null;
            
            if ($noticeId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid notice id'], 400);
            }
            
            $result = $svc->updateNotice($noticeId, $title, $content, $priority, $status, $expiresAt, $isPublic);
            
            if ($result['ok'] ?? false) {
                $notice = $svc->getNoticeById($noticeId);
                Response::json(['success' => true, 'notice' => $notice]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Update failed'], 400);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for POST request'], 400);
    }
}

function handlePutRequests($svc, $action) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($action) {
        case 'update':
            // PUT /api/coordinator/notices/notice.php?action=update&id=1
            // Body: {"title": "Updated Notice", "content": "Updated content", ...}
            $noticeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $title = array_key_exists('title', $data) ? $data['title'] : null;
            $content = array_key_exists('content', $data) ? $data['content'] : null;
            $priority = array_key_exists('priority', $data) ? $data['priority'] : null;
            $status = array_key_exists('status', $data) ? $data['status'] : null;
            $expiresAt = array_key_exists('expires_at', $data) ? $data['expires_at'] : null;
            $isPublic = array_key_exists('is_public', $data) ? (bool)$data['is_public'] : null;
            
            if ($noticeId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid notice id'], 400);
            }
            
            $result = $svc->updateNotice($noticeId, $title, $content, $priority, $status, $expiresAt, $isPublic);
            
            if ($result['ok'] ?? false) {
                $notice = $svc->getNoticeById($noticeId);
                Response::json(['success' => true, 'notice' => $notice]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Update failed'], 400);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for PUT request'], 400);
    }
}

function handleDeleteRequests($svc, $action) {
    switch ($action) {
        case 'delete':
            // DELETE /api/coordinator/notices/notice.php?action=delete&id=1
            $noticeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($noticeId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid notice id'], 400);
            }
            
            $result = $svc->deleteNotice($noticeId);
            
            if ($result['ok'] ?? false) {
                Response::json(['success' => true, 'message' => 'Notice deleted successfully']);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Delete failed'], 400);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for DELETE request'], 400);
    }
}