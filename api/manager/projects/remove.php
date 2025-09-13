<?php
// api/manager/projects/remove.php
require_once __DIR__.'/../../../config/Cors.php';
require_once __DIR__.'/../../../utils/Response.php';
require_once __DIR__.'/../../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../../services/ProjectService.php';

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(['success'=>false, 'message'=>'Method not allowed'], 405);
  }

  $user = AuthMiddleware::requireAuth(['manager']);
  $managerId = (int)$user['id'];

  $data = json_decode(file_get_contents('php://input'), true) ?? [];
  $projectId = isset($data['project_id']) ? (int)$data['project_id'] : 0;
  $memberId = isset($data['member_id']) ? (int)$data['member_id'] : 0;
  
  if ($projectId <= 0) { Response::json(['success'=>false, 'message'=>'Invalid project id'], 400); }
  if ($memberId <= 0) { Response::json(['success'=>false, 'message'=>'Invalid member id'], 400); }

  $svc = new ProjectService();
  $res = $svc->removeMember($managerId, $projectId, $memberId);

  if ($res['ok'] ?? false) {
    Response::json(['success'=>true, 'project'=>$res]);
  }
  Response::json(['success'=>false, 'message'=>$res['message'] ?? 'Assign failed'], 400);
} catch (Throwable $e) {
  Response::json(['success'=>false, 'message'=>$e->getMessage()], 500);
}
