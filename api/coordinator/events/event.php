<?php
// api/coordinator/events/event.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/EventService.php';


try {
    $user = AuthMiddleware::requireAuth(['coordinator']);
    $coordinatorId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new EventService();
    
    switch ($method) {
        case 'GET':
            handleGetRequests($svc, $coordinatorId, $action);
            break;
        default:
            Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }

} catch (Throwable $e) {
    error_log('[projects] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => 'Server error'], 500);
}

function handleGetRequests($svc, $coordinatorId, $action) {
    switch ($action) {
        case 'list':
            // GET /api/coordinator/events/event.php?action=list&order_by=date&order_direction=ASC
            $orderBy = $_GET['order_by'] ?? 'date';
            $orderDirection = $_GET['order_direction'] ?? 'ASC';
            $events = $svc->getAll($coordinatorId, $orderBy, $orderDirection);
            Response::json(['success' => true, 'events' => $events]);
            break;

        case 'get':
            // GET /api/coordinator/events/event.php?action=get&id=1
            $eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($eventId <= 0) {
                Response::json(['success' => false, 'message' => 'Invalid event ID'], 400);
                return;
            }
            $event = $svc->getById($coordinatorId, $eventId);
            if (!$event) {
                Response::json(['success' => false, 'message' => 'Event not found'], 404);
                return;
            }
            Response::json(['success' => true, 'event' => $event]);
            break;
            
        case 'upcoming':
            // GET /api/coordinator/events/event.php?action=upcoming&limit=10
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $events = $svc->getUpcoming($coordinatorId, $limit);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'today':
            // GET /api/coordinator/events/event.php?action=today
            $events = $svc->getTodaysEvents($coordinatorId);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'this_week':
            // GET /api/coordinator/events/event.php?action=this_week
            $events = $svc->getThisWeekEvents($coordinatorId);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'this_month':
            // GET /api/coordinator/events/event.php?action=this_month
            $events = $svc->getThisMonthEvents($coordinatorId);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'by_month':
            // GET /api/coordinator/events/event.php?action=by_month&year=2024&month=12
            $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
            $month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
            $events = $svc->getEventsByMonth($coordinatorId, $year, $month);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'date_range':
            // GET /api/coordinator/events/event.php?action=date_range&start_date=2024-12-01&end_date=2024-12-31
            $startDate = $_GET['start_date'] ?? '';
            $endDate = $_GET['end_date'] ?? '';
            
            if (!$startDate || !$endDate) {
                Response::json(['success' => false, 'message' => 'start_date and end_date are required'], 400);
                return;
            }
            
            $events = $svc->getByDateRange($coordinatorId, $startDate, $endDate);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'overview':
            // GET /api/coordinator/events/event.php?action=overview
            $overview = $svc->getEventsOverview($coordinatorId);
            Response::json(['success' => true, 'overview' => $overview]);
            break;
            
        case 'search':
            // GET /api/coordinator/events/event.php?action=search&query=meeting&start_date=2024-12-01&end_date=2024-12-31
            $query = $_GET['query'] ?? '';
            if (!$query) {
                Response::json(['success' => false, 'message' => 'Query parameter is required'], 400);
                return;
            }
            
            $startDate = $_GET['start_date'] ?? null;
            $endDate = $_GET['end_date'] ?? null;
            
            $events = $svc->searchEvents($coordinatorId, $query, $startDate, $endDate);
            Response::json(['success' => true, 'events' => $events]);
            break;
            
        case 'with_meet_links':
            // GET /api/coordinator/events/event.php?action=with_meet_links
            $events = $svc->getEventsWithMeetLinks($coordinatorId);
            Response::json(['success' => true, 'events' => $events]);
            break;
        
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for GET request'], 400);
    }
}

