<?php
// api/manager/pdf/generate.php
require_once __DIR__ . '/../../../config/Cors.php';
require_once __DIR__ . '/../../../utils/Response.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../services/PDFService.php';

try {
    $user = AuthMiddleware::requireAuth(['manager']);
    $managerId = (int)$user['id'];
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method !== 'POST') {
        Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }
    
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $projectId = isset($data['project_id']) ? (int)$data['project_id'] : 0;
    
    if ($projectId <= 0) {
        Response::json(['success' => false, 'message' => 'Invalid project id'], 400);
    }
    
    $pdfService = new PDFService();
    $result = $pdfService->generateProjectPDF($managerId, $projectId);
    
    if ($result['ok'] ?? false) {
        Response::json([
            'success' => true,
            'message' => $result['message'],
            'filename' => $result['filename'],
            'download_url' => $result['download_url']
        ], 201);
    }
    
    Response::json([
        'success' => false, 
        'message' => $result['message'] ?? 'Failed to generate PDF'
    ], 400);

} catch (Throwable $e) {
    error_log('[pdf/generate] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => $e->getMessage()], 500);
}