<?php
// api/save_mood.php
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
// start session and require a logged-in user
if (session_status() === PHP_SESSION_NONE) session_start();
$userId = $_SESSION['user_id'] ?? null;
if ($userId === null) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$maxPayload = 10 * 1024; // limit payload size (10KB)
$raw = file_get_contents('php://input');
if ($raw === false || strlen($raw) > $maxPayload) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Whitelist and validate fields:
$face_emotion = isset($data['face_emotion']) ? trim(substr($data['face_emotion'], 0, 64)) : null;
$face_confidence = isset($data['face_confidence']) ? floatval($data['face_confidence']) : null;
$audio_emotion = isset($data['audio_emotion']) ? trim(substr($data['audio_emotion'], 0, 64)) : null;
$audio_score = isset($data['audio_score']) ? floatval($data['audio_score']) : null;
$combined_score = isset($data['combined_score']) ? intval($data['combined_score']) : null;
$date = isset($data['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date']) ? $data['date'] : date('Y-m-d');
$time = isset($data['time']) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $data['time']) ? $data['time'] : date('H:i:s');
$diary_id = null;
$metaArr = [];
if (isset($data['meta']) && is_array($data['meta'])) {
    $metaArr = $data['meta'];
    if (isset($metaArr['diary_id'])) {
        $diary_id = intval($metaArr['diary_id']);
        // remove diary_id from meta payload to avoid duplication
        unset($metaArr['diary_id']);
    }
}
$meta = !empty($metaArr) ? json_encode($metaArr) : null;

// Normalize emotion names to TitleCase that's used in the UI
$map = [
    'happy'=>'Happy','joyful'=>'Joyful','calm'=>'Calm','peaceful'=>'Peaceful','neutral'=>'Neutral',
    'sad'=>'Sad','angry'=>'Angry','stressed'=>'Stressed','anxious'=>'Anxious','tired'=>'Tired',
    'fearful'=>'Anxious','disgusted'=>'Angry','surprised'=>'Confused','high_energy'=>'Joyful'
];
if ($face_emotion !== null) {
    $key = strtolower($face_emotion);
    if (isset($map[$key])) $face_emotion = $map[$key];
    else $face_emotion = ucfirst($key);
}
if ($audio_emotion !== null) {
    $key = strtolower($audio_emotion);
    if (isset($map[$key])) $audio_emotion = $map[$key];
    else $audio_emotion = ucfirst($key);
}

// Normalize confidence/score to percentages (0-100) if they were provided as 0..1
if ($face_confidence !== null && $face_confidence <= 1) {
    $face_confidence = round($face_confidence * 100);
}
if ($audio_score !== null && $audio_score <= 1) {
    $audio_score = round($audio_score * 100);
}

if ($combined_score === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing combined_score']);
    exit;
}

try {
    $pdo = getPDO();

    // Try to find an existing entry for this user/date and update it (upsert-like behavior)
    $existingStmt = $pdo->prepare('SELECT id, meta FROM mood_logs WHERE user_id = :uid AND date = :date ORDER BY created_at DESC LIMIT 1');
    $existingStmt->execute([':uid' => $userId, ':date' => $date]);
    $existing = $existingStmt->fetch();

    // If meta exists in DB, merge with incoming meta (incoming wins)
    $mergedMetaArr = $metaArr;
    if ($existing && !empty($existing['meta'])) {
        $existingMeta = json_decode($existing['meta'], true);
        if (is_array($existingMeta)) {
            $mergedMetaArr = array_merge($existingMeta, $metaArr);
        }
    }
    // ensure selected_mood normalized if present
    if (isset($mergedMetaArr['selected_mood'])) {
        $k = strtolower($mergedMetaArr['selected_mood']);
        $mergedMetaArr['selected_mood'] = $map[$k] ?? ucfirst($k);
    }
    $mergedMeta = !empty($mergedMetaArr) ? json_encode($mergedMetaArr) : null;

    if ($existing) {
        // update existing row
        $sql = 'UPDATE mood_logs SET time = :time, face_emotion = :face_emotion, face_confidence = :face_confidence, audio_emotion = :audio_emotion, audio_score = :audio_score, combined_score = :combined_score, diary_id = :diary_id, meta = :meta, created_at = CURRENT_TIMESTAMP WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $params = [
            ':time' => $time,
            ':face_emotion' => $face_emotion,
            ':face_confidence' => $face_confidence,
            ':audio_emotion' => $audio_emotion,
            ':audio_score' => $audio_score,
            ':combined_score' => $combined_score,
            ':diary_id' => $diary_id,
            ':meta' => $mergedMeta,
            ':id' => $existing['id']
        ];
        $stmt->execute($params);
        $resultId = (int)$existing['id'];
    } else {
        // insert new
        $cols = 'user_id, date, time, face_emotion, face_confidence, audio_emotion, audio_score, combined_score';
        $vals = ':user_id, :date, :time, :face_emotion, :face_confidence, :audio_emotion, :audio_score, :combined_score';
        if ($diary_id !== null) { $cols .= ', diary_id'; $vals .= ', :diary_id'; }
        if ($mergedMeta !== null) { $cols .= ', meta'; $vals .= ', :meta'; }

        $sql = "INSERT INTO mood_logs ({$cols}) VALUES ({$vals})";
        $stmt = $pdo->prepare($sql);
        $params = [
            ':user_id' => $userId,
            ':date' => $date,
            ':time' => $time,
            ':face_emotion' => $face_emotion,
            ':face_confidence' => $face_confidence,
            ':audio_emotion' => $audio_emotion,
            ':audio_score' => $audio_score,
            ':combined_score' => $combined_score
        ];
        if ($diary_id !== null) $params[':diary_id'] = $diary_id;
        if ($mergedMeta !== null) $params[':meta'] = $mergedMeta;
        $stmt->execute($params);
        $resultId = (int)$pdo->lastInsertId();
    }

    echo json_encode(['ok' => true, 'id' => $resultId]);
} catch (Exception $e) {
    http_response_code(500);
    // don't leak exception messages in production; log them instead
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
