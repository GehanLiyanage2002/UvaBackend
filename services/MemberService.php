<?php

// services/MemberService.php
require_once __DIR__ . '/../models/User.php';

class MemberService
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getAllMembers(): array
    {
        $members = $this->user->getAllMembers();

        return [
            'members' => $members,
        ];
    }

   
}