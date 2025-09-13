<?php
require_once __DIR__ . '/../config/Database.php';

class Project
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(int $managerId, string $title, ?string $description = null ,$start_date =null ,$end_date = null): array
    {
        $title = trim($title);
        $description = ($description !== null) ? trim($description) : null;
        if ($title === '' || mb_strlen($title) > 150) {
            return ['ok' => false, 'message' => 'Invalid project title'];
        }

        $sql = "INSERT INTO projects (title, description, manager_id, start_date, end_date) VALUES (?,?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];

        $stmt->bind_param('ssssi', $title, $description, $managerId, $start_date, $end_date);
        if (!$stmt->execute()) return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];

        return [
            'ok' => true,
            'project' => [
                'id' => (int)$this->db->insert_id,
                'title' => $title,
                'description' => $description ?? '',
                'start_date' => $start_date ?? '',
                'end_date' => $end_date ?? '',
            ]
        ];
    }

    public function listWithCounts(int $managerId): array
    {
        $sql = "
          SELECT 
            p.id,
            p.title,
            COALESCE(p.description,'') AS description,
            SUM(CASE WHEN t.status='todo' THEN 1 ELSE 0 END)        AS todo_count,
            SUM(CASE WHEN t.status='in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
            SUM(CASE WHEN t.status='done' THEN 1 ELSE 0 END)        AS done_count
          FROM projects p
          LEFT JOIN tasks t ON t.project_id = p.id
          WHERE p.manager_id = ?
          GROUP BY p.id, p.title, p.description
          ORDER BY p.created_at DESC, p.id DESC
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('listWithCounts prepare: ' . $this->db->error);
            return [];
        }
        $stmt->bind_param('i', $managerId);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $row['id']                 = (int)$row['id'];
            $row['todo_count']         = (int)$row['todo_count'];
            $row['in_progress_count']  = (int)$row['in_progress_count'];
            $row['done_count']         = (int)$row['done_count'];
            // keep a testing_count=0 for your UI badge
            $row['testing_count']      = 0;
            $rows[] = $row;
        }
        return $rows;
    }

    public function listWithCountsMember(int $memberId): array
    {
        $sql = "
            SELECT 
                p.id,
                p.title,
                COALESCE(p.description, '') AS description,
                SUM(CASE WHEN t.status = 'todo' THEN 1 ELSE 0 END) AS todo_count,
                SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
                SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) AS done_count
            FROM projects p
            INNER JOIN project_members mp ON p.id = mp.project_id
            LEFT JOIN tasks t ON t.project_id = p.id
            WHERE mp.user_id = ?
            GROUP BY p.id, p.title, p.description
            ORDER BY p.created_at DESC, p.id DESC
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('listWithCounts prepare: ' . $this->db->error);
            return [];
        }
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['todo_count'] = (int)$row['todo_count'];
            $row['in_progress_count'] = (int)$row['in_progress_count'];
            $row['done_count'] = (int)$row['done_count'];
            $row['testing_count'] = 0; // Keep for UI badge compatibility
            $rows[] = $row;
        }
        return $rows;
    }

    public function listForMember(int $memberId): array
    {
        $sql = "
            SELECT 
                p.id,
                p.title,
                COALESCE(p.description,'') AS description,
                SUM(CASE WHEN t.status='todo' THEN 1 ELSE 0 END)        AS todo_count,
                SUM(CASE WHEN t.status='in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
                SUM(CASE WHEN t.status='done' THEN 1 ELSE 0 END)        AS done_count
            FROM projects p
            INNER JOIN project_members pm ON pm.project_id = p.id
            LEFT JOIN tasks t ON t.project_id = p.id
            WHERE pm.user_id = ?
            GROUP BY p.id, p.title, p.description
            ORDER BY p.created_at DESC, p.id DESC
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('listWithCountsByMember prepare: ' . $this->db->error);
            return [];
        }
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $row['id']                 = (int)$row['id'];
            $row['todo_count']         = (int)$row['todo_count'];
            $row['in_progress_count']  = (int)$row['in_progress_count'];
            $row['done_count']         = (int)$row['done_count'];
            // keep a testing_count=0 for your UI badge
            $row['testing_count']      = 0;
            $rows[] = $row;
        }
        return $rows;
    }

    public function listForCoordinator(): array
    {
        $sql = "
            SELECT 
                p.id,
                p.title,
                COALESCE(p.description,'') AS description,
                SUM(CASE WHEN t.status = 'todo' THEN 1 ELSE 0 END)        AS todo_count,
                SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
                SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END)        AS done_count,
                GROUP_CONCAT(DISTINCT s.full_name SEPARATOR ', ')         AS supervisors,
                GROUP_CONCAT(DISTINCT s.id SEPARATOR ', ')         AS supervisor_id
            FROM projects p
            INNER JOIN project_members pm 
                ON pm.project_id = p.id
            LEFT JOIN tasks t 
                ON t.project_id = p.id
            LEFT JOIN project_supervisors ps 
                ON ps.project_id = p.id
            LEFT JOIN supervisors s 
                ON s.id = ps.supervisor_id
            GROUP BY p.id, p.title, p.description
            ORDER BY p.created_at DESC, p.id DESC
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('listWithCountsByMember prepare: ' . $this->db->error);
            return [];
        }
        $stmt->execute();
        $res = $stmt->get_result();

        $sqlS = "SELECT u.id, u.name, u.email, ps.assigned_by, ps.assigned_at
                FROM project_supervisors ps
                JOIN users u ON u.id = ps.supervisor_id
                WHERE ps.project_id = ?
                ORDER BY u.name ASC";

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $row['id']                 = (int)$row['id'];
            $row['todo_count']         = (int)$row['todo_count'];
            $row['in_progress_count']  = (int)$row['in_progress_count'];
            $row['done_count']         = (int)$row['done_count'];
            // keep a testing_count=0 for your UI badge
            $row['testing_count']      = 0;
            $rows[] = $row;
        }
        return $rows;
    }

    

    public function getByIdWithDetails(int $managerId, int $projectId): ?array
    {
        $sql = "SELECT p.id, p.title, COALESCE(p.description,'') AS description, p.manager_id, p.created_at, p.start_date, p.end_date, u.name as manager_name
                FROM projects as p , users as u
                WHERE p.id = ? AND p.manager_id = ? AND p.manager_id = u.id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param('ii', $projectId, $managerId);
        $stmt->execute();
        $res = $stmt->get_result();
        $p = $res->fetch_assoc();
        if (!$p) return null;

        // counts (todo | in_progress | done)
        $sqlC = "SELECT
                    SUM(CASE WHEN status='todo' THEN 1 ELSE 0 END)        AS todo_count,
                    SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
                    SUM(CASE WHEN status='done' THEN 1 ELSE 0 END)        AS done_count
                FROM tasks WHERE project_id = ?";
        $stmtC = $this->db->prepare($sqlC);
        $stmtC->bind_param('i', $projectId);
        $stmtC->execute();
        $counts = $stmtC->get_result()->fetch_assoc() ?: [
            'todo_count' => 0,
            'in_progress_count' => 0,
            'done_count' => 0
        ];

        // members
        $sqlM = "SELECT u.id, u.name, u.email
                FROM project_members pm
                JOIN users u ON u.id = pm.user_id
                WHERE pm.project_id = ?
                ORDER BY u.name ASC";
        $stmtM = $this->db->prepare($sqlM);
        $stmtM->bind_param('i', $projectId);
        $stmtM->execute();
        $members = $stmtM->get_result()->fetch_all(MYSQLI_ASSOC);

        // supervisors
        $sqlS = "SELECT u.id, u.full_name, u.email, ps.assigned_by, ps.assigned_at
                FROM project_supervisors ps
                JOIN supervisors u ON u.id = ps.supervisor_id
                WHERE ps.project_id = ?
                ORDER BY u.full_name ASC";
        $stmtS = $this->db->prepare($sqlS);
        $stmtS->bind_param('i', $projectId);
        $stmtS->execute();
        $supervisors = $stmtS->get_result()->fetch_all(MYSQLI_ASSOC);

        // tasks
        $sqlT = "SELECT id, title, status, created_at
                FROM tasks
                WHERE project_id = ?
                ORDER BY created_at DESC";
        $stmtT = $this->db->prepare($sqlT);
        $stmtT->bind_param('i', $projectId);
        $stmtT->execute();
        $tasks = $stmtT->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'id'          => (int)$p['id'],
            'title'       => $p['title'],
            'manager'      => $p['manager_name'],
            'description' => $p['description'],
            'manager_id'  => (int)$p['manager_id'],
            'start_date' => $p['start_date'],
            'end_date' => $p['end_date'],
            'created_at'  => $p['created_at'],
            'counts' => [
                'todo_count'        => (int)$counts['todo_count'],
                'in_progress_count' => (int)$counts['in_progress_count'],
                'testing_count'     => 0, // your UI expects it; DB has no such status
                'done_count'        => (int)$counts['done_count'],
            ],
            'members' => $members,
            'supervisors' => $supervisors,
            'tasks' => $tasks,
        ];
    }

    public function getByIdWithDetailsByMember(int $memberId, int $projectId): ?array
    {
        $sql = "SELECT 
            p.id, 
            p.title, 
            COALESCE(p.description,'') AS description, 
            p.manager_id, 
            p.created_at, 
            p.start_date, 
            p.end_date,
            u.name AS manager_name
        FROM projects p
        INNER JOIN project_members pm ON pm.project_id = p.id
        INNER JOIN users u ON u.id = p.manager_id
        WHERE p.id = ? 
        AND pm.user_id = ?
        LIMIT 1";


        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param('ii', $projectId, $memberId);
        $stmt->execute();
        $res = $stmt->get_result();
        $p = $res->fetch_assoc();
        if (!$p) return null;

        // counts (todo | in_progress | done)
        $sqlC = "SELECT
                    SUM(CASE WHEN status='todo' THEN 1 ELSE 0 END)        AS todo_count,
                    SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
                    SUM(CASE WHEN status='done' THEN 1 ELSE 0 END)        AS done_count
                FROM tasks WHERE project_id = ?";
        $stmtC = $this->db->prepare($sqlC);
        $stmtC->bind_param('i', $projectId);
        $stmtC->execute();
        $counts = $stmtC->get_result()->fetch_assoc() ?: [
            'todo_count' => 0,
            'in_progress_count' => 0,
            'done_count' => 0
        ];

        // members
        $sqlM = "SELECT u.id, u.name, u.email
                FROM project_members pm
                JOIN users u ON u.id = pm.user_id
                WHERE pm.project_id = ?
                ORDER BY u.name ASC";
        $stmtM = $this->db->prepare($sqlM);
        $stmtM->bind_param('i', $projectId);
        $stmtM->execute();
        $members = $stmtM->get_result()->fetch_all(MYSQLI_ASSOC);

        // supervisors
        $sqlS = "SELECT u.id, u.full_name, u.email, ps.assigned_by, ps.assigned_at
                FROM project_supervisors ps
                JOIN supervisors u ON u.id = ps.supervisor_id
                WHERE ps.project_id = ?
                ORDER BY u.full_name ASC";


        $stmtS = $this->db->prepare($sqlS);
        $stmtS->bind_param('i', $projectId);
        $stmtS->execute();
        $supervisors = $stmtS->get_result()->fetch_all(MYSQLI_ASSOC);

        // tasks
        $sqlT = "SELECT id, title, status, created_at
                FROM tasks
                WHERE project_id = ?
                ORDER BY created_at DESC";
        $stmtT = $this->db->prepare($sqlT);
        $stmtT->bind_param('i', $projectId);
        $stmtT->execute();
        $tasks = $stmtT->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'id'          => (int)$p['id'],
            'title'       => $p['title'],
            'description' => $p['description'],
            'manager'      => $p['manager_name'],
            'manager_id'  => (int)$p['manager_id'],
            'start_date' => $p['start_date'],
            'end_date' => $p['end_date'],
            'created_at'  => $p['created_at'],
            'counts' => [
                'todo_count'        => (int)$counts['todo_count'],
                'in_progress_count' => (int)$counts['in_progress_count'],
                'testing_count'     => 0, // your UI expects it; DB has no such status
                'done_count'        => (int)$counts['done_count'],
            ],
            'members' => $members,
            'supervisors' => $supervisors,
            'tasks' => $tasks,
        ];
    }

    
    public function getByIdWithDetailsByCoordinator(int $projectId): ?array
    {
        $sql = "SELECT p.id, p.title, COALESCE(p.description,'') AS description, p.manager_id, p.created_at, p.start_date, p.end_date
                FROM projects p
                INNER JOIN project_members pm ON pm.project_id = p.id
                WHERE p.id = ? 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param('i', $projectId);
        $stmt->execute();
        $res = $stmt->get_result();
        $p = $res->fetch_assoc();
        if (!$p) return null;

        // counts (todo | in_progress | done)
        $sqlC = "SELECT
                    SUM(CASE WHEN status='todo' THEN 1 ELSE 0 END)        AS todo_count,
                    SUM(CASE WHEN status='in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
                    SUM(CASE WHEN status='done' THEN 1 ELSE 0 END)        AS done_count
                FROM tasks WHERE project_id = ?";
        $stmtC = $this->db->prepare($sqlC);
        $stmtC->bind_param('i', $projectId);
        $stmtC->execute();
        $counts = $stmtC->get_result()->fetch_assoc() ?: [
            'todo_count' => 0,
            'in_progress_count' => 0,
            'done_count' => 0
        ];

        // members
        $sqlM = "SELECT u.id, u.name, u.email
                FROM project_members pm
                JOIN users u ON u.id = pm.user_id
                WHERE pm.project_id = ?
                ORDER BY u.name ASC";
        $stmtM = $this->db->prepare($sqlM);
        $stmtM->bind_param('i', $projectId);
        $stmtM->execute();
        $members = $stmtM->get_result()->fetch_all(MYSQLI_ASSOC);

        // supervisors
        $sqlS = "SELECT u.id, u.name, u.email, ps.assigned_by, ps.assigned_at
                FROM project_supervisors ps
                JOIN users u ON u.id = ps.supervisor_id
                WHERE ps.project_id = ?
                ORDER BY u.name ASC";
        $stmtS = $this->db->prepare($sqlS);
        $stmtS->bind_param('i', $projectId);
        $stmtS->execute();
        $supervisors = $stmtS->get_result()->fetch_all(MYSQLI_ASSOC);

        // tasks
        $sqlT = "SELECT id, title, status, created_at
                FROM tasks
                WHERE project_id = ?
                ORDER BY created_at DESC";
        $stmtT = $this->db->prepare($sqlT);
        $stmtT->bind_param('i', $projectId);
        $stmtT->execute();
        $tasks = $stmtT->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'id'          => (int)$p['id'],
            'title'       => $p['title'],
            'description' => $p['description'],
            'manager_id'  => (int)$p['manager_id'],
            'start_date' => $p['start_date'],
            'end_date' => $p['end_date'],
            'created_at'  => $p['created_at'],
            'counts' => [
                'todo_count'        => (int)$counts['todo_count'],
                'in_progress_count' => (int)$counts['in_progress_count'],
                'testing_count'     => 0, // your UI expects it; DB has no such status
                'done_count'        => (int)$counts['done_count'],
            ],
            'members' => $members,
            'supervisors' => $supervisors,
            'tasks' => $tasks,
        ];
    }

    public function updateDetails(
        int $managerId,
        int $projectId,
        ?string $title,
        ?string $description,
        ?string $start_date,
        ?string $end_date
    ): array {
        $fields = [];
        $params = [];
        $types  = '';
    
        // Title
        if ($title !== null) {
            $title = trim($title);
            if ($title === '' || mb_strlen($title) > 150) {
                return ['ok' => false, 'message' => 'Invalid title'];
            }
            $fields[] = "title = ?";
            $params[] = $title;
            $types   .= 's';
        }
    
        // Description
        if ($description !== null) {
            $description = trim($description);
            $fields[] = "description = ?";
            $params[] = ($description === '') ? null : $description;
            $types   .= 's';
        }
    
        // Start date
        if ($start_date !== null) {
            $start_date = trim($start_date);
            if ($start_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
                return ['ok' => false, 'message' => 'Invalid start date format (YYYY-MM-DD)'];
            }
            $fields[] = "start_date = ?";
            $params[] = ($start_date === '') ? null : $start_date;
            $types   .= 's';
        }
    
        // End date
        if ($end_date !== null) {
            $end_date = trim($end_date);
            if ($end_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
                return ['ok' => false, 'message' => 'Invalid end date format (YYYY-MM-DD)'];
            }
            $fields[] = "end_date = ?";
            $params[] = ($end_date === '') ? null : $end_date;
            $types   .= 's';
        }
    
        // No fields to update
        if (empty($fields)) {
            return ['ok' => false, 'message' => 'Nothing to update'];
        }
    
        // SQL query
        $sql = "UPDATE projects SET " . implode(', ', $fields) . " WHERE id = ? AND manager_id = ?";
        $params[] = $projectId;
        $types .= 'i';
        $params[] = $managerId;
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
    
        return ['ok' => true];
    }
    

    public function deleteProject(int $managerId, int $projectId): array
    {
        $sql = "DELETE FROM projects WHERE id = ? AND manager_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
    
        $stmt->bind_param("ii", $projectId, $managerId);
        if (!$stmt->execute()) return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
    
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        if ($affectedRows === 0) {
            return ['ok' => false, 'message' => 'Project not found or you do not have permission to delete it'];
        }
    
        return ['ok' => true, 'message' => 'Project deleted successfully'];
    }

    public function assignMember(int $managerId, int $projectId, int $memberId): array
    {
        $projectSql = "SELECT id FROM projects WHERE id = ? AND manager_id = ? LIMIT 1";
        $projectStmt = $this->db->prepare($projectSql);
        if (!$projectStmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }
        
        $projectStmt->bind_param('ii', $projectId, $managerId);
        $projectStmt->execute();
        $projectResult = $projectStmt->get_result();
        
        if ($projectResult->num_rows === 0) {
            return ['ok' => false, 'message' => 'Project not found or access denied'];
        }
        
        $userSql = "SELECT id FROM users WHERE id = ? AND role = 'member' LIMIT 1";
        $userStmt = $this->db->prepare($userSql);
        if (!$userStmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }
        
        $userStmt->bind_param('i', $memberId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        
        if ($userResult->num_rows === 0) {
            return ['ok' => false, 'message' => 'User not found or not a member'];
        }
        
        $existingSql = "SELECT id FROM project_members WHERE project_id = ? AND user_id = ? LIMIT 1";
        $existingStmt = $this->db->prepare($existingSql);
        if (!$existingStmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }
        
        $existingStmt->bind_param('ii', $projectId, $memberId);
        $existingStmt->execute();
        $existingResult = $existingStmt->get_result();
        
        if ($existingResult->num_rows > 0) {
            return ['ok' => false, 'message' => 'User is already assigned to this project'];
        }
        
        $insertSql = "INSERT INTO project_members (project_id, user_id, assigned_by) VALUES (?, ?, ?)";
        $insertStmt = $this->db->prepare($insertSql);
        if (!$insertStmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $insertStmt->bind_param('iii', $projectId, $memberId, $managerId);
        if (!$insertStmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $insertStmt->error];
        }

        return [
            'ok' => true,
            'message' => 'Member assigned successfully',
            'assignment' => [
                'id' => (int)$this->db->insert_id,
                'project_id' => $projectId,
                'user_id' => $memberId,
            ]
        ];
    }

    public function removeMember(int $managerId, int $projectId, int $memberId): array
    {
        $checkSql = "SELECT id FROM projects WHERE id = ? AND manager_id = ? LIMIT 1";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->bind_param('ii', $projectId, $managerId);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows === 0) {
            return ['ok' => false, 'message' => 'Project not found or access denied'];
        }
        
        $sql = "DELETE FROM project_members WHERE project_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $projectId, $memberId);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'message' => 'Member not found in this project'];
        }
        
        return ['ok' => true, 'message' => 'Member removed successfully'];
    }
    
}