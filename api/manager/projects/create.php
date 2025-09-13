<?php
// api/manager/projects/create.php
require_once __DIR__ . '/../../../config/Cors.php';
require_once __DIR__ . '/../../../utils/Response.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../services/ProjectService.php';

$user = AuthMiddleware::requireAuth(['manager']); // must be manager
$managerId = (int)$user['id'];                    // ignore client-sent manager_id for security

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$title = trim($input['title'] ?? '');
$description = isset($input['description']) ? trim($input['description']) : null;
$start_date = isset($input['start_date']) ? trim($input['start_date']) : null;
$end_date = isset($input['end_date']) ? trim($input['end_date']) : null;

$svc = new ProjectService();
$res = $svc->createForManager($managerId, $title, $description,$start_date,$end_date);

if ($res['ok'] ?? false) {
    Response::json(['success'=>true, 'project'=>$res['project']]);
}
Response::json(['success'=>false, 'message'=>$res['message'] ?? 'Failed to create project'], 400);
