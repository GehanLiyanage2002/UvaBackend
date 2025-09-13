<?php
// api/coordinator/tasks/task.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/TaskService.php';


try {
    $user = AuthMiddleware::requireAuth(['coordinator']);
    $coordinatorId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new TaskService();
    
    switch ($method) {
        case 'GET':
            handleGetRequests($svc, $coordinatorId, $action);
            break;
        default:
            Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }

} catch (Throwable $e) {
    error_log('[projects] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => 'Server error'], 500);
}

function handleGetRequests($svc, $coordinatorId, $action) {
    switch ($action) {
        case 'list':
            // GET /api/coordinator/tasks/task.php?action=list&id=1
            $projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $projects = $svc->listByProjectAssignedCoordinater($projectId);
            Response::json(['success' => true, 'tasks' => $projects]);
            break;
        
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for GET request'], 400);
    }
}

