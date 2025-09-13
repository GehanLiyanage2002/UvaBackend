<?php
require_once __DIR__.'/../../config/Cors.php';
require_once __DIR__.'/../../middleware/AuthMiddleware.php';
require_once __DIR__.'/../../utils/Response.php';
require_once __DIR__.'/../../config/Database.php';

$user = AuthMiddleware::requireAuth(['manager']); // only managers
$managerId = (int)$user['id'];
$db = Database::getConnection();

/** total projects owned by manager */
$stmt = $db->prepare("SELECT COUNT(*) AS c FROM projects WHERE manager_id = ?");
$stmt->bind_param('i', $managerId);
$stmt->execute();
$totalProjects = (int)$stmt->get_result()->fetch_assoc()['c'];

/** total tasks under manager’s projects */
$stmt = $db->prepare("
  SELECT COUNT(t.id) AS c
  FROM tasks t
  JOIN projects p ON p.id = t.project_id
  WHERE p.manager_id = ?
");
$stmt->bind_param('i', $managerId);
$stmt->execute();
$totalTasks = (int)$stmt->get_result()->fetch_assoc()['c'];

/** total distinct members assigned under manager’s projects */
// $stmt = $db->prepare("
//   SELECT COUNT(DISTINCT pm.user_id) AS c
//   FROM project_members pm
//   JOIN projects p ON p.id = pm.project_id
//   WHERE p.manager_id = ?
// ");
// $stmt->bind_param('i', $managerId);
// $stmt->execute();
// $totalMembers = (int)$stmt->get_result()->fetch_assoc()['c'];

/** project list for selector */
// $stmt = $db->prepare("SELECT id, name FROM projects WHERE manager_id = ? ORDER BY created_at DESC");
// $stmt->bind_param('i', $managerId);
// $stmt->execute();
// $projects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

Response::json([
  'success' => true,
  'data' => [
    // 'totalProjects' => $totalProjects,
    // 'totalTasks'    => $totalTasks,
    // 'totalMembers'  => $totalMembers,
    // 'projects'      => $projects
  ]
]);