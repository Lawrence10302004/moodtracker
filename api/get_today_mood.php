<?php
// api/get_today_mood.php
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
    $stmt = $pdo->prepare("SELECT * FROM mood_logs WHERE date = {$currentDate} AND user_id = :uid ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([':uid' => $userId]);
    $row = $stmt->fetch();
    if (!$row) {
        echo json_encode(['found' => false]);
    } else {
        // normalize names
        $map = [
            'happy'=>'Happy','joyful'=>'Joyful','calm'=>'Calm','peaceful'=>'Peaceful','neutral'=>'Neutral',
            'sad'=>'Sad','angry'=>'Angry','stressed'=>'Stressed','anxious'=>'Anxious','tired'=>'Tired',
            'fearful'=>'Anxious','disgusted'=>'Angry','surprised'=>'Confused','high_energy'=>'Joyful'
        ];
        $normalize = function($n) use ($map) {
            if (!$n) return $n; $k = strtolower($n); return $map[$k] ?? ucfirst($k);
        };
        if (isset($row['face_emotion'])) $row['face_emotion'] = $normalize($row['face_emotion']);
        if (isset($row['audio_emotion'])) $row['audio_emotion'] = $normalize($row['audio_emotion']);
        if (isset($row['face_confidence']) && $row['face_confidence'] !== null && $row['face_confidence'] <= 1) $row['face_confidence'] = round($row['face_confidence'] * 100);
        if (isset($row['audio_score']) && $row['audio_score'] !== null && $row['audio_score'] <= 1) $row['audio_score'] = round($row['audio_score'] * 100);
        // normalize meta.selected_mood
        if (!empty($row['meta'])) {
            $m = json_decode($row['meta'], true);
            if (is_array($m) && isset($m['selected_mood'])) $m['selected_mood'] = $normalize($m['selected_mood']);
            $row['meta'] = json_encode($m);
        }

        echo json_encode(['found' => true, 'data' => $row]);
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
