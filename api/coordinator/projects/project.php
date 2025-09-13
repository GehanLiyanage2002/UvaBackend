<?php
// api/coordinator/projects/project.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/ProjectService.php';


try {
    $user = AuthMiddleware::requireAuth(['coordinator']);
    $coordinatorId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new ProjectService();
    
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
            // GET /api/coordinator/projects/project.php?action=list
            $projects = $svc->listForCoordinator();
            Response::json(['success' => true, 'tasks' => $projects]);
            break;
        case 'view':
            // GET /api/coordinator/projects/project.php?action=view&id=1

            $projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($projectId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project id'], 400);
            }
            
            $task = $svc->getByIdWithDetailsByCoordinator($projectId);

            if (!$task) {
                Response::json(['success' => false, 'message' => 'Project not found'], 404);
            }
            
            Response::json(['success' => true, 'task' => $task]);
            break;
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for GET request'], 400);
    }
}

