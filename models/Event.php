<?php
require_once __DIR__ . '/../config/Database.php';

class Event
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(string $title, string $date, ?string $time = null, ?string $meetLink = null, ?string $description = null): array
    {
        $title = trim($title);
        if ($title === '' || mb_strlen($title) > 255) {
            return ['ok' => false, 'message' => 'Invalid event title. Must be between 1-255 characters'];
        }

        if (!$this->isValidDate($date)) {
            return ['ok' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD'];
        }

        if ($time !== null && !$this->isValidTime($time)) {
            return ['ok' => false, 'message' => 'Invalid time format. Use HH:MM:SS or HH:MM'];
        }

        if ($meetLink !== null) {
            $meetLink = trim($meetLink);
            if ($meetLink === '') {
                $meetLink = null;
            } elseif (mb_strlen($meetLink) > 255) {
                return ['ok' => false, 'message' => 'Meet link too long. Maximum 255 characters'];
            }
        }

        if ($description !== null) {
            $description = trim($description);
            if ($description === '') {
                $description = null;
            }
        }

        $sql = "INSERT INTO events (title, date, time, meet_link, description) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param('sssss', $title, $date, $time, $meetLink, $description);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        return [
            'ok' => true,
            'event' => [
                'id' => (int)$this->db->insert_id,
                'title' => $title,
                'date' => $date,
                'time' => $time,
                'meet_link' => $meetLink,
                'description' => $description,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    public function getAll(?string $orderBy = 'date', ?string $orderDirection = 'ASC'): array
    {
        $allowedOrderBy = ['id', 'title', 'date', 'time', 'created_at', 'updated_at'];
        $allowedOrderDirection = ['ASC', 'DESC'];

        if (!in_array($orderBy, $allowedOrderBy)) {
            $orderBy = 'date';
        }

        if (!in_array(strtoupper($orderDirection), $allowedOrderDirection)) {
            $orderDirection = 'ASC';
        }

        $sql = "SELECT id, title, date, time, meet_link, description, created_at, updated_at 
                FROM events 
                ORDER BY {$orderBy} {$orderDirection}";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('getAll prepare: ' . $this->db->error);
            return [];
        }
        
        $stmt->execute();
        $res = $stmt->get_result();

        $events = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $events[] = $row;
        }
        return $events;
    }

    public function getById(int $eventId): ?array
    {
        $sql = "SELECT id, title, date, time, meet_link, description, created_at, updated_at
                FROM events
                WHERE id = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        
        $stmt->bind_param('i', $eventId);
        $stmt->execute();
        $res = $stmt->get_result();
        $event = $res->fetch_assoc();
        
        if (!$event) return null;

        return [
            'id' => (int)$event['id'],
            'title' => $event['title'],
            'date' => $event['date'],
            'time' => $event['time'],
            'meet_link' => $event['meet_link'],
            'description' => $event['description'],
            'created_at' => $event['created_at'],
            'updated_at' => $event['updated_at']
        ];
    }

    public function update(int $eventId, ?string $title = null, ?string $date = null, ?string $time = null, ?string $meetLink = null, ?string $description = null): array
    {
        $fields = [];
        $params = [];
        $types = '';

        if ($title !== null) {
            $title = trim($title);
            if ($title === '' || mb_strlen($title) > 255) {
                return ['ok' => false, 'message' => 'Invalid title. Must be between 1-255 characters'];
            }
            $fields[] = "title = ?";
            $params[] = $title;
            $types .= 's';
        }

        if ($date !== null) {
            if (!$this->isValidDate($date)) {
                return ['ok' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD'];
            }
            $fields[] = "date = ?";
            $params[] = $date;
            $types .= 's';
        }

        if ($time !== null) {
            if ($time === '') {
                $time = null;
            } elseif (!$this->isValidTime($time)) {
                return ['ok' => false, 'message' => 'Invalid time format. Use HH:MM:SS or HH:MM'];
            }
            $fields[] = "time = ?";
            $params[] = $time;
            $types .= 's';
        }

        if ($meetLink !== null) {
            $meetLink = trim($meetLink);
            if ($meetLink === '') {
                $meetLink = null;
            } elseif (mb_strlen($meetLink) > 255) {
                return ['ok' => false, 'message' => 'Meet link too long. Maximum 255 characters'];
            }
            $fields[] = "meet_link = ?";
            $params[] = $meetLink;
            $types .= 's';
        }

        if ($description !== null) {
            $description = trim($description);
            if ($description === '') {
                $description = null;
            }
            $fields[] = "description = ?";
            $params[] = $description;
            $types .= 's';
        }

        if (empty($fields)) {
            return ['ok' => false, 'message' => 'Nothing to update'];
        }

        $fields[] = "updated_at = NOW()";

        $sql = "UPDATE events SET " . implode(', ', $fields) . " WHERE id = ?";
        $params[] = $eventId;
        $types .= 'i';

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'message' => 'Event not found or no changes made'];
        }

        return ['ok' => true, 'message' => 'Event updated successfully'];
    }

    public function delete(int $eventId): array
    {
        $sql = "DELETE FROM events WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param("i", $eventId);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        if ($affectedRows === 0) {
            return ['ok' => false, 'message' => 'Event not found'];
        }

        return ['ok' => true, 'message' => 'Event deleted successfully'];
    }

    public function getByDateRange(string $startDate, string $endDate): array
    {
        if (!$this->isValidDate($startDate) || !$this->isValidDate($endDate)) {
            return [];
        }

        $sql = "SELECT id, title, date, time, meet_link, description, created_at, updated_at 
                FROM events 
                WHERE date BETWEEN ? AND ?
                ORDER BY date ASC, time ASC";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('getByDateRange prepare: ' . $this->db->error);
            return [];
        }
        
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $res = $stmt->get_result();

        $events = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $events[] = $row;
        }
        return $events;
    }

    public function getUpcoming(int $limit = 10): array
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT id, title, date, time, meet_link, description, created_at, updated_at 
                FROM events 
                WHERE date >= ?
                ORDER BY date ASC, time ASC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('getUpcoming prepare: ' . $this->db->error);
            return [];
        }
        
        $stmt->bind_param('si', $today, $limit);
        $stmt->execute();
        $res = $stmt->get_result();

        $events = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $events[] = $row;
        }
        return $events;
    }

    private function isValidDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function isValidTime(string $time): bool
    {
        return preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $time);
    }
}