<?php

// services/NoticeService.php
require_once __DIR__ . '/../models/Notice.php';

class NoticeService
{
    private Notice $notice;

    public function __construct()
    {
        $this->notice = new Notice();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function createNotice(string $title, string $content, string $priority = 'normal', string $status = 'active', ?string $expiresAt = null, bool $isPublic = true, int $createdBy = 0): array
    {
        if (empty(trim($title))) {
            return ['ok' => false, 'message' => 'Title is required'];
        }

        if (empty(trim($content))) {
            return ['ok' => false, 'message' => 'Content is required'];
        }

        if ($createdBy <= 0) {
            return ['ok' => false, 'message' => 'Valid created by ID is required'];
        }

        return $this->notice->create($title, $content, $priority, $status, $expiresAt, $isPublic, $createdBy);
    }

    public function getAllNotices(): array
    {
        $notices = $this->notice->getAll();

        return [
            'notices' => $notices,
        ];
    }

    public function getNoticeById(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        return $this->notice->getById($id);
    }

    public function updateNotice(int $id, ?string $title = null, ?string $content = null, ?string $priority = null, ?string $status = null, ?string $expiresAt = null, ?bool $isPublic = null): array
    {
        if ($id <= 0) {
            return ['ok' => false, 'message' => 'Invalid notice ID'];
        }

        $existingNotice = $this->notice->getById($id);
        if (!$existingNotice) {
            return ['ok' => false, 'message' => 'Notice not found'];
        }

        return $this->notice->update($id, $title, $content, $priority, $status, $expiresAt, $isPublic);
    }

    public function deleteNotice(int $id): array
    {
        if ($id <= 0) {
            return ['ok' => false, 'message' => 'Invalid notice ID'];
        }

        // Check if notice exists
        $existingNotice = $this->notice->getById($id);
        if (!$existingNotice) {
            return ['ok' => false, 'message' => 'Notice not found'];
        }

        return $this->notice->delete($id);
    }
}