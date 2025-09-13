<?php
// services/TaskService.php
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Project.php';

class TaskService
{
    private Task $task;
    private Project $project;

    public function __construct()
    {
        $this->task = new Task();
        $this->project = new Project();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function createForProject(int $managerId, int $projectId, string $title, string $status = 'todo'): array
    {
        $projectCheck = $this->project->getByIdWithDetails($managerId, $projectId);
        if (!$projectCheck) {
            return ['ok' => false, 'message' => 'Project not found or access denied'];
        }

        return $this->task->create($projectId, $title, $status);
    }

    public function listForProject(int $managerId, int $projectId, ?string $status = null): array
    {
        $projectCheck = $this->project->getByIdWithDetails($managerId, $projectId);
        if (!$projectCheck) {
            return [];
        }

        return $this->task->listByProject($projectId, $status);
    }

    public function listTaskAll(int $managerId): array
    {
        return $this->task->getAllTask($managerId);
    }
    

    

    public function getTaskView(int $managerId, int $projectId, int $taskId): ?array
    {
        $projectCheck = $this->project->getByIdWithDetails($managerId, $projectId);
        if (!$projectCheck) {
            return null;
        }

        return $this->task->getById($taskId, $projectId);
    }

    public function updateTaskDetails(int $managerId, int $projectId, int $taskId, ?string $title = null, ?string $status = null): array
    {
        $projectCheck = $this->project->getByIdWithDetails($managerId, $projectId);
        if (!$projectCheck) {
            return ['ok' => false, 'message' => 'Project not found or access denied'];
        }

        return $this->task->updateTask($taskId, $projectId, $title, $status);
    }

    public function updateTaskDetailsMember(int $memnerId, int $projectId, int $taskId, ?string $title = null, ?string $status = null): array
    {
        return $this->task->updateTask($taskId, $projectId, $title, $status);
    }

    public function updateTaskStatus(int $managerId, int $projectId, int $taskId, string $status): array
    {
        $projectCheck = $this->project->getByIdWithDetails($managerId, $projectId);
        if (!$projectCheck) {
            return ['ok' => false, 'message' => 'Project not found or access denied'];
        }

        return $this->task->updateStatus($taskId, $projectId, $status);
    }

    public function updateTaskStatusMember(int $memberId, int $projectId, int $taskId, string $status): array
    {
        return $this->task->updateStatus($taskId, $projectId, $status);
    }

    public function deleteTask(int $managerId, int $projectId, int $taskId): array
    {
        $projectCheck = $this->project->getByIdWithDetails($managerId, $projectId);
        if (!$projectCheck) {
            return ['ok' => false, 'message' => 'Project not found or access denied'];
        }

        return $this->task->deleteTask($taskId, $projectId);
    }

    public function getTaskCounts(int $managerId, int $projectId): array
    {
        $projectCheck = $this->project->getByIdWithDetails($managerId, $projectId);
        if (!$projectCheck) {
            return [
                'todo_count' => 0,
                'in_progress_count' => 0,
                'done_count' => 0,
                'total_count' => 0
            ];
        }

        return $this->task->getTaskCounts($projectId);
    }

    public function moveTaskToProject(int $managerId, int $fromProjectId, int $toProjectId, int $taskId): array
    {
        $fromProjectCheck = $this->project->getByIdWithDetails($managerId, $fromProjectId);
        $toProjectCheck = $this->project->getByIdWithDetails($managerId, $toProjectId);
        
        if (!$fromProjectCheck || !$toProjectCheck) {
            return ['ok' => false, 'message' => 'One or both projects not found or access denied'];
        }

        return $this->task->moveTask($taskId, $fromProjectId, $toProjectId);
    }

    public function moveTaskToProjectMember(int $memberId, int $fromProjectId, int $toProjectId, int $taskId): array
    {
        return $this->task->moveTask($taskId, $fromProjectId, $toProjectId);
    }


    

    public function getAllTasksByStatus(int $managerId, string $status): array
    {
        $projects = $this->project->listWithCounts($managerId);
        $allTasks = [];

        foreach ($projects as $project) {
            $tasks = $this->task->listByProject($project['id'], $status);
            foreach ($tasks as $task) {
                $task['project_title'] = $project['title'];
                $allTasks[] = $task;
            }
        }

        return $allTasks;
    }

    public function getTasksOverview(int $managerId): array
    {
        $projects = $this->project->listWithCounts($managerId);
        $overview = [
            'total_projects' => count($projects),
            'total_todo' => 0,
            'total_in_progress' => 0,
            'total_done' => 0,
            'total_tasks' => 0
        ];

        foreach ($projects as $project) {
            $overview['total_todo'] += $project['todo_count'];
            $overview['total_in_progress'] += $project['in_progress_count'];
            $overview['total_done'] += $project['done_count'];
        }

        $overview['total_tasks'] = $overview['total_todo'] + $overview['total_in_progress'] + $overview['total_done'];

        return $overview;
    }

    public function getTasksOverviewMember(int $memberId): array{
        $projects = $this->project->listWithCountsMember($memberId);
        $overview = [
            'total_projects' => count($projects),
            'total_todo' => 0,
            'total_in_progress' => 0,
            'total_done' => 0,
            'total_tasks' => 0
        ];

        foreach ($projects as $project) {
            $overview['total_todo'] += $project['todo_count'];
            $overview['total_in_progress'] += $project['in_progress_count'];
            $overview['total_done'] += $project['done_count'];
        }

        $overview['total_tasks'] = $overview['total_todo'] + $overview['total_in_progress'] + $overview['total_done'];

        return $overview;
        
    }

    public function listForMember(int $memberId, int $projectId, ?string $status = null): array
    {
        return $this->task->listByProject($projectId, $status);
    }

    public function listByProjectAssigned(int $memberId, int $projectId): array
    {
        
        return $this->task->listByProjectAssigned($memberId, $projectId);
    }

    public function listByProjectAssignedCoordinater(int $projectId): array
    {
        
        return $this->task->listByProjectAssignedCoordinater($projectId);
    }

    

    public function updateTaskStatusForMember(int $memberId, int $projectId, int $taskId, string $status): array
    {
        return $this->task->updateStatus($taskId, $projectId, $status);
    }

    public function assignUserToTask(int $managerId, int $projectId, int $taskId, int $userId): array
    {
        $projectCheck = $this->project->getByIdWithDetails($managerId, $projectId);
        if (!$projectCheck) {
            return ['ok' => false, 'message' => 'Project not found or access denied'];
        }

        // Verify the task belongs to this project
        $task = $this->task->getById($taskId, $projectId);
        if (!$task) {
            return ['ok' => false, 'message' => 'Task not found in this project'];
        }

        // Check if the user is a member of the project
        $memberCheckSql = "SELECT pm.id FROM project_members pm WHERE pm.project_id = ? AND pm.user_id = ? LIMIT 1";
        $db = Database::getConnection();
        $memberStmt = $db->prepare($memberCheckSql);
        
        if (!$memberStmt) {
            return ['ok' => false, 'message' => 'Database error'];
        }
        
        $memberStmt->bind_param('ii', $projectId, $userId);
        $memberStmt->execute();
        $memberResult = $memberStmt->get_result();
        
        if ($memberResult->num_rows === 0) {
            return ['ok' => false, 'message' => 'User is not a member of this project'];
        }

        return $this->task->assignUserToTask($taskId, $userId, $managerId);
    }

    public function unassignUserFromTask(int $managerId, int $projectId, int $taskId, int $userId): array
    {
        $projectCheck = $this->project->getByIdWithDetails($managerId, $projectId);
        if (!$projectCheck) {
            return ['ok' => false, 'message' => 'Project not found or access denied'];
        }

        // Verify the task belongs to this project
        $task = $this->task->getById($taskId, $projectId);
        if (!$task) {
            return ['ok' => false, 'message' => 'Task not found in this project'];
        }

        return $this->task->unassignUserFromTask($taskId, $userId);
    }

    public function getTaskAssignments(int $managerId, int $projectId, int $taskId): array
    {
        $projectCheck = $this->project->getByIdWithDetails($managerId, $projectId);
        if (!$projectCheck) {
            return [];
        }

        // Verify the task belongs to this project
        $task = $this->task->getById($taskId, $projectId);
        if (!$task) {
            return [];
        }

        return $this->task->getTaskAssignments($taskId);
    }

    public function getProjectMembers(int $managerId, int $projectId): array
    {
        $projectCheck = $this->project->getByIdWithDetails($managerId, $projectId);
        if (!$projectCheck) {
            return [];
        }

        $db = Database::getConnection();
        $sql = "SELECT u.id, u.name, u.email
                FROM project_members pm
                JOIN users u ON pm.user_id = u.id
                WHERE pm.project_id = ?
                ORDER BY u.name";
        
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return [];
        }
        
        $stmt->bind_param('i', $projectId);
        $stmt->execute();
        $result = $stmt->get_result();

        $members = [];
        while ($row = $result->fetch_assoc()) {
            $members[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'email' => $row['email'],
            ];
        }

        return $members;
    }
}