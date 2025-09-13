<?php
// models/Report.php
require_once __DIR__ . '/../config/Database.php';

class Report {
    private mysqli $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function create(int $projectId, string $url): array {
        $stmt = $this->db->prepare("INSERT INTO reports (project_id, url) VALUES (?, ?)");
        $stmt->bind_param('is', $projectId, $url);
        
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'Failed to create report record: ' . $stmt->error];
        }
        
        return [
            'ok' => true,
            'id' => $this->db->insert_id,
            'message' => 'Report record created successfully'
        ];
    }
    
    public function getByProjectId(int $projectId): array {
        $stmt = $this->db->prepare("SELECT * FROM reports WHERE project_id = ? ORDER BY created_at DESC");
        $stmt->bind_param('i', $projectId);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $reports = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['project_id'] = (int)$row['project_id'];
            $reports[] = $row;
        }
        
        return [
            'ok' => true,
            'reports' => $reports
        ];
    }
    
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM reports WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        
        if (!$row) return null;
        
        return [
            'id' => (int)$row['id'],
            'project_id' => (int)$row['project_id'],
            'url' => $row['url'],
            'created_at' => $row['created_at']
        ];
    }
    
    public function delete(int $id): array {
        $stmt = $this->db->prepare("DELETE FROM reports WHERE id = ?");
        $stmt->bind_param('i', $id);
        
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'Failed to delete report record: ' . $stmt->error];
        }
        
        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'message' => 'Report not found'];
        }
        
        return ['ok' => true, 'message' => 'Report record deleted successfully'];
    }
    
    public function deleteOldReports(int $daysOld = 7): array {
        $stmt = $this->db->prepare("DELETE FROM reports WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->bind_param('i', $daysOld);
        
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'Failed to delete old report records: ' . $stmt->error];
        }
        
        $deletedCount = $stmt->affected_rows;
        
        return [
            'ok' => true,
            'message' => "Deleted {$deletedCount} old report records",
            'deleted_count' => $deletedCount
        ];
    }
    
    public function getReportWithManagerCheck(int $reportId, int $managerId): ?array {
        $stmt = $this->db->prepare("
            SELECT r.*, p.title as project_title
            FROM reports r 
            INNER JOIN projects p ON r.project_id = p.id 
            WHERE r.id = ? AND p.manager_id = ?
            LIMIT 1
        ");
        $stmt->bind_param('ii', $reportId, $managerId);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        
        if (!$row) return null;
        
        return [
            'id' => (int)$row['id'],
            'project_id' => (int)$row['project_id'],
            'url' => $row['url'],
            'created_at' => $row['created_at'],
            'project_title' => $row['project_title']
        ];
    }

    public function getReportsByManager(int $managerId): array {
        $stmt = $this->db->prepare("
            SELECT r.*, p.title as project_title
            FROM reports r 
            INNER JOIN projects p ON r.project_id = p.id 
            WHERE p.manager_id = ? 
            ORDER BY r.created_at DESC
        ");
        $stmt->bind_param('i', $managerId);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $reports = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['project_id'] = (int)$row['project_id'];
            $reports[] = $row;
        }
        
        return [
            'ok' => true,
            'reports' => $reports
        ];
    }

    public function getRecentReports(int $limit = 10): array {
        $stmt = $this->db->prepare("
            SELECT r.*, p.title as project_title 
            FROM reports r 
            LEFT JOIN projects p ON r.project_id = p.id 
            ORDER BY r.created_at DESC 
            LIMIT ?
        ");
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $reports = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['project_id'] = (int)$row['project_id'];
            $reports[] = $row;
        }
        
        return [
            'ok' => true,
            'reports' => $reports
        ];
    }

    public function getReportsByMember(int $memberId): array {
        $stmt = $this->db->prepare("
            SELECT r.*, p.title as project_title
            FROM reports r 
            INNER JOIN projects p ON r.project_id = p.id 
            INNER JOIN project_members pm ON p.id = pm.project_id
            WHERE pm.user_id = ? 
            ORDER BY r.created_at DESC
        ");
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $reports = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['project_id'] = (int)$row['project_id'];
            $reports[] = $row;
        }
        
        return [
            'ok' => true,
            'reports' => $reports
        ];
    }
}