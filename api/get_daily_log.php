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
$date = $_GET['date'] ?? date('Y-m-d');

try {
    $pdo = getPDO();
    
    // Get mood log
    $mood = $pdo->prepare('SELECT * FROM mood_logs WHERE user_id = :uid AND date = :date ORDER BY created_at DESC LIMIT 1');
    $mood->execute([':uid' => $user_id, ':date' => $date]);
    $moodData = $mood->fetch();

    // Normalize mood names for consistent UI usage
    function normalize_mood_name($n) {
        if (!$n) return $n;
        $map = [
            'happy'=>'Happy','joyful'=>'Joyful','calm'=>'Calm','peaceful'=>'Peaceful','neutral'=>'Neutral',
            'sad'=>'Sad','angry'=>'Angry','stressed'=>'Stressed','anxious'=>'Anxious','tired'=>'Tired',
            'fearful'=>'Anxious','disgusted'=>'Angry','surprised'=>'Confused','high_energy'=>'Joyful'
        ];
        $k = strtolower($n);
        return $map[$k] ?? (ucfirst($k));
    }
    if ($moodData) {
        if (isset($moodData['face_emotion'])) $moodData['face_emotion'] = normalize_mood_name($moodData['face_emotion']);
        if (isset($moodData['audio_emotion'])) $moodData['audio_emotion'] = normalize_mood_name($moodData['audio_emotion']);
        if (isset($moodData['face_confidence']) && $moodData['face_confidence'] !== null && $moodData['face_confidence'] <= 1) $moodData['face_confidence'] = round($moodData['face_confidence'] * 100);
        if (isset($moodData['audio_score']) && $moodData['audio_score'] !== null && $moodData['audio_score'] <= 1) $moodData['audio_score'] = round($moodData['audio_score'] * 100);
        if (!empty($moodData['meta'])) {
            $m = json_decode($moodData['meta'], true);
            if (is_array($m) && isset($m['selected_mood'])) $m['selected_mood'] = normalize_mood_name($m['selected_mood']);
            $moodData['meta'] = json_encode($m);
        }
    }
    
    // Get diary
    $diary = $pdo->prepare('SELECT * FROM diary_entries WHERE user_id = :uid AND date = :date');
    $diary->execute([':uid' => $user_id, ':date' => $date]);
    $diaryData = $diary->fetch();
    
    // Get tags
    $tags = $pdo->prepare('SELECT tag_name FROM mood_tags WHERE user_id = :uid AND date = :date');
    $tags->execute([':uid' => $user_id, ':date' => $date]);
    $tagList = $tags->fetchAll(PDO::FETCH_COLUMN);
    
    // Get media
    $media = $pdo->prepare('SELECT id, media_type, file_path FROM media_uploads WHERE user_id = :uid AND date = :date');
    $media->execute([':uid' => $user_id, ':date' => $date]);
    $mediaList = $media->fetchAll();
    
    echo json_encode([
        'found' => true,
        'mood' => $moodData ?: null,
        'diary' => $diaryData ?: null,
        'tags' => $tagList,
        'media' => $mediaList
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
?>
