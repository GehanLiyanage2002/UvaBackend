<?php
// api/manager/tasks.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/TaskService.php';


try {
    $user = AuthMiddleware::requireAuth(['manager','member']);
    $managerId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new TaskService();
    
    switch ($method) {
        case 'GET':
            handleGetRequests($svc, $managerId, $action);
            break;
            
        case 'POST':
            handlePostRequests($svc, $managerId, $action);
            break;
            
        case 'PUT':
            handlePutRequests($svc, $managerId, $action);
            break;
            
        case 'DELETE':
            handleDeleteRequests($svc, $managerId, $action);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }

} catch (Throwable $e) {
    error_log('[tasks] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => 'Server error'], 500);
}

function handleGetRequests($svc, $managerId, $action) {
    switch ($action) {
        case 'list-all':
            // GET /api/manager/tasks/task.php?action=list
            $tasks = $svc->listTaskAll($managerId);
            Response::json(['success' => true, 'tasks' => $tasks]);
            break;
        case 'list':
            // GET /api/manager/tasks/task.php?action=list&project_id=1&status=todo (optional)
            $projectId = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
            $status = $_GET['status'] ?? null;
            
            if ($projectId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project id'], 400);
            }
            
            $tasks = $svc->listForProject($managerId, $projectId, $status);
            Response::json(['success' => true, 'tasks' => $tasks]);
            break;
            
        case 'view':
            // GET /api/manager/tasks/task.php?action=view&project_id=1&id=1
            $projectId = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
            $taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($projectId <= 0 || $taskId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project or task id'], 400);
            }
            
            $task = $svc->getTaskView($managerId, $projectId, $taskId);
            if (!$task) {
                Response::json(['success' => false, 'message' => 'Task not found'], 404);
            }
            
            Response::json(['success' => true, 'task' => $task]);
            break;
            
        case 'counts':
            // GET /api/manager/tasks/task.php?action=counts&project_id=1
            $projectId = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
            
            if ($projectId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project id'], 400);
            }
            
            $counts = $svc->getTaskCounts($managerId, $projectId);
            Response::json(['success' => true, 'counts' => $counts]);
            break;
            
        case 'overview':
            // GET /api/manager/tasks/task.php?action=overview
            $overview = $svc->getTasksOverview($managerId);
            Response::json(['success' => true, 'overview' => $overview]);
            break;
            
        case 'by-status':
            // GET /api/manager/tasks/task.php?action=by-status&status=todo
            $status = $_GET['status'] ?? '';
            if (empty($status)) {
                Response::json(['success' => false, 'message' => 'Status is required'], 400);
            }
            
            $tasks = $svc->getAllTasksByStatus($managerId, $status);
            Response::json(['success' => true, 'tasks' => $tasks]);
            break;

        case 'assignments':
            // GET /api/manager/tasks/task.php?action=assignments&project_id=1&task_id=1
            $projectId = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
            $taskId = isset($_GET['task_id']) ? (int)$_GET['task_id'] : 0;
            
            if ($projectId <= 0 || $taskId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project or task id'], 400);
            }
            
            $assignments = $svc->getTaskAssignments($managerId, $projectId, $taskId);
            Response::json(['success' => true, 'assignments' => $assignments]);
            break;

        case 'project-members':
            // GET /api/manager/tasks/task.php?action=project-members&project_id=1
            $projectId = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
            
            if ($projectId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project id'], 400);
            }
            
            $members = $svc->getProjectMembers($managerId, $projectId);
            Response::json(['success' => true, 'members' => $members]);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for GET request'], 400);
    }
}

function handlePostRequests($svc, $managerId, $action) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($action) {
        case 'create':
            // POST /api/manager/tasks/task.php?action=create
            // Body: {"project_id": 1, "title": "New Task", "status": "todo"}
            $projectId = isset($data['project_id']) ? (int)$data['project_id'] : 0;
            $title = trim($data['title'] ?? '');
            $status = $data['status'] ?? 'todo';
            
            if ($projectId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project id'], 400);
            }
            
            if (empty($title)) {
                Response::json(['success' => false, 'message' => 'Title is required'], 400);
            }
            
            $result = $svc->createForProject($managerId, $projectId, $title, $status);
            
            if ($result['ok'] ?? false) {
                Response::json(['success' => true, 'task' => $result['task']]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Failed to create task'], 400);
            break;
            
        case 'update':
            // POST /api/manager/tasks/task.php?action=update
            // Body: {"project_id": 1, "id": 1, "title": "Updated Task", "status": "in_progress"}
            $projectId = isset($data['project_id']) ? (int)$data['project_id'] : 0;
            $taskId = isset($data['id']) ? (int)$data['id'] : 0;
            $title = array_key_exists('title', $data) ? $data['title'] : null;
            $status = array_key_exists('status', $data) ? $data['status'] : null;
            
            if ($projectId <= 0 || $taskId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project or task id'], 400);
            }
            
            $result = $svc->updateTaskDetails($managerId, $projectId, $taskId, $title, $status);
            
            if ($result['ok'] ?? false) {
                $task = $svc->getTaskView($managerId, $projectId, $taskId);
                Response::json(['success' => true, 'task' => $task]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Update failed'], 400);
            break;
            
        case 'update-status':
            // POST /api/manager/tasks/task.php?action=update-status
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
            
            $result = $svc->updateTaskStatus($managerId, $projectId, $taskId, $status);
            
            if ($result['ok'] ?? false) {
                $task = $svc->getTaskView($managerId, $projectId, $taskId);
                Response::json(['success' => true, 'task' => $task]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Status update failed'], 400);
            break;
            
        case 'move':
            // POST /api/manager/tasks/task.php?action=move
            // Body: {"from_project_id": 1, "to_project_id": 2, "id": 1}
            $fromProjectId = isset($data['from_project_id']) ? (int)$data['from_project_id'] : 0;
            $toProjectId = isset($data['to_project_id']) ? (int)$data['to_project_id'] : 0;
            $taskId = isset($data['id']) ? (int)$data['id'] : 0;
            
            if ($fromProjectId <= 0 || $toProjectId <= 0 || $taskId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project or task id'], 400);
            }
            
            $result = $svc->moveTaskToProject($managerId, $fromProjectId, $toProjectId, $taskId);
            
            if ($result['ok'] ?? false) {
                Response::json(['success' => true, 'message' => 'Task moved successfully']);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Move failed'], 400);
            break;

        case 'assign-user':
            // POST /api/manager/tasks/task.php?action=assign-user
            // Body: {"project_id": 1, "task_id": 1, "user_id": 2}
            $projectId = isset($data['project_id']) ? (int)$data['project_id'] : 0;
            $taskId = isset($data['task_id']) ? (int)$data['task_id'] : 0;
            $userId = isset($data['user_id']) ? (int)$data['user_id'] : 0;
            
            if ($projectId <= 0 || $taskId <= 0 || $userId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project, task, or user id'], 400);
            }
            
            $result = $svc->assignUserToTask($managerId, $projectId, $taskId, $userId);
            
            if ($result['ok'] ?? false) {
                $assignments = $svc->getTaskAssignments($managerId, $projectId, $taskId);
                Response::json(['success' => true, 'message' => 'User assigned successfully', 'assignments' => $assignments]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Assignment failed'], 400);
            break;

        case 'unassign-user':
            // POST /api/manager/tasks/task.php?action=unassign-user
            // Body: {"project_id": 1, "task_id": 1, "user_id": 2}
            $projectId = isset($data['project_id']) ? (int)$data['project_id'] : 0;
            $taskId = isset($data['task_id']) ? (int)$data['task_id'] : 0;
            $userId = isset($data['user_id']) ? (int)$data['user_id'] : 0;
            
            if ($projectId <= 0 || $taskId <= 0 || $userId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project, task, or user id'], 400);
            }
            
            $result = $svc->unassignUserFromTask($managerId, $projectId, $taskId, $userId);
            
            if ($result['ok'] ?? false) {
                $assignments = $svc->getTaskAssignments($managerId, $projectId, $taskId);
                Response::json(['success' => true, 'message' => 'User unassigned successfully', 'assignments' => $assignments]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Unassignment failed'], 400);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for POST request'], 400);
    }
}

function handlePutRequests($svc, $managerId, $action) {
    // Alternative update method using PUT
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($action) {
        case 'update':
            // PUT /api/manager/tasks/task.php?action=update&project_id=1&id=1
            // Body: {"title": "Updated Task", "status": "in_progress"}
            $projectId = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
            $taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $title = array_key_exists('title', $data) ? $data['title'] : null;
            $status = array_key_exists('status', $data) ? $data['status'] : null;
            
            if ($projectId <= 0 || $taskId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project or task id'], 400);
            }
            
            $result = $svc->updateTaskDetails($managerId, $projectId, $taskId, $title, $status);
            
            if ($result['ok'] ?? false) {
                $task = $svc->getTaskView($managerId, $projectId, $taskId);
                Response::json(['success' => true, 'task' => $task]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Update failed'], 400);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for PUT request'], 400);
    }
}

function handleDeleteRequests($svc, $managerId, $action) {
    switch ($action) {
        case 'delete':
            // DELETE /api/manager/tasks/task.php?action=delete&project_id=1&id=1
            $projectId = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
            $taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($projectId <= 0 || $taskId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project or task id'], 400);
            }
            
            $result = $svc->deleteTask($managerId, $projectId, $taskId);
            
            if ($result['ok'] ?? false) {
                Response::json(['success' => true, 'message' => 'Task deleted successfully']);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Delete failed'], 400);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for DELETE request'], 400);
    }
}