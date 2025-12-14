<?php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$diary_id = $_POST['diary_id'] ?? null;
$date = $_POST['date'] ?? date('Y-m-d');

if (!isset($_FILES['media'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file provided']);
    exit;
}

$file = $_FILES['media'];
$allowed = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/quicktime'];
$maxSize = 50 * 1024 * 1024; // 50MB

if (!in_array($file['type'], $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Allowed: JPG, PNG, GIF, MP4, WebM']);
    exit;
}

if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large. Max 50MB']);
    exit;
}

if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Upload error']);
    exit;
}

try {
    // Create uploads directory if it doesn't exist
    $uploadDir = __DIR__ . '/../uploads/' . $user_id . '/' . date('Y/m');
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $filepath = $uploadDir . '/' . $filename;
    $relPath = 'uploads/' . $user_id . '/' . date('Y/m') . '/' . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Upload failed');
    }
    
    // Determine media type
    $mediaType = strpos($file['type'], 'image') !== false ? 'photo' : 'video';
    
    // Save to database
    $pdo = getPDO();
    $stmt = $pdo->prepare('INSERT INTO media_uploads (user_id, diary_id, date, media_type, file_path, file_size) VALUES (:uid, :did, :date, :type, :path, :size)');
    $stmt->execute([
        ':uid' => $user_id,
        ':did' => $diary_id,
        ':date' => $date,
        ':type' => $mediaType,
        ':path' => $relPath,
        ':size' => $file['size']
    ]);
    
    echo json_encode([
        'ok' => true,
        'id' => getLastInsertId($pdo, 'media_uploads'),
        'path' => $relPath,
        'type' => $mediaType,
        'filename' => $filename
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
?>
