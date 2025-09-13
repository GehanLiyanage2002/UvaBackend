<?php
require_once __DIR__ . '/../config/Database.php';

class Task
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(int $projectId, string $title, string $status = 'todo'): array
    {
        $title = trim($title);
        if ($title === '' || mb_strlen($title) > 200) {
            return ['ok' => false, 'message' => 'Invalid task title'];
        }

        $allowedStatuses = ['todo', 'in_progress', 'done'];
        if (!in_array($status, $allowedStatuses)) {
            return ['ok' => false, 'message' => 'Invalid status. Must be: todo, in_progress, or done'];
        }

        // Verify project exists
        $projectSql = "SELECT id FROM projects WHERE id = ? LIMIT 1";
        $projectStmt = $this->db->prepare($projectSql);
        if (!$projectStmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }
        
        $projectStmt->bind_param('i', $projectId);
        $projectStmt->execute();
        $projectResult = $projectStmt->get_result();
        
        if ($projectResult->num_rows === 0) {
            return ['ok' => false, 'message' => 'Project not found'];
        }

        $sql = "INSERT INTO tasks (project_id, title, status) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param('iss', $projectId, $title, $status);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        return [
            'ok' => true,
            'task' => [
                'id' => (int)$this->db->insert_id,
                'project_id' => $projectId,
                'title' => $title,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    public function listByProject(int $projectId, ?string $status = null): array
    {
        $sql = "SELECT id, project_id, title, status, created_at 
                FROM tasks 
                WHERE project_id = ?";
        
        $params = [$projectId];
        $types = 'i';
        
        if ($status !== null) {
            $allowedStatuses = ['todo', 'in_progress', 'done'];
            if (!in_array($status, $allowedStatuses)) {
                return [];
            }
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $sql .= " ORDER BY created_at DESC, id DESC";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('listByProject prepare: ' . $this->db->error);
            return [];
        }
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['project_id'] = (int)$row['project_id'];
            $rows[] = $row;
        }
        return $rows;
    }

    
    public function getAllTask(int $projectId): array
    {
        $sql = "SELECT id, project_id, title, status, created_at 
                FROM tasks";
        
        $sql .= " ORDER BY created_at DESC, id DESC";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('listByProject prepare: ' . $this->db->error);
            return [];
        }
        
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['project_id'] = (int)$row['project_id'];
            $rows[] = $row;
        }
        return $rows;
    }

    public function listByProjectAssigned(int $memberId, int $projectId): array
    {
        $sql = "SELECT t.id, t.project_id, t.title, t.status, t.created_at 
            FROM tasks t
            INNER JOIN task_assignments ta ON ta.task_id = t.id
            WHERE t.project_id = ? AND ta.user_id = ?";
    
        $params = [$projectId, $memberId];
        $types = 'ii';
        
        
        $sql .= " ORDER BY t.created_at DESC, t.id DESC";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('listByProjectAndUser prepare: ' . $this->db->error);
            return [];
        }
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['project_id'] = (int)$row['project_id'];
            $rows[] = $row;
        }
        return $rows;
    }

    public function listByProjectAssignedCoordinater(int $projectId): array
    {
        $sql = "SELECT t.id, t.project_id, t.title, t.status, t.created_at 
            FROM tasks t
            INNER JOIN task_assignments ta ON ta.task_id = t.id
            WHERE t.project_id = ?";
    
        $params = [$projectId];
        $types = 'i';
        
        
        $sql .= " ORDER BY t.created_at DESC, t.id DESC";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('listByProjectAndUser prepare: ' . $this->db->error);
            return [];
        }
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['project_id'] = (int)$row['project_id'];
            $rows[] = $row;
        }
        return $rows;
    }

    

    public function getById(int $taskId, int $projectId): ?array
    {
        $sql = "SELECT id, project_id, title, status, created_at
                FROM tasks
                WHERE id = ? AND project_id = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        
        $stmt->bind_param('ii', $taskId, $projectId);
        $stmt->execute();
        $res = $stmt->get_result();
        $task = $res->fetch_assoc();
        
        if (!$task) return null;

        return [
            'id' => (int)$task['id'],
            'project_id' => (int)$task['project_id'],
            'title' => $task['title'],
            'status' => $task['status'],
            'created_at' => $task['created_at']
        ];
    }

    public function updateTask(int $taskId, int $projectId, ?string $title = null, ?string $status = null): array
    {
        $fields = [];
        $params = [];
        $types = '';

        // Title
        if ($title !== null) {
            $title = trim($title);
            if ($title === '' || mb_strlen($title) > 200) {
                return ['ok' => false, 'message' => 'Invalid title'];
            }
            $fields[] = "title = ?";
            $params[] = $title;
            $types .= 's';
        }

        // Status
        if ($status !== null) {
            $allowedStatuses = ['todo', 'in_progress', 'done'];
            if (!in_array($status, $allowedStatuses)) {
                return ['ok' => false, 'message' => 'Invalid status. Must be: todo, in_progress, or done'];
            }
            $fields[] = "status = ?";
            $params[] = $status;
            $types .= 's';
        }

        // No fields to update
        if (empty($fields)) {
            return ['ok' => false, 'message' => 'Nothing to update'];
        }

        // SQL query
        $sql = "UPDATE tasks SET " . implode(', ', $fields) . " WHERE id = ? AND project_id = ?";
        $params[] = $taskId;
        $types .= 'i';
        $params[] = $projectId;
        $types .= 'i';

        // Prepare and execute
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'message' => 'Task not found or no changes made'];
        }

        return ['ok' => true, 'message' => 'Task updated successfully'];
    }

    public function updateStatus(int $taskId, int $projectId, string $status): array
    {
        $allowedStatuses = ['todo', 'in_progress', 'done'];
        if (!in_array($status, $allowedStatuses)) {
            return ['ok' => false, 'message' => 'Invalid status. Must be: todo, in_progress, or done'];
        }

        $sql = "UPDATE tasks SET status = ? WHERE id = ? AND project_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param('sii', $status, $taskId, $projectId);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'message' => 'Task not found'];
        }

        return ['ok' => true, 'message' => 'Task status updated successfully'];
    }

    public function deleteTask(int $taskId, int $projectId): array
    {
        $sql = "DELETE FROM tasks WHERE id = ? AND project_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param("ii", $taskId, $projectId);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        if ($affectedRows === 0) {
            return ['ok' => false, 'message' => 'Task not found'];
        }

        return ['ok' => true, 'message' => 'Task deleted successfully'];
    }

    public function getTaskCounts(int $projectId): array
    {
        $sql = "SELECT
                    SUM(CASE WHEN status='todo' THEN 1 ELSE 0 END) AS todo_count,
                    SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
                    SUM(CASE WHEN status='done' THEN 1 ELSE 0 END) AS done_count,
                    COUNT(*) AS total_count
                FROM tasks WHERE project_id = ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [
                'todo_count' => 0,
                'in_progress_count' => 0,
                'done_count' => 0,
                'total_count' => 0
            ];
        }
        
        $stmt->bind_param('i', $projectId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return [
            'todo_count' => (int)($result['todo_count'] ?? 0),
            'in_progress_count' => (int)($result['in_progress_count'] ?? 0),
            'done_count' => (int)($result['done_count'] ?? 0),
            'total_count' => (int)($result['total_count'] ?? 0)
        ];
    }

    public function moveTask(int $taskId, int $fromProjectId, int $toProjectId): array
    {
        // Verify both projects exist
        $projectSql = "SELECT id FROM projects WHERE id IN (?, ?)";
        $projectStmt = $this->db->prepare($projectSql);
        if (!$projectStmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }
        
        $projectStmt->bind_param('ii', $fromProjectId, $toProjectId);
        $projectStmt->execute();
        $projectResult = $projectStmt->get_result();
        
        if ($projectResult->num_rows !== 2) {
            return ['ok' => false, 'message' => 'One or both projects not found'];
        }

        // Move the task
        $sql = "UPDATE tasks SET project_id = ? WHERE id = ? AND project_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param('iii', $toProjectId, $taskId, $fromProjectId);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'message' => 'Task not found in source project'];
        }

        return ['ok' => true, 'message' => 'Task moved successfully'];
    }

    public function assignUserToTask(int $taskId, int $userId, int $assignedBy): array
    {
        $taskSql = "SELECT id FROM tasks WHERE id = ? LIMIT 1";
        $taskStmt = $this->db->prepare($taskSql);
        if (!$taskStmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }
        
        $taskStmt->bind_param('i', $taskId);
        $taskStmt->execute();
        $taskResult = $taskStmt->get_result();
        
        if ($taskResult->num_rows === 0) {
            return ['ok' => false, 'message' => 'Task not found'];
        }

        // Check if user is already assigned
        $checkSql = "SELECT id FROM task_assignments WHERE task_id = ? AND user_id = ? LIMIT 1";
        $checkStmt = $this->db->prepare($checkSql);
        if (!$checkStmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }
        
        $checkStmt->bind_param('ii', $taskId, $userId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            return ['ok' => false, 'message' => 'User is already assigned to this task'];
        }

        // Insert the assignment
        $sql = "INSERT INTO task_assignments (task_id, user_id, assigned_by) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param('iii', $taskId, $userId, $assignedBy);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        return ['ok' => true, 'message' => 'User assigned to task successfully'];
    }

    public function unassignUserFromTask(int $taskId, int $userId): array
    {
        $sql = "DELETE FROM task_assignments WHERE task_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param('ii', $taskId, $userId);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'message' => 'Assignment not found'];
        }

        return ['ok' => true, 'message' => 'User unassigned from task successfully'];
    }

    public function getTaskAssignments(int $taskId): array
    {
        $sql = "SELECT ta.id, ta.task_id, ta.user_id, ta.assigned_by, ta.assigned_at,
                       u.name as user_name, u.email as user_email,
                       ab.name as assigned_by_name
                FROM task_assignments ta
                JOIN users u ON ta.user_id = u.id
                JOIN users ab ON ta.assigned_by = ab.id
                WHERE ta.task_id = ?
                ORDER BY ta.assigned_at DESC";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('getTaskAssignments prepare: ' . $this->db->error);
            return [];
        }
        
        $stmt->bind_param('i', $taskId);
        $stmt->execute();
        $res = $stmt->get_result();

        $assignments = [];
        while ($row = $res->fetch_assoc()) {
            $assignments[] = [
                'id' => (int)$row['id'],
                'task_id' => (int)$row['task_id'],
                'user_id' => (int)$row['user_id'],
                'user_name' => $row['user_name'],
                'user_email' => $row['user_email'],
                'assigned_by' => (int)$row['assigned_by'],
                'assigned_by_name' => $row['assigned_by_name'],
                'assigned_at' => $row['assigned_at']
            ];
        }
        
        return $assignments;
    }
}