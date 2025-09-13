<?php
// api/manager/projects/update.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/ProjectService.php';

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(['success'=>false, 'message'=>'Method not allowed'], 405);
  }

  $user = AuthMiddleware::requireAuth(['manager','member']);
  $managerId = (int)$user['id'];

  $data = json_decode(file_get_contents('php://input'), true) ?? [];
  $projectId = isset($data['id']) ? (int)$data['id'] : 0;
  $title       = array_key_exists('title', $data) ? $data['title'] : null;
  $description = array_key_exists('description', $data) ? $data['description'] : null;
  $start_date = array_key_exists('start_date', $data) ? $data['start_date'] : null;
  $end_date = array_key_exists('end_date', $data) ? $data['end_date'] : null;
  
  if ($projectId <= 0) { Response::json(['success'=>false, 'message'=>'Invalid project id'], 400); }

  $svc = new ProjectService();
  $res = $svc->updateProjectDetails($managerId, $projectId, $title, $description, $start_date, $end_date);

  if ($res['ok'] ?? false) {
    $project = $svc->getProjectView($managerId, $projectId);
    Response::json(['success'=>true, 'project'=>$project]);
  }
  Response::json(['success'=>false, 'message'=>$res['message'] ?? 'Update failed'], 400);
} catch (Throwable $e) {
  error_log('[projects/update] '.$e->getMessage());
  Response::json(['success'=>false, 'message'=>$e->getMessage()], 500);
}
