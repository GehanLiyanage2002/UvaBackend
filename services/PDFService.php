<?php
// services/PDFService.php
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Report.php';

class PDFService
{
    private Project $projectModel;
    private Task $taskModel;
    private User $userModel;
    private Report $reportModel;

    public function __construct()
    {
        $this->projectModel = new Project();
        $this->taskModel = new Task();
        $this->userModel = new User();
        $this->reportModel = new Report();
    }

    public function generateProjectPDF(int $managerId, int $projectId): array
    {
        // Get project details
        $project = $this->projectModel->getByIdWithDetails($managerId, $projectId);
        if (!$project) {
            return ['ok' => false, 'message' => 'Project not found or access denied'];
        }

        // Get manager details
        $manager = $this->userModel->getById($managerId);
        if (!$manager) {
            return ['ok' => false, 'message' => 'Manager not found'];
        }

        // Get all tasks for the project
        $tasks = $this->taskModel->listByProject($projectId);

        // Get task assignments for each task
        $tasksWithAssignments = [];
        foreach ($tasks as $task) {
            $assignments = $this->taskModel->getTaskAssignments($task['id']);
            $task['assignments'] = $assignments;
            $tasksWithAssignments[] = $task;
        }

        // Generate PDF content
        $pdfContent = $this->generatePDFContent($project, $manager, $tasksWithAssignments);
        
        // Create PDF file
        $filename = $this->createPDFFile($pdfContent, $project['title'], $projectId);
        
        if (!$filename) {
            return ['ok' => false, 'message' => 'Failed to generate PDF'];
        }

        // Generate download URL
        $downloadUrl = $this->generateDownloadUrl($filename);

        // Store report record in database
        $reportResult = $this->reportModel->create($projectId, $downloadUrl);
        
        if (!$reportResult['ok']) {
            // Log the error but don't fail the PDF generation
            error_log("Failed to store report record: " . $reportResult['message']);
        }

        return [
            'ok' => true,
            'filename' => $filename,
            'download_url' => $downloadUrl,
            'report_id' => $reportResult['ok'] ? $reportResult['id'] : null,
            'message' => 'PDF generated successfully'
        ];
    }

    public function getProjectReports(int $projectId): array
    {
        return $this->reportModel->getByProjectId($projectId);
    }

    public function getReportById(int $reportId): ?array
    {
        return $this->reportModel->getById($reportId);
    }

    public function deleteReport(int $reportId): array
    {
        // Get report details first
        $report = $this->reportModel->getById($reportId);
        if (!$report) {
            return ['ok' => false, 'message' => 'Report not found'];
        }

        // Delete the physical file
        $filename = basename($report['url']);
        $filepath = __DIR__ . '/../uploads/pdf/' . $filename;
        
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Delete the database record
        return $this->reportModel->delete($reportId);
    }

    private function generatePDFContent(array $project, array $manager, array $tasks): string
    {
        $html = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Project Report - ' . htmlspecialchars($project['title']) . '</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 40px;
                        line-height: 1.5;
                        color: #333;
                        max-width: 800px;
                    }
                    
                    .header {
                        text-align: center;
                        margin-bottom: 40px;
                        border-bottom: 1px solid #ccc;
                        padding-bottom: 20px;
                    }
                    
                    .header h1 {
                        color: #333;
                        margin: 0 0 10px 0;
                        font-size: 24px;
                        font-weight: normal;
                    }
                    
                    .header .subtitle {
                        color: #666;
                        font-size: 14px;
                    }
                    
                    .section {
                        margin-bottom: 30px;
                    }
                    
                    .section-title {
                        font-size: 16px;
                        font-weight: bold;
                        margin-bottom: 15px;
                        color: #333;
                        border-bottom: 1px solid #eee;
                        padding-bottom: 5px;
                    }
                    
                    .info-row {
                        margin-bottom: 8px;
                        display: flex;
                    }
                    
                    .info-label {
                        font-weight: bold;
                        width: 120px;
                        flex-shrink: 0;
                    }
                    
