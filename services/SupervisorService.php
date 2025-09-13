<?php
// services/SupervisorService.php
require_once __DIR__ . '/../models/Supervisor.php';

class SupervisorService
{
    private Supervisor $supervisor;

    public function __construct()
    {
        $this->supervisor = new Supervisor();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function createSupervisor(string $fullName, string $email, string $contact, string $type, string $facultyName, string $departmentName, string $about = ''): array
    {
        if (empty(trim($fullName))) {
            return ['ok' => false, 'message' => 'Full name is required'];
        }

        if (empty(trim($email))) {
            return ['ok' => false, 'message' => 'Email is required'];
        }

        if (empty(trim($facultyName))) {
            return ['ok' => false, 'message' => 'Faculty name is required'];
        }

        if (empty(trim($departmentName))) {
            return ['ok' => false, 'message' => 'Department name is required'];
        }

        return $this->supervisor->create($fullName, $email, $contact, $type, $facultyName, $departmentName, $about);
    }

    public function getAllSupervisors(?string $type = null, ?string $facultyName = null, ?string $departmentName = null): array
    {
        return $this->supervisor->getAll($type, $facultyName, $departmentName);
    }

    public function getSupervisorById(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        return $this->supervisor->getById($id);
    }

    public function updateSupervisor(int $id, ?string $fullName = null, ?string $email = null, ?string $contact = null, ?string $type = null, ?string $facultyName = null, ?string $departmentName = null, ?string $about = null): array
    {
        if ($id <= 0) {
            return ['ok' => false, 'message' => 'Invalid supervisor ID'];
        }

        // Check if supervisor exists
        $existingSupervisor = $this->supervisor->getById($id);
        if (!$existingSupervisor) {
            return ['ok' => false, 'message' => 'Supervisor not found'];
        }

        return $this->supervisor->update($id, $fullName, $email, $contact, $type, $facultyName, $departmentName, $about);
    }

    public function deleteSupervisor(int $id): array
    {
        if ($id <= 0) {
            return ['ok' => false, 'message' => 'Invalid supervisor ID'];
        }

        // Check if supervisor exists
        $existingSupervisor = $this->supervisor->getById($id);
        if (!$existingSupervisor) {
            return ['ok' => false, 'message' => 'Supervisor not found'];
        }

        // TODO: Add checks for related data (projects, students, etc.) before deletion
        // Example: Check if supervisor is assigned to any active projects
        
        return $this->supervisor->delete($id);
    }

    public function assignSupervisorToProject(int $projectId, int $supervisorId, int $assignedBy): array 
    {
        if ($projectId <= 0) {
            return ['ok' => false, 'message' => 'Invalid project ID'];
        }
        
        if ($supervisorId <= 0) {
            return ['ok' => false, 'message' => 'Invalid supervisor ID'];
        }
        
        if ($assignedBy <= 0) {
            return ['ok' => false, 'message' => 'Invalid assigned by ID'];
        }
        
        $supervisor = $this->supervisor->getById($supervisorId);
        if (!$supervisor) {
            return ['ok' => false, 'message' => 'Supervisor not found'];
        }
        
        $db = Database::getConnection();
        
        // Check if project already has ANY supervisor assigned
        $checkSql = "SELECT id, supervisor_id FROM project_supervisors WHERE project_id = ? LIMIT 1";
        $checkStmt = $db->prepare($checkSql);
        if (!$checkStmt) {
            return ['ok' => false, 'message' => 'Database error'];
        }
        
        $checkStmt->bind_param('i', $projectId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $existingAssignment = $checkResult->fetch_assoc();
            if ($existingAssignment['supervisor_id'] == $supervisorId) {
                return ['ok' => false, 'message' => 'Supervisor is already assigned to this project'];
            } else {
                return ['ok' => false, 'message' => 'Project already has a supervisor assigned'];
            }
        }
        
        $sql = "INSERT INTO project_supervisors (project_id, supervisor_id, assigned_by) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'Database error'];
        }
        
        $stmt->bind_param('iii', $projectId, $supervisorId, $assignedBy);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'Failed to assign supervisor'];
        }
        
        return ['ok' => true, 'message' => 'Supervisor assigned to project successfully'];
    }

    public function unassignSupervisorFromProject(int $projectId, int $supervisorId): array
    {
        if ($projectId <= 0) {
            return ['ok' => false, 'message' => 'Invalid project ID'];
        }

        if ($supervisorId <= 0) {
            return ['ok' => false, 'message' => 'Invalid supervisor ID'];
        }

        $db = Database::getConnection();
        $sql = "DELETE FROM project_supervisors WHERE project_id = ? AND supervisor_id = ?";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'Database error'];
        }

        $stmt->bind_param('ii', $projectId, $supervisorId);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'Failed to unassign supervisor'];
        }

        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'message' => 'Supervisor assignment not found'];
        }

        return ['ok' => true, 'message' => 'Supervisor unassigned from project successfully'];
    }

    public function getProjectSupervisors(int $projectId): array
    {
        if ($projectId <= 0) {
            return [];
        }

        $db = Database::getConnection();
        $sql = "SELECT ps.id as assignment_id, ps.assigned_at, ps.assigned_by,
                       s.id, s.full_name, s.email, s.contact, s.type, s.faculty_name, s.department_name, s.about,
                       ab.name as assigned_by_name
                FROM project_supervisors ps
                JOIN supervisors s ON ps.supervisor_id = s.id
                LEFT JOIN users ab ON ps.assigned_by = ab.id
                WHERE ps.project_id = ?
                ORDER BY ps.assigned_at DESC";
        
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log('getProjectSupervisors prepare: ' . $db->error);
            return [];
        }
        
        $stmt->bind_param('i', $projectId);
        $stmt->execute();
        $res = $stmt->get_result();

        $supervisors = [];
        while ($row = $res->fetch_assoc()) {
            $supervisors[] = [
                'assignment_id' => (int)$row['assignment_id'],
                'supervisor' => [
                    'id' => (int)$row['id'],
                    'full_name' => $row['full_name'],
                    'email' => $row['email'],
                    'contact' => $row['contact'],
                    'type' => $row['type'],
                    'faculty_name' => $row['faculty_name'],
                    'department_name' => $row['department_name'],
                    'about' => $row['about']
                ],
                'assigned_at' => $row['assigned_at'],
                'assigned_by' => (int)$row['assigned_by'],
                'assigned_by_name' => $row['assigned_by_name']
            ];
        }
        
        return $supervisors;
    }

}