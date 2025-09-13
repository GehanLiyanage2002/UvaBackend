<?php
// api/member/supervisors/supervisor.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/SupervisorService.php';


try {
    $user = AuthMiddleware::requireAuth(['member']);
    $memberId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new SupervisorService();
    
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
            // GET /api/member/supervisors/supervisor.php?action=list&type=supervisor&faculty_name=Engineering&department_name=IT (all optional)
            $type = $_GET['type'] ?? null;
            $facultyName = $_GET['faculty_name'] ?? null;
            $departmentName = $_GET['department_name'] ?? null;
            
            $supervisors = $svc->getAllSupervisors($type, $facultyName, $departmentName);
            Response::json(['success' => true, 'supervisors' => $supervisors]);
            break;
            
        case 'view':
            // GET /api/member/supervisors/supervisor.php?action=view&id=1
            $supervisorId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($supervisorId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid supervisor id'], 400);
            }
            
            $supervisor = $svc->getSupervisorById($supervisorId);
            if (!$supervisor) {
                Response::json(['success' => false, 'message' => 'Supervisor not found'], 404);
            }
            
            Response::json(['success' => true, 'supervisor' => $supervisor]);
            break;
        
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for GET request'], 400);
    }
}

