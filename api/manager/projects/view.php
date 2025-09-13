<?php
// api/manager/projects/view.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/ProjectService.php';

try {
  $user = AuthMiddleware::requireAuth(['manager']);
  $managerId = (int)$user['id'];

  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($id <= 0) { Response::json(['success'=>false, 'message'=>'Invalid id'], 400); }

  $svc = new ProjectService();
  $project = $svc->getProjectView($managerId, $id);
  if (!$project) { Response::json(['success'=>false, 'message'=>'Project not found'], 404); }

  Response::json(['success'=>true, 'project'=>$project]);
} catch (Throwable $e) {
  error_log('[projects/view] '.$e->getMessage());
  Response::json(['success'=>false, 'message'=>'Server error'], 500);
}
