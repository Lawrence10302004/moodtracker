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
$content = $_POST['content'] ?? '';
$date = $_POST['date'] ?? date('Y-m-d');

if (empty($content)) {
    http_response_code(400);
    echo json_encode(['error' => 'Content required']);
    exit;
}

try {
    $pdo = getPDO();
    
    // Check if entry exists for this date
    $check = $pdo->prepare('SELECT id FROM diary_entries WHERE user_id = :uid AND date = :date');
    $check->execute([':uid' => $user_id, ':date' => $date]);
    $existing = $check->fetch();
    
    if ($existing) {
        // Update existing
        $currentTimestamp = sqlCurrentTimestamp();
        $stmt = $pdo->prepare("UPDATE diary_entries SET content = :content, created_at = {$currentTimestamp} WHERE id = :id");
        $stmt->execute([':content' => $content, ':id' => $existing['id']]);
        echo json_encode(['ok' => true, 'id' => $existing['id'], 'action' => 'updated']);
    } else {
        // Insert new
        $currentTime = sqlCurrentTime();
        $stmt = $pdo->prepare("INSERT INTO diary_entries (user_id, date, time, content) VALUES (:uid, :date, {$currentTime}, :content)");
        $stmt->execute([':uid' => $user_id, ':date' => $date, ':content' => $content]);
        echo json_encode(['ok' => true, 'id' => getLastInsertId($pdo, 'diary_entries'), 'action' => 'created']);
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
?>
