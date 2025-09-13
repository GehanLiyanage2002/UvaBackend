<?php
// services/ProfileService.php
require_once __DIR__ . '/../models/User.php';

class ProfileService
{
    private User $user;
    private string $uploadDir;

    public function __construct()
    {
        $this->user = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Set upload directory - adjust path as needed
        $this->uploadDir = __DIR__ . '/../uploads/profile_images/';
        
        // Create directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function updateProfile(int $id, ?string $name = null, ?string $email = null, ?string $password = null, ?string $academic_year = null, ?string $bio = null): array
    {
        if ($id <= 0) {
            return ['ok' => false, 'message' => 'Invalid user ID'];
        }
        return $this->user->update($id, $name, $email, $password, $academic_year, $bio);
    }

    public function updateProfileImage(int $id, array $fileData): array {
        if ($id <= 0) return ['ok'=>false,'message'=>'Invalid user ID'];
        if ($fileData['error'] !== UPLOAD_ERR_OK) return ['ok'=>false,'message'=>'File upload error'];

        // Validate file
        $allowedTypes = ['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
        $fileType = mime_content_type($fileData['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) return ['ok'=>false,'message'=>'Invalid file type'];

        $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        $fileName = 'profile_'.$id.'_'.time().'_'.uniqid().'.'.$ext;
        $filePath = $this->uploadDir.$fileName;

        // Move uploaded file
        if (!move_uploaded_file($fileData['tmp_name'], $filePath)) return ['ok'=>false,'message'=>'Failed to move uploaded file'];

        // Resize optional
        $this->resizeImage($filePath, 400, 400);

        // Delete old image
        $currentUser = $this->user->getById($id);
        if ($currentUser && !empty($currentUser['profile_image'])) {
            $oldPath = $this->uploadDir . basename($currentUser['profile_image']);
            if (file_exists($oldPath)) unlink($oldPath);
        }

        // Update DB with full URL
        $baseUrl = $this->getBaseUrl();
        $imageUrl = $baseUrl.'/uploads/profile_images/'.$fileName;
        $result = $this->user->updateProfileImageUrl($id, $imageUrl);

        if (!$result['ok']) {
            unlink($filePath); // rollback
            return ['ok'=>false,'message'=>$result['message']];
        }

        return ['ok'=>true,'profile_image_url'=>$imageUrl];
    }

    private function resizeImage(string $filePath, int $maxWidth, int $maxHeight): void {
        $info = getimagesize($filePath);
        if (!$info) return;
        list($width, $height, $type) = $info;
        $ratio = min($maxWidth/$width, $maxHeight/$height);
        if ($ratio >= 1) return;
        $newW = (int)($width*$ratio);
        $newH = (int)($height*$ratio);

        switch($type) {
            case IMAGETYPE_JPEG: $src=imagecreatefromjpeg($filePath); break;
            case IMAGETYPE_PNG: $src=imagecreatefrompng($filePath); break;
            case IMAGETYPE_GIF: $src=imagecreatefromgif($filePath); break;
            case IMAGETYPE_WEBP: $src=imagecreatefromwebp($filePath); break;
            default: return;
        }
        $dst = imagecreatetruecolor($newW,$newH);
        if($type==IMAGETYPE_PNG||$type==IMAGETYPE_GIF){
            imagealphablending($dst,false);
            imagesavealpha($dst,true);
            $transparent=imagecolorallocatealpha($dst,255,255,255,127);
            imagefilledrectangle($dst,0,0,$newW,$newH,$transparent);
        }
        imagecopyresampled($dst,$src,0,0,0,0,$newW,$newH,$width,$height);
        switch($type){
            case IMAGETYPE_JPEG: imagejpeg($dst,$filePath,85); break;
            case IMAGETYPE_PNG: imagepng($dst,$filePath,8); break;
            case IMAGETYPE_GIF: imagegif($dst,$filePath); break;
            case IMAGETYPE_WEBP: imagewebp($dst,$filePath,85); break;
        }
        imagedestroy($src); imagedestroy($dst);
    }

    private function getBaseUrl(): string {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on')?'https':'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $script = dirname($_SERVER['SCRIPT_NAME']);
        $basePath = str_replace('/api','',$script);
        return $protocol.'://'.$host.rtrim($basePath,'/');
    }
}