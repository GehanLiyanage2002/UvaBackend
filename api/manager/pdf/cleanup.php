<?php
// api/manager/pdf/cleanup.php - Cleanup old reports (admin/cron endpoint)
require_once __DIR__ . '/../../../config/Cors.php';
require_once __DIR__ . '/../../../utils/Response.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../services/PDFService.php';

try {
    $user = AuthMiddleware::requireAuth(['manager', 'admin']); // Allow admin to run cleanup
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method !== 'POST') {
        Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $daysOld = isset($data['days']) ? (int)$data['days'] : 7;
    
    // Minimum 1 day to prevent accidental deletion of recent files
    if ($daysOld < 1) {
        $daysOld = 1;
    }
    
    $pdfService = new PDFService();
    $result = $pdfService->cleanupOldFiles($daysOld);
    
    if ($result['ok'] ?? false) {
        Response::json([
            'success' => true,
            'message' => $result['message'],
            'deleted_files' => $result['deleted_files'] ?? 0,
            'deleted_records' => $result['deleted_records'] ?? 0
        ], 200);
    }
    
    Response::json([
        'success' => false, 
        'message' => $result['message'] ?? 'Failed to cleanup files'
    ], 400);

} catch (Throwable $e) {
    error_log('[pdf/cleanup] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => 'Server error'], 500);
}