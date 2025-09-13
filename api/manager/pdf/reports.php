<?php
// api/manager/pdf/reports.php - Get reports for a project
require_once __DIR__ . '/../../../config/Cors.php';
require_once __DIR__ . '/../../../utils/Response.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../services/PDFService.php';

try {
    $user = AuthMiddleware::requireAuth(['manager']);
    $managerId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method !== 'GET') {
        Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }
    
    $projectId = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
    
    if ($projectId <= 0) {
        Response::json(['success' => false, 'message' => 'Invalid project id'], 400);
    }
    
    $pdfService = new PDFService();
    $result = $pdfService->getProjectReports($projectId);
    
    if ($result['ok'] ?? false) {
        Response::json([
            'success' => true,
            'reports' => $result['reports'],
            'count' => count($result['reports'])
        ], 200);
    }
    
    Response::json([
        'success' => false, 
        'message' => $result['message'] ?? 'Failed to fetch reports'
    ], 400);

} catch (Throwable $e) {
    error_log('[pdf/reports] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => 'Server error'], 500);
}

