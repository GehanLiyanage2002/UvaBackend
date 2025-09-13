<?php

// api/manager/events.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/EventService.php';

try {
    $user = AuthMiddleware::requireAuth(['manager']);
    $userId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new EventService();
    
    switch ($method) {
        case 'GET':
            handleGetRequests($svc, $userId, $action);
            break;
        case 'POST':
            handlePostRequests($svc, $userId, $action);
            break;
        case 'PUT':
            handlePutRequests($svc, $userId, $action);
            break;
        case 'DELETE':
            handleDeleteRequests($svc, $userId, $action);
            break;
        default:
            Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }

} catch (Throwable $e) {
    error_log('[events] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => 'Server error'], 500);
}

function handleGetRequests($svc, $userId, $action) {
    switch ($action) {
        case 'list':
            // GET /api/manager/events/event.php?action=list&order_by=date&order_direction=ASC
            $orderBy = $_GET['order_by'] ?? 'date';
            $orderDirection = $_GET['order_direction'] ?? 'ASC';
            $events = $svc->getAll($userId, $orderBy, $orderDirection);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'get':
            // GET /api/manager/events/event.php?action=get&id=1
            $eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($eventId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid event ID'], 400);
                return;
            }
            
            $event = $svc->getById($userId, $eventId);
            if (!$event) {
                Response::json(['success' => false, 'message' => 'Event not found'], 404);
                return;
            }
            
            Response::json(['success' => true, 'event' => $event]);
            break;
            
        case 'upcoming':
            // GET /api/manager/events/event.php?action=upcoming&limit=10
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $events = $svc->getUpcoming($userId, $limit);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'today':
            // GET /api/manager/events/event.php?action=today
            $events = $svc->getTodaysEvents($userId);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'this_week':
            // GET /api/manager/events/event.php?action=this_week
            $events = $svc->getThisWeekEvents($userId);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'this_month':
            // GET /api/manager/events/event.php?action=this_month
            $events = $svc->getThisMonthEvents($userId);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'by_month':
            // GET /api/manager/events/event.php?action=by_month&year=2024&month=12
            $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
            $month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
            $events = $svc->getEventsByMonth($userId, $year, $month);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'date_range':
            // GET /api/manager/events/event.php?action=date_range&start_date=2024-12-01&end_date=2024-12-31
            $startDate = $_GET['start_date'] ?? '';
            $endDate = $_GET['end_date'] ?? '';
            
            if (!$startDate || !$endDate) {
                Response::json(['success' => false, 'message' => 'start_date and end_date are required'], 400);
                return;
            }
            
            $events = $svc->getByDateRange($userId, $startDate, $endDate);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'overview':
            // GET /api/manager/events/event.php?action=overview
            $overview = $svc->getEventsOverview($userId);
            Response::json(['success' => true, 'overview' => $overview]);
            break;
            
        case 'search':
            // GET /api/manager/events/event.php?action=search&query=meeting&start_date=2024-12-01&end_date=2024-12-31
            $query = $_GET['query'] ?? '';
            if (!$query) {
                Response::json(['success' => false, 'message' => 'Query parameter is required'], 400);
                return;
            }
            
            $startDate = $_GET['start_date'] ?? null;
            $endDate = $_GET['end_date'] ?? null;
            
            $events = $svc->searchEvents($userId, $query, $startDate, $endDate);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'with_meet_links':
            // GET /api/manager/events/event.php?action=with_meet_links
            $events = $svc->getEventsWithMeetLinks($userId);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for GET request'], 400);
    }
}

function handlePostRequests($svc, $userId, $action) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'create':
            // POST /api/manager/events/event.php?action=create
            // Body: {"title": "Event Title", "date": "2024-12-25", "time": "10:00", "meet_link": "https://...", "description": "..."}
            $title = $input['title'] ?? '';
            $date = $input['date'] ?? '';
            $time = $input['time'] ?? null;
            $meetLink = $input['meet_link'] ?? null;
            $description = $input['description'] ?? null;
            
            if (!$title || !$date) {
                Response::json(['success' => false, 'message' => 'Title and date are required'], 400);
                return;
            }
            
            $result = $svc->create($userId, $title, $date, $time, $meetLink, $description);
            
            if ($result['ok']) {
                Response::json(['success' => true, 'event' => $result['event']], 201);
            } else {
                Response::json(['success' => false, 'message' => $result['message']], 400);
            }
            break;
            
        case 'duplicate':
            // POST /api/manager/events/event.php?action=duplicate
            // Body: {"event_id": 1, "new_date": "2024-12-26"}
            $eventId = isset($input['event_id']) ? (int)$input['event_id'] : 0;
            $newDate = $input['new_date'] ?? '';
            
            if ($eventId <= 0 || !$newDate) {
                Response::json(['success' => false, 'message' => 'Event ID and new date are required'], 400);
                return;
            }
            
            $result = $svc->duplicateEvent($userId, $eventId, $newDate);
            
            if ($result['ok']) {
                Response::json(['success' => true, 'event' => $result['event']], 201);
            } else {
                Response::json(['success' => false, 'message' => $result['message']], 400);
            }
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for POST request'], 400);
    }
}

function handlePutRequests($svc, $userId, $action) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'update':
            // PUT /api/manager/events/event.php?action=update&id=1
            // Body: {"title": "Updated Title", "date": "2024-12-26", "time": "11:00", "meet_link": "https://...", "description": "..."}
            $eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($eventId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid event ID'], 400);
                return;
            }
            
            $title = $input['title'] ?? null;
            $date = $input['date'] ?? null;
            $time = $input['time'] ?? null;
            $meetLink = $input['meet_link'] ?? null;
            $description = $input['description'] ?? null;
            
            $result = $svc->update($userId, $eventId, $title, $date, $time, $meetLink, $description);
            
            if ($result['ok']) {
                Response::json(['success' => true, 'message' => $result['message']]);
            } else {
                Response::json(['success' => false, 'message' => $result['message']], 400);
            }
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for PUT request'], 400);
    }
}

function handleDeleteRequests($svc, $userId, $action) {
    switch ($action) {
        case 'delete':
            // DELETE /api/manager/events/event.php?action=delete&id=1
            $eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($eventId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid event ID'], 400);
                return;
            }
            
            $result = $svc->delete($userId, $eventId);
            
            if ($result['ok']) {
                Response::json(['success' => true, 'message' => $result['message']]);
            } else {
                Response::json(['success' => false, 'message' => $result['message']], 400);
            }
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for DELETE request'], 400);
    }
}