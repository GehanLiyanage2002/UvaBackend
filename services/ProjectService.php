<?php
// services/ProjectService.php
require_once __DIR__ . '/../models/Project.php';

class ProjectService
{
    private Project $project;

    public function __construct()
    {
        $this->project = new Project();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function createForManager(int $managerId, string $title, ?string $desc = null,$start_date = null,$end_date = null): array
    {
        return $this->project->create($managerId, $title, $desc, $start_date, $end_date);
    }
    public function listForManager(int $managerId): array
    {
        return $this->project->listWithCounts($managerId);
    }

    public function listForMember(int $memberId): array
    {
        return $this->project->listForMember($memberId);
    }

    public function listForCoordinator(): array
    {
        return $this->project->listForCoordinator();
    }
    
    
    public function getProjectView(int $managerId, int $projectId): ?array
    {
        return $this->project->getByIdWithDetails($managerId, $projectId);
    }

    public function getByIdWithDetailsByMember(int $memberId, int $projectId): ?array
    {
        return $this->project->getByIdWithDetailsByMember($memberId, $projectId);
    }

    public function getByIdWithDetailsByCoordinator(int $projectId): ?array
    {
        return $this->project->getByIdWithDetailsByCoordinator($projectId);
    }

    
    public function updateProjectDetails(int $managerId, int $projectId, ?string $title, ?string $description, ?string $start_date, ?string $end_date): array
    {
        return $this->project->updateDetails($managerId, $projectId, $title, $description, $start_date, $end_date);
    }

    public function deleteProject(int $managerId, int $projectId): array
    {
        return $this->project->deleteProject($managerId, $projectId);
    }

    public function assignMember(int $managerId, int $projectId, int $memberId): array
    {
        return $this->project->assignMember($managerId, $projectId, $memberId);
    }

    public function removeMember(int $managerId, int $projectId, int $memberId): array
    {
        return $this->project->removeMember($managerId, $projectId, $memberId);
    }

    
}