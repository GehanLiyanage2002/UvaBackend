<?php
// api/profile.php
require_once __DIR__.'/../config/Cors.php';
require_once __DIR__.'/../utils/Response.php';
require_once __DIR__.'/../middleware/AuthMiddleware.php';
require_once __DIR__.'/../services/ProfileService.php';

try {
    $user = AuthMiddleware::requireAuth(['admin', 'coordinator', 'manager', 'user','member']);
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    $svc = new ProfileService();
    
    switch ($method) {
        case 'GET':
            handleGetRequests($svc, $action, $user);
            break;
            
        case 'POST':
            handlePostRequests($svc, $action, $user);
            break;
            
        case 'PUT':
            handlePutRequests($svc, $action, $user);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Method not allowed'], 405);
    }

} catch (Throwable $e) {
    error_log('[profile] ' . $e->getMessage());
    Response::json(['success' => false, 'message' => $e->getMessage()], 500);
}

function handleGetRequests($svc, $action, $user) {
    switch ($action) {
        case 'view':
            // GET //api/profile.php?action=view
            // Returns current user's profile
            $userService = new User();
            $profile = $userService->getById($user['id']);
            
            if (!$profile) {
                Response::json(['success' => false, 'message' => 'Profile not found'], 404);
            }
            
            unset($profile['password_hash']);
            
            Response::json(['success' => true, 'profile' => $profile]);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for GET request'], 400);
    }
}

function handlePostRequests($svc, $action, $user) {
    switch ($action) {
        case 'update':
            // POST //api/profile.php?action=update
            // Body: {"name": "Updated Name", "email": "updated@email.com", "password": "newpassword123"}
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            
            $name = array_key_exists('name', $data) ? $data['name'] : null;
            $email = array_key_exists('email', $data) ? $data['email'] : null;
            $password = array_key_exists('password', $data) ? $data['password'] : null;
            $academic_year = array_key_exists('academic_year', $data) ? $data['academic_year'] : null;
            $bio = array_key_exists('bio', $data) ? $data['bio'] : null;
            
            // Security: Users can ONLY update their own profile (uses authenticated user's ID)
            $result = $svc->updateProfile($user['id'], $name, $email, $password, $academic_year, $bio);
            
            if ($result['ok'] ?? false) {
                $userService = new User();
                $updatedProfile = $userService->getById($user['id']);
                if ($updatedProfile) {
                    unset($updatedProfile['password_hash']);
                }
                Response::json(['success' => true, 'message' => 'Profile updated successfully', 'profile' => $updatedProfile]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Update failed'], 400);
            break;
            
        case 'upload-image':
            // POST //api/profile.php?action=upload-image
            // Multipart form data with 'profile_image' file field
            if (!isset($_FILES['profile_image'])) {
                Response::json(['success' => false, 'message' => 'No image file provided'], 400);
            }
            
            $result = $svc->updateProfileImage($user['id'], $_FILES['profile_image']);
            
            if ($result['ok'] ?? false) {
                Response::json([
                    'success' => true, 
                    'message' => 'Profile image updated successfully',
                    'profile_image_url' => $result['profile_image_url'] ?? null
                ]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Image upload failed'], 400);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for POST request'], 400);
    }
}

function handlePutRequests($svc, $action, $user) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    
    switch ($action) {
        case 'update':
            // PUT //api/profile.php?action=update
            // Body: {"name": "Updated Name", "email": "updated@email.com", "password": "newpassword123"}
            $name = array_key_exists('name', $data) ? $data['name'] : null;
            $email = array_key_exists('email', $data) ? $data['email'] : null;
            $password = array_key_exists('password', $data) ? $data['password'] : null;
            $academic_year = array_key_exists('academic_year', $data) ? $data['academic_year'] : null;
            $bio = array_key_exists('bio', $data) ? $data['bio'] : null;
            
            // Security: Users can ONLY update their own profile (uses authenticated user's ID)
            $result = $svc->updateProfile($user['id'], $name, $email, $password, $academic_year, $bio);
            
            if ($result['ok'] ?? false) {
                $userService = new User();
                $updatedProfile = $userService->getById($user['id']);
                if ($updatedProfile) {
                    unset($updatedProfile['password_hash']);
                }
                Response::json(['success' => true, 'message' => 'Profile updated successfully', 'profile' => $updatedProfile]);
            }
            Response::json(['success' => false, 'message' => $result['message'] ?? 'Update failed'], 400);
            break;
            
        default:
            Response::json(['success' => false, 'message' => 'Invalid action for PUT request'], 400);
    }
}