                    .info-value {
                        color: #555;
                    }
                    
                    .stat-item {
                        text-align: center;
                    }
                    
                    .stat-number {
                        font-size: 20px;
                        font-weight: bold;
                        color: #333;
                    }
                    
                    .stat-label {
                        font-size: 12px;
                        color: #666;
                        text-transform: uppercase;
                    }
                    
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 20px 0;
                    }
                    
                    th, td {
                        padding: 8px 12px;
                        text-align: left;
                        border-bottom: 1px solid #ddd;
                    }
                    
                    th {
                        background-color: #f9f9f9;
                        font-weight: bold;
                        color: #333;
                        font-size: 14px;
                    }
                    
                    td {
                        font-size: 14px;
                    }
                    
                    .status {
                        padding: 2px 6px;
                        border-radius: 3px;
                        font-size: 12px;
                        font-weight: bold;
                    }
                    
                    .status-todo { background: #f0f0f0; color: #666; }
                    .status-in_progress { background: #fff3cd; color: #856404; }
                    .status-done { background: #d4edda; color: #155724; }
                    
                    .member-item {
                        margin-bottom: 5px;
                        font-size: 14px;
                    }
                    
                    .member-name {
                        font-weight: bold;
                    }
                    
                    .member-email {
                        color: #666;
                        font-size: 12px;
                    }
                    
                    .footer {
                        margin-top: 40px;
                        padding-top: 20px;
                        border-top: 1px solid #ccc;
                        text-align: center;
                        color: #666;
                        font-size: 12px;
                    }
                    
                    .no-data {
                        color: #999;
                        font-style: italic;
                        margin: 10px 0;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>' . htmlspecialchars($project['title']) . '</h1>
                    <div class="subtitle">Project Report</div>
                </div>

                <div class="section">
                    <div class="section-title">Project Information</div>
                    <div class="info-row">
                        <span class="info-label">Project ID:</span>
                        <span class="info-value">' . $project['id'] . '</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Manager:</span>
                        <span class="info-value">' . htmlspecialchars($manager['name']) . '</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Start Date:</span>
                        <span class="info-value">' . ($project['start_date'] ?: 'Not specified') . '</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">End Date:</span>
                        <span class="info-value">' . ($project['end_date'] ?: 'Not specified') . '</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Created:</span>
                        <span class="info-value">' . date('F j, Y', strtotime($project['created_at'])) . '</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Description:</span>
                        <span class="info-value">' . (trim($project['description']) ? htmlspecialchars($project['description']) : 'No description provided') . '</span>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Task Summary</div>
                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-number">' . $project['counts']['todo_count'] . '</div>
                            <div class="stat-label">To Do</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">' . $project['counts']['in_progress_count'] . '</div>
                            <div class="stat-label">In Progress</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">' . $project['counts']['testing_count'] . '</div>
                            <div class="stat-label">Testing</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">' . $project['counts']['done_count'] . '</div>
                            <div class="stat-label">Done</div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Project Members (' . count($project['members']) . ')</div>';
                    
                    if (!empty($project['members'])) {
                        foreach ($project['members'] as $member) {
                            $html .= '
                            <div class="member-item">
                                <span class="member-name">' . htmlspecialchars($member['name']) . '</span>
                                <span class="member-email"> - ' . htmlspecialchars($member['email']) . '</span>
                            </div>';
                        }
                    } else {
                        $html .= '<div class="no-data">No members assigned to this project</div>';
                    }
                    
                    $html .= '</div>';

                    if (!empty($project['supervisors'])) {
                        $html .= '
                    <div class="section">
                        <div class="section-title">Project Supervisors (' . count($project['supervisors']) . ')</div>';
                        
                        foreach ($project['supervisors'] as $supervisor) {
                            $html .= '
                            <div class="member-item">
                                <span class="member-name">' . htmlspecialchars($supervisor['full_name']) . '</span>
                                <span class="member-email"> - ' . htmlspecialchars($supervisor['email']) . '</span>
                            </div>';
                        }
                        
                        $html .= '</div>';
                    }

                    $html .= '
                <div class="section">
                    <div class="section-title">Tasks (' . count($tasks) . ')</div>';
                    
                    if (!empty($tasks)) {
                        $html .= '
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>';
                        
                        foreach ($tasks as $task) {
                            $statusClass = 'status-' . str_replace(' ', '_', strtolower($task['status']));
                            $assignedMembers = !empty($task['assignments']) 
                                ? implode(', ', array_column($task['assignments'], 'user_name'))
                                : 'Unassigned';
                            
                            $html .= '
                            <tr>
                                <td>' . $task['id'] . '</td>
                                <td>' . htmlspecialchars($task['title']) . '</td>
                                <td><span class="status ' . $statusClass . '">' . ucwords(str_replace('_', ' ', $task['status'])) . '</span></td>
                                <td>' . htmlspecialchars($assignedMembers) . '</td>
                                <td>' . date('M j, Y', strtotime($task['created_at'])) . '</td>
                            </tr>';
                        }
                        
                        $html .= '</tbody></table>';
                    } else {
                        $html .= '<div class="no-data">No tasks found for this project</div>';
                    }
                    
                    $html .= '</div>

                <div class="footer">
                    <p>Report generated on ' . date('F j, Y') . '</p>
                </div>

            </body>
            </html>
        ';

        return $html;
    }

    private function createPDFFile(string $htmlContent, string $projectTitle, int $projectId): ?string
    {
        require_once __DIR__ . '/../libraries/dompdf/autoload.inc.php';
        
        $uploadsDir = __DIR__ . '/../uploads/pdf/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $sanitizedTitle = preg_replace('/[^A-Za-z0-9\-_]/', '_', $projectTitle);
        $filename = "project_{$projectId}_{$sanitizedTitle}_{$timestamp}.pdf";
        $filepath = $uploadsDir . $filename;

        try {
            $options = new \Dompdf\Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isFontSubsettingEnabled', true);
            
            $dompdf = new \Dompdf\Dompdf($options);
            
            $dompdf->loadHtml($htmlContent);
            
            $dompdf->setPaper('A4', 'portrait');
            
            $dompdf->render();
            
            $pdfContent = $dompdf->output();
            
            if (file_put_contents($filepath, $pdfContent) === false) {
                return null;
            }
            
            return $filename;
            
        } catch (Exception $e) {
            error_log("PDF generation failed: " . $e->getMessage());
            return null;
        }
    }

    private function servePDFFile(string $filename): void
    {
        $filepath = __DIR__ . '/../uploads/pdf/' . $filename;
        
        if (!file_exists($filepath)) {
            http_response_code(404);
            echo "File not found";
            return;
        }
        
        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Output the file
        readfile($filepath);
        exit;
    }

    private function generateDownloadUrl(string $filename): string
    {
        $baseUrl = $this->getBaseUrl();
        return $baseUrl . '/uwu_pms_backend-main/uploads/pdf/' . urlencode($filename);
    }

    private function getBaseUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host;
    }

    public function cleanupOldFiles(int $daysOld = 7): array
    {
        // Clean up database records first
        $dbCleanupResult = $this->reportModel->deleteOldReports($daysOld);
        
        // Clean up physical files
        $uploadsDir = __DIR__ . '/../uploads/pdf/';
        if (!is_dir($uploadsDir)) {
            return $dbCleanupResult;
        }

        $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
        $files = glob($uploadsDir . '*');
        $deletedFiles = 0;

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoffTime) {
                if (unlink($file)) {
                    $deletedFiles++;
                }
            }
        }

        return [
            'ok' => true,
            'message' => "Cleaned up {$deletedFiles} old files and {$dbCleanupResult['deleted_count']} database records",
            'deleted_files' => $deletedFiles,
            'deleted_records' => $dbCleanupResult['deleted_count'] ?? 0
        ];
    }
}