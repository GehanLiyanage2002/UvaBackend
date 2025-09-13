<?php
// api/manager/supervisors.php 
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/SupervisorService.php';

try {
    $user = AuthMiddleware::requireAuth(['coordinator','manager']);
    $managerId = 0;
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new SupervisorService();
    
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
    error_log('[supervisors] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => $e->getMessage()], 500);
}

function handleGetRequests($svc, $action) {
    switch ($action) {
        case 'list':
            // GET /api/coordinator/supervisors/supervisor.php?action=list&type=supervisor&faculty_name=Engineering&department_name=IT (all optional)
            $type = $_GET['type'] ?? null;
            $facultyName = $_GET['faculty_name'] ?? null;
            $departmentName = $_GET['department_name'] ?? null;
            
            $supervisors = $svc->getAllSupervisors($type, $facultyName, $departmentName);
            Response::json(['success' => true, 'supervisors' => $supervisors]);
            break;
            
        case 'view':
            // GET /api/coordinator/supervisors/supervisor.php?action=view&id=1
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

function handlePostRequests($svc, $action) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($action) {
        case 'create':
            // POST /api/coordinator/supervisors/supervisor.php?action=create
            // Body: {"full_name": "John Doe", "email": "john@example.com", "contact": "123456789", "type": "supervisor", "faculty_name": "Engineering", "department_name": "IT", "about": "Description"}
            $fullName = trim($data['full_name'] ?? '');
            $email = trim($data['email'] ?? '');
            $contact = trim($data['contact'] ?? '');
            $type = $data['type'] ?? '';
            $facultyName = trim($data['faculty_name'] ?? '');
            $departmentName = trim($data['department_name'] ?? '');
            $about = trim($data['about'] ?? '');
            
            if (empty($fullName)) {
                Response::json(['success' => false, 'message' => 'Full name is required'], 400);
            }
            
            if (empty($email)) {
                Response::json(['success' => false, 'message' => 'Email is required'], 400);
            }
            
            if (empty($type)) {
                Response::json(['success' => false, 'message' => 'Type is required'], 400);
            }
            
            if (empty($facultyName)) {
                Response::json(['success' => false, 'message' => 'Faculty name is required'], 400);
            }
            
            if (empty($departmentName)) {
                Response::json(['success' => false, 'message' => 'Department name is required'], 400);
            }
            
            $result = $svc->createSupervisor($fullName, $email, $contact, $type, $facultyName, $departmentName, $about);
            
            if ($result['ok'] ?? false) {
                Response::json(['success' => true, 'supervisor' => $result['supervisor']]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Failed to create supervisor'], 400);
            break;
            
        case 'update':
            // POST /api/coordinator/supervisors/supervisor.php?action=update
            // Body: {"id": 1, "full_name": "Updated Name", "email": "updated@example.com", ...}
            $supervisorId = isset($data['id']) ? (int)$data['id'] : 0;
            $fullName = array_key_exists('full_name', $data) ? $data['full_name'] : null;
            $email = array_key_exists('email', $data) ? $data['email'] : null;
            $contact = array_key_exists('contact', $data) ? $data['contact'] : null;
            $type = array_key_exists('type', $data) ? $data['type'] : null;
            $facultyName = array_key_exists('faculty_name', $data) ? $data['faculty_name'] : null;
            $departmentName = array_key_exists('department_name', $data) ? $data['department_name'] : null;
            $about = array_key_exists('about', $data) ? $data['about'] : null;
            
            if ($supervisorId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid supervisor id'], 400);
            }
            
            $result = $svc->updateSupervisor($supervisorId, $fullName, $email, $contact, $type, $facultyName, $departmentName, $about);
            
            if ($result['ok'] ?? false) {
                $supervisor = $svc->getSupervisorById($supervisorId);
                Response::json(['success' => true, 'supervisor' => $supervisor]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Update failed'], 400);
            break;
        case 'assign-to-project':
            // POST /supervisors.php?action=assign-to-project
            // Body: {"project_id": 1, "supervisor_id": 2}
            $projectId = isset($data['project_id']) ? (int)$data['project_id'] : 0;
            $supervisorId = isset($data['supervisor_id']) ? (int)$data['supervisor_id'] : 0;
            $userId = isset($GLOBALS['user']['id']) ? (int)$GLOBALS['user']['id'] : 1; // Get from auth middleware
            
            if ($projectId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project id'], 400);
            }
            
            if ($supervisorId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid supervisor id'], 400);
            }
            
            $result = $svc->assignSupervisorToProject($projectId, $supervisorId, $userId);
            
            if ($result['ok'] ?? false) {
                $supervisors = $svc->getProjectSupervisors($projectId);
                Response::json(['success' => true, 'message' => 'Supervisor assigned successfully', 'supervisors' => $supervisors]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Assignment failed'], 400);
            break;

        case 'unassign-from-project':
            // POST /supervisors.php?action=unassign-from-project
            // Body: {"project_id": 1, "supervisor_id": 2}
            $projectId = isset($data['project_id']) ? (int)$data['project_id'] : 0;
            $supervisorId = isset($data['supervisor_id']) ? (int)$data['supervisor_id'] : 0;
            
            if ($projectId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid project id'], 400);
            }
            
            if ($supervisorId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid supervisor id'], 400);
            }
            
            $result = $svc->unassignSupervisorFromProject($projectId, $supervisorId);
            
            if ($result['ok'] ?? false) {
                $supervisors = $svc->getProjectSupervisors($projectId);
                Response::json(['success' => true, 'message' => 'Supervisor unassigned successfully', 'supervisors' => $supervisors]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Unassignment failed'], 400);
            break;
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for POST request'], 400);
    }
}

function handlePutRequests($svc, $action) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($action) {
        case 'update':
            // PUT /api/coordinator/supervisors/supervisor.php?action=update&id=1
            // Body: {"full_name": "Updated Name", "email": "updated@example.com", ...}
            $supervisorId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $fullName = array_key_exists('full_name', $data) ? $data['full_name'] : null;
            $email = array_key_exists('email', $data) ? $data['email'] : null;
            $contact = array_key_exists('contact', $data) ? $data['contact'] : null;
            $type = array_key_exists('type', $data) ? $data['type'] : null;
            $facultyName = array_key_exists('faculty_name', $data) ? $data['faculty_name'] : null;
            $departmentName = array_key_exists('department_name', $data) ? $data['department_name'] : null;
            $about = array_key_exists('about', $data) ? $data['about'] : null;
            
            if ($supervisorId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid supervisor id'], 400);
            }
            
            $result = $svc->updateSupervisor($supervisorId, $fullName, $email, $contact, $type, $facultyName, $departmentName, $about);
            
            if ($result['ok'] ?? false) {
                $supervisor = $svc->getSupervisorById($supervisorId);
                Response::json(['success' => true, 'supervisor' => $supervisor]);
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
            // DELETE /api/coordinator/supervisors/supervisor.php?action=delete&id=1
            $supervisorId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($supervisorId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid supervisor id'], 400);
            }
            
            $result = $svc->deleteSupervisor($supervisorId);
            
            if ($result['ok'] ?? false) {
                Response::json(['success' => true, 'message' => 'Supervisor deleted successfully']);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Delete failed'], 400);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for DELETE request'], 400);
    }
}