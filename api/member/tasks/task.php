<?php
// api/member/tasks/task.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/TaskService.php';


try {
    $user = AuthMiddleware::requireAuth(['member']);
    $memberId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new TaskService();
    
    switch ($method) {
        case 'GET':
            handleGetRequests($svc, $memberId, $action);
            break;
        case 'POST':
            handlePostRequests($svc, $memberId, $action);
            break;
        case 'PUT':
            handlePutRequests($svc, $memberId, $action);
            break;
                
        default:
            Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }

} catch (Throwable $e) {
    error_log('[projects] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => $e->getMessage()], 500);
}

function handleGetRequests($svc, $memberId, $action) {
    switch ($action) {
        case 'list':
            // GET /api/member/tasks/task.php?action=list&id=1
            $projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $projects = $svc->listByProjectAssigned($memberId, $projectId);
            Response::json(['success' => true, 'tasks' => $projects]);
            break;

        case 'overview':
            // GET /api/manager/tasks/task.php?action=overview
            $overview = $svc->getTasksOverviewMember($memberId);
            Response::json(['success' => true, 'overview' => $overview]);
            break;
        
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for GET request'], 400);
    }
}


function handlePostRequests($svc, $memberId, $action) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($action) {
        case 'update':
            // POST /api/member/tasks/task.php?action=update
            // Body: {"project_id": 1, "id": 1, "title": "Updated Task", "status": "in_progress"}
            $projectId = isset($data['project_id']) ? (int)$data['project_id'] : 0;
            $taskId = isset($data['id']) ? (int)$data['id'] : 0;
            $title = array_key_exists('title', $data) ? $data['title'] : null;
            $status = array_key_exists('status', $data) ? $data['status'] : null;
            
            if ($projectId <= 0 || $taskId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project or task id'], 400);
            }
            
            $result = $svc->updateTaskDetailsMember($memberId, $projectId, $taskId, $title, $status);
            
            if ($result['ok'] ?? false) {
                $task = $svc->getTaskView($memberId, $projectId, $taskId);
                Response::json(['success' => true, 'task' => $task]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Update failed'], 400);
            break;
            
        case 'update-status':
            // POST /api/member/tasks/task.php?action=update-status
            // Body: {"project_id": 1, "id": 1, "status": "done"}
            $projectId = isset($data['project_id']) ? (int)$data['project_id'] : 0;
            $taskId = isset($data['id']) ? (int)$data['id'] : 0;
            $status = $data['status'] ?? '';
            
            if ($projectId <= 0 || $taskId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project or task id'], 400);
            }
            
            if (empty($status)) {
                Response::json(['success' => false, 'message' => 'Status is required'], 400);
            }
            
            $result = $svc->updateTaskStatusMember($memberId, $projectId, $taskId, $status);
            
            if ($result['ok'] ?? false) {
                $task = $svc->getTaskView($memberId, $projectId, $taskId);
                Response::json(['success' => true, 'task' => $task]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Status update failed'], 400);
            break;
            
        case 'move':
            // POST /api/member/tasks/task.php?action=move
            // Body: {"from_project_id": 1, "to_project_id": 2, "id": 1}
            $fromProjectId = isset($data['from_project_id']) ? (int)$data['from_project_id'] : 0;
            $toProjectId = isset($data['to_project_id']) ? (int)$data['to_project_id'] : 0;
            $taskId = isset($data['id']) ? (int)$data['id'] : 0;
            
            if ($fromProjectId <= 0 || $toProjectId <= 0 || $taskId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project or task id'], 400);
            }
            
            $result = $svc->moveTaskToProjectMember($memberId, $fromProjectId, $toProjectId, $taskId);
            
            if ($result['ok'] ?? false) {
                Response::json(['success' => true, 'message' => 'Task moved successfully']);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Move failed'], 400);
            break;
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for POST request'], 400);
    }
}
