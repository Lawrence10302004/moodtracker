<?php
// api/get_insights.php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId === null) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }

    $pdo = getPDO();
    $currentDate = sqlCurrentDate();
    $dateFunc = sqlDate('created_at');
    $stmt = $pdo->prepare("SELECT * FROM mood_logs WHERE {$dateFunc} = {$currentDate} AND user_id = :uid ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([':uid' => $userId]);
    $row = $stmt->fetch();

    if (!$row) {
        echo json_encode(['found' => false]);
        exit;
    }

    $response = [
        'found' => true,
        'face_emotion'    => $row['face_emotion'] ?? null,
        'face_confidence' => $row['face_confidence'] ?? null,
        'audio_emotion'   => $row['audio_emotion'] ?? null,
        'audio_score'     => $row['audio_score'] ?? null,
        'combined_score'  => $row['combined_score'] ?? null,
        'created_at'      => $row['created_at'] ?? null,
    ];

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
