<?php
// api/manager/pdf/delete.php
require_once __DIR__ . '/../../../config/Cors.php';
require_once __DIR__ . '/../../../utils/Response.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../services/PDFService.php';
require_once __DIR__ . '/../../../models/Report.php';

try {
    $user = AuthMiddleware::requireAuth(['manager']);
    $managerId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method !== 'DELETE') {
        Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }
    
    $reportId = isset($_GET['report_id']) ? (int)$_GET['report_id'] : 0;
    
    if ($reportId <= 0) {
        Response::json(['success' => false, 'message' => 'Invalid report id'], 400);
    }
    
    $reportModel = new Report();
    
    // Verify that the report belongs to a project managed by this manager
    $report = $reportModel->getReportWithManagerCheck($reportId, $managerId);
    if (!$report) {
        Response::json(['success' => false, 'message' => 'Report not found or access denied'], 404);
    }
    
    $pdfService = new PDFService();
    
    // Delete the report
    $result = $pdfService->deleteReport($reportId);
    
    if ($result['ok'] ?? false) {
        Response::json([
            'success' => true,
            'message' => $result['message']
        ], 200);
    }
    
    Response::json([
        'success' => false, 
        'message' => $result['message'] ?? 'Failed to delete report'
    ], 400);

} catch (Throwable $e) {
    error_log('[pdf/delete] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => 'Server error'], 500);
}