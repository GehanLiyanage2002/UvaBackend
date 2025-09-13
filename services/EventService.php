<?php
// services/EventService.php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../config/Database.php';

class EventService
{
    private Event $event;

    public function __construct()
    {
        $this->event = new Event();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function create(int $userId, string $title, string $date, ?string $time = null, ?string $meetLink = null, ?string $description = null): array
    {
        if (!$this->verifyUser($userId)) {
            return ['ok' => false, 'message' => 'User not found or access denied'];
        }

        return $this->event->create($title, $date, $time, $meetLink, $description);
    }

    public function getAll(int $userId, ?string $orderBy = 'date', ?string $orderDirection = 'ASC'): array
    {
        if (!$this->verifyUser($userId)) {
            return [];
        }

        return $this->event->getAll($orderBy, $orderDirection);
    }

    public function getById(int $userId, int $eventId): ?array
    {
        if (!$this->verifyUser($userId)) {
            return null;
        }

        return $this->event->getById($eventId);
    }

    public function update(int $userId, int $eventId, ?string $title = null, ?string $date = null, ?string $time = null, ?string $meetLink = null, ?string $description = null): array
    {
        if (!$this->verifyUser($userId)) {
            return ['ok' => false, 'message' => 'User not found or access denied'];
        }

        return $this->event->update($eventId, $title, $date, $time, $meetLink, $description);
    }

    public function delete(int $userId, int $eventId): array
    {
        if (!$this->verifyUser($userId)) {
            return ['ok' => false, 'message' => 'User not found or access denied'];
        }

        return $this->event->delete($eventId);
    }

    public function getByDateRange(int $userId, string $startDate, string $endDate): array
    {
        if (!$this->verifyUser($userId)) {
            return [];
        }

        return $this->event->getByDateRange($startDate, $endDate);
    }

    public function getUpcoming(int $userId, int $limit = 10): array
    {
        if (!$this->verifyUser($userId)) {
            return [];
        }

        return $this->event->getUpcoming($limit);
    }

    public function getTodaysEvents(int $userId): array
    {
        if (!$this->verifyUser($userId)) {
            return [];
        }

        $today = date('Y-m-d');
        return $this->event->getByDateRange($today, $today);
    }

    public function getThisWeekEvents(int $userId): array
    {
        if (!$this->verifyUser($userId)) {
            return [];
        }

        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
        
        return $this->event->getByDateRange($startOfWeek, $endOfWeek);
    }

    public function getThisMonthEvents(int $userId): array
    {
        if (!$this->verifyUser($userId)) {
            return [];
        }

        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        
        return $this->event->getByDateRange($startOfMonth, $endOfMonth);
    }

    public function getEventsByMonth(int $userId, int $year, int $month): array
    {
        if (!$this->verifyUser($userId)) {
            return [];
        }

        if ($month < 1 || $month > 12) {
            return [];
        }

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        
        return $this->event->getByDateRange($startDate, $endDate);
    }

    public function getEventsOverview(int $userId): array
    {
        if (!$this->verifyUser($userId)) {
            return [
                'total_events' => 0,
                'today_events' => 0,
                'this_week_events' => 0,
                'upcoming_events' => 0
            ];
        }

        $db = Database::getConnection();
        $today = date('Y-m-d');
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));

        $sql = "SELECT
                    COUNT(*) as total_events,
                    SUM(CASE WHEN date = ? THEN 1 ELSE 0 END) as today_events,
                    SUM(CASE WHEN date BETWEEN ? AND ? THEN 1 ELSE 0 END) as this_week_events,
                    SUM(CASE WHEN date >= ? THEN 1 ELSE 0 END) as upcoming_events
                FROM events";
        
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return [
                'total_events' => 0,
                'today_events' => 0,
                'this_week_events' => 0,
                'upcoming_events' => 0
            ];
        }
        
        $stmt->bind_param('ssss', $today, $startOfWeek, $endOfWeek, $today);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return [
            'total_events' => (int)($result['total_events'] ?? 0),
            'today_events' => (int)($result['today_events'] ?? 0),
            'this_week_events' => (int)($result['this_week_events'] ?? 0),
            'upcoming_events' => (int)($result['upcoming_events'] ?? 0)
        ];
    }

    public function searchEvents(int $userId, string $query, ?string $startDate = null, ?string $endDate = null): array
    {
        if (!$this->verifyUser($userId)) {
            return [];
        }

        $query = trim($query);
        if ($query === '') {
            return [];
        }

        $db = Database::getConnection();
        $sql = "SELECT id, title, date, time, meet_link, description, created_at, updated_at 
                FROM events 
                WHERE (title LIKE ? OR description LIKE ?)";
        
        $params = ["%$query%", "%$query%"];
        $types = 'ss';

        if ($startDate !== null && $endDate !== null) {
            $sql .= " AND date BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
            $types .= 'ss';
        }

        $sql .= " ORDER BY date ASC, time ASC";
        
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log('searchEvents prepare: ' . $db->error);
            return [];
        }
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $events = [];
        while ($row = $result->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $events[] = $row;
        }

        return $events;
    }

    public function getEventsWithMeetLinks(int $userId): array
    {
        if (!$this->verifyUser($userId)) {
            return [];
        }

        $db = Database::getConnection();
        $sql = "SELECT id, title, date, time, meet_link, description, created_at, updated_at 
                FROM events 
                WHERE meet_link IS NOT NULL AND meet_link != ''
                ORDER BY date ASC, time ASC";
        
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log('getEventsWithMeetLinks prepare: ' . $db->error);
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        $events = [];
        while ($row = $result->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $events[] = $row;
        }

        return $events;
    }

    public function duplicateEvent(int $userId, int $eventId, string $newDate): array
    {
        if (!$this->verifyUser($userId)) {
            return ['ok' => false, 'message' => 'User not found or access denied'];
        }

        $originalEvent = $this->event->getById($eventId);
        if (!$originalEvent) {
            return ['ok' => false, 'message' => 'Original event not found'];
        }

        return $this->event->create(
            $originalEvent['title'] . ' (Copy)',
            $newDate,
            $originalEvent['time'],
            $originalEvent['meet_link'],
            $originalEvent['description']
        );
    }

    private function verifyUser(int $userId): bool
    {
        $db = Database::getConnection();
        $sql = "SELECT id FROM users WHERE id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
}