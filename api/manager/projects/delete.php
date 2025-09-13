<?php
// api/manager/projects/delete.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/ProjectService.php';

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::json(['success'=>false, 'message'=>'Method not allowed here'], 405);
  }

  $user = AuthMiddleware::requireAuth(['manager']);
  $managerId = (int)$user['id'];

  $data = json_decode(file_get_contents('php://input'), true) ?? [];
  $projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  $title       = array_key_exists('title', $data) ? $data['title'] : null;
  $description = array_key_exists('description', $data) ? $data['description'] : null;

  if ($projectId <= 0) { Response::json(['success'=>false, 'message'=>'Invalid project id'], 400); }

  $svc = new ProjectService();
  $res = $svc->deleteProject($managerId, $projectId);

  if ($res['ok'] ?? false) {
    Response::json(['success'=>true]);
  }
  Response::json(['success'=>false, 'message'=>$res['message'] ?? 'Update failed'], 400);
} catch (Throwable $e) {
  error_log('[projects/update] '.$e->getMessage());
  Response::json(['success'=>false, 'message'=>$e->getMessage()], 500);
}
