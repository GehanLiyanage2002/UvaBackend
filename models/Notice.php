<?php
// models/Notice.php
require_once __DIR__ . '/../config/Database.php';

class Notice
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(string $title, string $content, string $priority = 'normal', string $status = 'active', ?string $expiresAt = null, bool $isPublic = true, int $createdBy = 0): array
    {
        // Validate inputs
        $title = trim($title);
        if ($title === '' || mb_strlen($title) > 255) {
            return ['ok' => false, 'message' => 'Invalid title'];
        }

        $content = trim($content);
        if ($content === '') {
            return ['ok' => false, 'message' => 'Content is required'];
        }

        $allowedPriorities = ['normal', 'high', 'urgent'];
        if (!in_array($priority, $allowedPriorities)) {
            return ['ok' => false, 'message' => 'Invalid priority. Must be: normal, high, or urgent'];
        }

        $allowedStatuses = ['active', 'inactive', 'archived'];
        if (!in_array($status, $allowedStatuses)) {
            return ['ok' => false, 'message' => 'Invalid status. Must be: active, inactive, or archived'];
        }

        if ($createdBy <= 0) {
            return ['ok' => false, 'message' => 'Invalid created by ID'];
        }

        // Validate expires_at if provided
        if ($expiresAt !== null && !empty($expiresAt)) {
            $expireDate = DateTime::createFromFormat('Y-m-d H:i:s', $expiresAt);
        }

        $sql = "INSERT INTO notices (title, content, priority, status, expires_at, is_public, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $isPublicInt = $isPublic ? 1 : 0;
        $stmt->bind_param('sssssis', $title, $content, $priority, $status, $expiresAt, $isPublicInt, $createdBy);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        return [
            'ok' => true,
            'notice' => [
                'id' => (int)$this->db->insert_id,
                'title' => $title,
                'content' => $content,
                'priority' => $priority,
                'status' => $status,
                'expires_at' => $expiresAt,
                'is_public' => $isPublic,
                'created_by' => $createdBy,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM notices";
        
        $params = [];
        $types = '';
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('getAll prepare: ' . $this->db->error);
            return [];
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $res = $stmt->get_result();

        $notices = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['is_public'] = (bool)$row['is_public'];
            $row['created_by'] = (int)$row['created_by'];
            $notices[] = $row;
        }
        return $notices;
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT id, title, content, priority, status, expires_at, is_public, created_by, created_at, updated_at
                FROM notices
                WHERE id = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $notice = $res->fetch_assoc();
        
        if (!$notice) return null;

        return [
            'id' => (int)$notice['id'],
            'title' => $notice['title'],
            'content' => $notice['content'],
            'priority' => $notice['priority'],
            'status' => $notice['status'],
            'expires_at' => $notice['expires_at'],
            'is_public' => (bool)$notice['is_public'],
            'created_by' => (int)$notice['created_by'],
            'created_at' => $notice['created_at'],
            'updated_at' => $notice['updated_at']
        ];
    }

    public function update(int $id, ?string $title = null, ?string $content = null, ?string $priority = null, ?string $status = null, ?string $expiresAt = null, ?bool $isPublic = null): array
    {
        $fields = [];
        $params = [];
        $types = '';

        // Title
        if ($title !== null) {
            $title = trim($title);
            if ($title === '' || mb_strlen($title) > 255) {
                return ['ok' => false, 'message' => 'Invalid title'];
            }
            $fields[] = "title = ?";
            $params[] = $title;
            $types .= 's';
        }

        // Content
        if ($content !== null) {
            $content = trim($content);
            if ($content === '') {
                return ['ok' => false, 'message' => 'Content cannot be empty'];
            }
            $fields[] = "content = ?";
            $params[] = $content;
            $types .= 's';
        }

        // Priority
        if ($priority !== null) {
            $allowedPriorities = ['normal', 'high', 'urgent'];
            if (!in_array($priority, $allowedPriorities)) {
                return ['ok' => false, 'message' => 'Invalid priority. Must be: normal, high, or urgent'];
            }
            $fields[] = "priority = ?";
            $params[] = $priority;
            $types .= 's';
        }

        // Status
        if ($status !== null) {
            $allowedStatuses = ['active', 'inactive', 'archived'];
            if (!in_array($status, $allowedStatuses)) {
                return ['ok' => false, 'message' => 'Invalid status. Must be: active, inactive, or archived'];
            }
            $fields[] = "status = ?";
            $params[] = $status;
            $types .= 's';
        }

        // Expires At
        if ($expiresAt !== null) {
            if (!empty($expiresAt)) {
                $expireDate = DateTime::createFromFormat('Y-m-d H:i:s', $expiresAt);
            }
            $fields[] = "expires_at = ?";
            $params[] = $expiresAt ?: null;
            $types .= 's';
        }

        // Is Public
        if ($isPublic !== null) {
            $fields[] = "is_public = ?";
            $params[] = $isPublic ? 1 : 0;
            $types .= 'i';
        }

        // No fields to update
        if (empty($fields)) {
            return ['ok' => false, 'message' => 'Nothing to update'];
        }

        // Add updated_at timestamp
        $fields[] = "updated_at = CURRENT_TIMESTAMP";

        // SQL query
        $sql = "UPDATE notices SET " . implode(', ', $fields) . " WHERE id = ?";
        $params[] = $id;
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
            return ['ok' => false, 'message' => 'Notice not found or no changes made'];
        }

        return ['ok' => true, 'message' => 'Notice updated successfully'];
    }

    public function delete(int $id): array
    {
        $sql = "DELETE FROM notices WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        if ($affectedRows === 0) {
            return ['ok' => false, 'message' => 'Notice not found'];
        }

        return ['ok' => true, 'message' => 'Notice deleted successfully'];
    }

    public function getCount(?string $status = null, ?string $priority = null, ?bool $isPublic = null, ?int $createdBy = null): int
    {
        $sql = "SELECT COUNT(*) as total FROM notices WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($status !== null) {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        if ($priority !== null) {
            $sql .= " AND priority = ?";
            $params[] = $priority;
            $types .= 's';
        }
        
        if ($isPublic !== null) {
            $sql .= " AND is_public = ?";
            $params[] = $isPublic ? 1 : 0;
            $types .= 'i';
        }
        
        if ($createdBy !== null && $createdBy > 0) {
            $sql .= " AND created_by = ?";
            $params[] = $createdBy;
            $types .= 'i';
        }

        // Add non-expired filter for active notices
        if ($status === null || $status === 'active') {
            $sql .= " AND (expires_at IS NULL OR expires_at > NOW())";
        }
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return 0;
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return (int)$row['total'];
    }
}



