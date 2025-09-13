<?php
require_once __DIR__ . '/../config/Database.php';

class Supervisor
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(string $fullName, string $email, string $contact, string $type, string $facultyName, string $departmentName, string $about = ''): array
    {
        // Validate inputs
        $fullName = trim($fullName);
        if ($fullName === '' || mb_strlen($fullName) > 255) {
            return ['ok' => false, 'message' => 'Invalid full name'];
        }

        $email = trim($email);
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
            return ['ok' => false, 'message' => 'Invalid email address'];
        }

        $contact = trim($contact);
        if (mb_strlen($contact) > 20) {
            return ['ok' => false, 'message' => 'Contact number too long'];
        }

        $allowedTypes = ['supervisor', 'co-supervisor'];
        if (!in_array($type, $allowedTypes)) {
            return ['ok' => false, 'message' => 'Invalid type. Must be: supervisor or co-supervisor'];
        }

        $facultyName = trim($facultyName);
        if ($facultyName === '' || mb_strlen($facultyName) > 255) {
            return ['ok' => false, 'message' => 'Invalid faculty name'];
        }

        $departmentName = trim($departmentName);
        if ($departmentName === '' || mb_strlen($departmentName) > 255) {
            return ['ok' => false, 'message' => 'Invalid department name'];
        }

        $about = trim($about);

        // Check if email already exists
        $checkSql = "SELECT id FROM supervisors WHERE email = ? LIMIT 1";
        $checkStmt = $this->db->prepare($checkSql);
        if (!$checkStmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }
        
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            return ['ok' => false, 'message' => 'Email already exists'];
        }

        $sql = "INSERT INTO supervisors (full_name, email, contact, type, faculty_name, department_name, about) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param('sssssss', $fullName, $email, $contact, $type, $facultyName, $departmentName, $about);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        return [
            'ok' => true,
            'supervisor' => [
                'id' => (int)$this->db->insert_id,
                'full_name' => $fullName,
                'email' => $email,
                'contact' => $contact,
                'type' => $type,
                'faculty_name' => $facultyName,
                'department_name' => $departmentName,
                'about' => $about,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    public function getAll(?string $type = null, ?string $facultyName = null, ?string $departmentName = null): array
    {
        $sql = "SELECT id, full_name, email, contact, type, faculty_name, department_name, about, created_at, updated_at 
                FROM supervisors WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($type !== null) {
            $allowedTypes = ['supervisor', 'co-supervisor'];
            if (!in_array($type, $allowedTypes)) {
                return [];
            }
            $sql .= " AND type = ?";
            $params[] = $type;
            $types .= 's';
        }
        
        if ($facultyName !== null) {
            $sql .= " AND faculty_name = ?";
            $params[] = $facultyName;
            $types .= 's';
        }
        
        if ($departmentName !== null) {
            $sql .= " AND department_name = ?";
            $params[] = $departmentName;
            $types .= 's';
        }
        
        $sql .= " ORDER BY full_name ASC, created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('getAll prepare: ' . $this->db->error);
            return [];
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $res = $stmt->get_result();

        $supervisors = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $supervisors[] = $row;
        }
        return $supervisors;
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT id, full_name, email, contact, type, faculty_name, department_name, about, created_at, updated_at
                FROM supervisors
                WHERE id = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $supervisor = $res->fetch_assoc();
        
        if (!$supervisor) return null;

        return [
            'id' => (int)$supervisor['id'],
            'full_name' => $supervisor['full_name'],
            'email' => $supervisor['email'],
            'contact' => $supervisor['contact'],
            'type' => $supervisor['type'],
            'faculty_name' => $supervisor['faculty_name'],
            'department_name' => $supervisor['department_name'],
            'about' => $supervisor['about'],
            'created_at' => $supervisor['created_at'],
            'updated_at' => $supervisor['updated_at']
        ];
    }

    public function update(int $id, ?string $fullName = null, ?string $email = null, ?string $contact = null, ?string $type = null, ?string $facultyName = null, ?string $departmentName = null, ?string $about = null): array
    {
        $fields = [];
        $params = [];
        $types = '';

        // Full Name
        if ($fullName !== null) {
            $fullName = trim($fullName);
            if ($fullName === '' || mb_strlen($fullName) > 255) {
                return ['ok' => false, 'message' => 'Invalid full name'];
            }
            $fields[] = "full_name = ?";
            $params[] = $fullName;
            $types .= 's';
        }

        // Email
        if ($email !== null) {
            $email = trim($email);
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
                return ['ok' => false, 'message' => 'Invalid email address'];
            }
            
            // Check if email already exists for another supervisor
            $checkSql = "SELECT id FROM supervisors WHERE email = ? AND id != ? LIMIT 1";
            $checkStmt = $this->db->prepare($checkSql);
            if (!$checkStmt) {
                return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
            }
            
            $checkStmt->bind_param('si', $email, $id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                return ['ok' => false, 'message' => 'Email already exists'];
            }
            
            $fields[] = "email = ?";
            $params[] = $email;
            $types .= 's';
        }

        // Contact
        if ($contact !== null) {
            $contact = trim($contact);
            if (mb_strlen($contact) > 20) {
                return ['ok' => false, 'message' => 'Contact number too long'];
            }
            $fields[] = "contact = ?";
            $params[] = $contact;
            $types .= 's';
        }

        // Type
        if ($type !== null) {
            $allowedTypes = ['supervisor', 'co-supervisor'];
            if (!in_array($type, $allowedTypes)) {
                return ['ok' => false, 'message' => 'Invalid type. Must be: supervisor or co-supervisor'];
            }
            $fields[] = "type = ?";
            $params[] = $type;
            $types .= 's';
        }

        // Faculty Name
        if ($facultyName !== null) {
            $facultyName = trim($facultyName);
            if ($facultyName === '' || mb_strlen($facultyName) > 255) {
                return ['ok' => false, 'message' => 'Invalid faculty name'];
            }
            $fields[] = "faculty_name = ?";
            $params[] = $facultyName;
            $types .= 's';
        }

        // Department Name
        if ($departmentName !== null) {
            $departmentName = trim($departmentName);
            if ($departmentName === '' || mb_strlen($departmentName) > 255) {
                return ['ok' => false, 'message' => 'Invalid department name'];
            }
            $fields[] = "department_name = ?";
            $params[] = $departmentName;
            $types .= 's';
        }

        // About
        if ($about !== null) {
            $about = trim($about);
            $fields[] = "about = ?";
            $params[] = $about;
            $types .= 's';
        }

        // No fields to update
        if (empty($fields)) {
            return ['ok' => false, 'message' => 'Nothing to update'];
        }

        // Add updated_at timestamp
        $fields[] = "updated_at = CURRENT_TIMESTAMP";

        // SQL query
        $sql = "UPDATE supervisors SET " . implode(', ', $fields) . " WHERE id = ?";
        $params[] = $id;
        $types .= 'i';

        // Prepare and execute
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'message' => 'Supervisor not found or no changes made'];
        }

        return ['ok' => true, 'message' => 'Supervisor updated successfully'];
    }

    public function delete(int $id): array
    {
        $sql = "DELETE FROM supervisors WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        if ($affectedRows === 0) {
            return ['ok' => false, 'message' => 'Supervisor not found'];
        }

        return ['ok' => true, 'message' => 'Supervisor deleted successfully'];
    }
}