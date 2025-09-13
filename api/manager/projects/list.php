<?php
// api/manager/projects/list.php
require_once __DIR__ . '/../../../config/Cors.php';
require_once __DIR__ . '/../../../utils/Response.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../services/ProjectService.php';

$user = AuthMiddleware::requireAuth(['manager']);
$managerId = (int)$user['id'];

$svc = new ProjectService();
$rows = $svc->listForManager($managerId);

Response::json(['success'=>true, 'projects'=>$rows]);
