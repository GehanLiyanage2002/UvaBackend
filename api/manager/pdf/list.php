<?php
// api/manager/pdf/list.php
require_once __DIR__ . '/../../../config/Cors.php';
require_once __DIR__ . '/../../../utils/Response.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/Report.php';

try {
    $user = AuthMiddleware::requireAuth(['manager']);
    $managerId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method !== 'GET') {
        Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }
    
    $reportModel = new Report();
    
    // Get reports for projects managed by this manager
    $result = $reportModel->getReportsByManager($managerId);
    
    if (!$result['ok']) {
        Response::json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to fetch reports'
        ], 500);
    }
    
    // Format the reports for frontend
    $reports = array_map(function($report) {
        return [
            'id' => $report['id'],
            'project_id' => $report['project_id'],
            'project_name' => $report['project_title'] ?? "Project #{$report['project_id']}",
            'filename' => basename($report['url']),
            'download_url' => $report['url'],
            'created_at' => $report['created_at']
        ];
    }, $result['reports']);
    
    Response::json([
        'success' => true,
        'reports' => $reports,
        'count' => count($reports)
    ], 200);

} catch (Throwable $e) {
    error_log('[pdf/list] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => 'Server error'], 500);
}