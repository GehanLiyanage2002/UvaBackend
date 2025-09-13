<?php
// models/User.php
require_once __DIR__.'/../config/Database.php';

class User {
    private mysqli $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT id, name, email, password_hash, role, profile_image, created_at FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return $row ?: null;
    }

    public function create(string $name, string $email, string $password, string $role): array {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users(name,email,password_hash,role) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $name, $email, $hash, $role);
        if (!$stmt->execute()) {
            if ($this->db->errno === 1062) {
                return ['ok'=>false, 'message'=>'Email already registered'];
            }
            return ['ok'=>false, 'message'=>'DB error'];
        }
        return [
            'ok'=>true,
            'user'=>[
                'id'=>$this->db->insert_id,
                'name'=>$name,
                'email'=>$email,
                'role'=>$role
            ]
        ];
    }
    
    public function getById(int $id): ?array
    {
        $sql = "SELECT id, name, email, password_hash, role, academic_year, bio, profile_image, created_at, updated_at
                FROM users
                WHERE id = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;
        
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        
        if (!$user) return null;

        return [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'password_hash' => $user['password_hash'],
            'role' => $user['role'],
            'academic_year' => $user['academic_year'],
            'bio' => $user['bio'],
            'profile_image' => $user['profile_image'],
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at']
        ];
    }
    
    public function update(int $id, ?string $name = null, ?string $email = null, ?string $password = null, string $academic_year = null, string $bio = null): array
    {
        $fields = [];
        $params = [];
        $types = '';

        // Name
        if ($name !== null) {
            $name = trim($name);
            if ($name === '' || mb_strlen($name) > 255) {
                return ['ok' => false, 'message' => 'Invalid name'];
            }
            $fields[] = "name = ?";
            $params[] = $name;
            $types .= 's';
        }

        // Email
        if ($email !== null) {
            $email = trim($email);
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
                return ['ok' => false, 'message' => 'Invalid email address'];
            }
            
            // Check if email already exists for another user
            $checkSql = "SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1";
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

        // Password
        if ($password !== null) {
            $password = trim($password);
            if (mb_strlen($password) < 6) {
                return ['ok' => false, 'message' => 'Password must be at least 6 characters long'];
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $fields[] = "password_hash = ?";
            $params[] = $hash;
            $types .= 's';
        }

        if ($academic_year !== null) {
            $academic_year = trim($academic_year);
            if ($academic_year === '' || mb_strlen($academic_year) > 255) {
                return ['ok' => false, 'message' => 'Invalid Academic Year'];
            }
            $fields[] = "academic_year = ?";
            $params[] = $academic_year;
            $types .= 's';
        }

        if ($bio !== null) {
            $bio = trim($bio);
            if ($bio === '' || mb_strlen($bio) > 255) {
                return ['ok' => false, 'message' => 'Invalid Bio'];
            }
            $fields[] = "bio = ?";
            $params[] = $bio;
            $types .= 's';
        }

        // No fields to update
        if (empty($fields)) {
            return ['ok' => false, 'message' => 'Nothing to update'];
        }

        // SQL query
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $params[] = $id;
        $types .= 'i';

        // Prepare and execute
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->db->error];
        }

        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            if ($this->db->errno === 1062) {
                return ['ok' => false, 'message' => 'Email already exists'];
            }
            return ['ok' => false, 'message' => 'SQL execute failed: ' . $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'message' => 'User not found or no changes made'];
        }

        return ['ok' => true, 'message' => 'User updated successfully'];
    }

    public function updateProfileImage($userId, $file)
    {
        $uploadDir = __DIR__ . '/../uploads/profile_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('profile_', true) . '.' . $ext;
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Full URL
            $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/uwu_pms_backend-main';
            $imageUrl = $baseUrl . '/uploads/profile_images/' . $fileName;

            // Store full URL in DB (column: profile_image)
            $stmt = $this->db->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->bind_param('si', $imageUrl, $userId);
            $stmt->execute();

            return [
                'ok' => true,
                'profile_image' => $imageUrl
            ];
        }

        return [
            'ok' => false,
            'message' => 'Failed to upload image'
        ];
    }


    public function getAllMembers(): array
    {
        $sql = "SELECT * FROM users WHERE role = 'member'";
        
        $params = [];
        $types = '';
        
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

        $members = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $members[] = $row;
        }
        return $members;
    }

    public function updateProfileImageUrl(int $userId, string $imageUrl): array {
        $stmt = $this->db->prepare("UPDATE users SET profile_image=? WHERE id=?");
        if (!$stmt) return ['ok'=>false,'message'=>$this->db->error];
        $stmt->bind_param('si', $imageUrl, $userId);
        if (!$stmt->execute()) return ['ok'=>false,'message'=>$stmt->error];
        return ['ok'=>true];
    }
}