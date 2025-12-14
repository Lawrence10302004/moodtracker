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
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');

try {
    $pdo = getPDO();
    
    // Get all moods for the month with related data
    // Select most recent mood_logs row per date to avoid stale grouped values
    $yearFunc = sqlYear('m.date');
    $monthFunc = sqlMonth('m.date');
    $groupConcat = sqlGroupConcat('DISTINCT t.tag_name');
    
    // Build GROUP BY clause based on database type
    if (isPostgreSQL()) {
        $groupBy = "GROUP BY m.id, m.date, m.combined_score, m.face_emotion, m.audio_emotion, m.meta";
    } else {
        $groupBy = "GROUP BY m.date";
    }
    
    // For the subquery, use table alias for date column
    $subqueryYearFunc = sqlYear('ml.date');
    $subqueryMonthFunc = sqlMonth('ml.date');
    
    $sql = "SELECT 
        m.id, m.date, m.combined_score, m.face_emotion, m.audio_emotion, m.meta,
        COUNT(DISTINCT d.id) as has_diary,
        COUNT(DISTINCT mu.id) as has_media,
        {$groupConcat} as tags
        FROM mood_logs m
        JOIN (
            SELECT ml.date, MAX(ml.created_at) AS max_created
            FROM mood_logs ml
            WHERE ml.user_id = :uid AND {$subqueryYearFunc} = :year AND {$subqueryMonthFunc} = :month
            GROUP BY ml.date
        ) latest ON m.date = latest.date AND m.created_at = latest.max_created AND m.user_id = :uid
        LEFT JOIN diary_entries d ON m.user_id = d.user_id AND m.date = d.date
        LEFT JOIN media_uploads mu ON m.user_id = mu.user_id AND m.date = mu.date
        LEFT JOIN mood_tags t ON m.user_id = t.user_id AND m.date = t.date
        WHERE m.user_id = :uid 
        AND {$yearFunc} = :year 
        AND {$monthFunc} = :month
        {$groupBy}
        ORDER BY m.date ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':uid' => $user_id, ':year' => $year, ':month' => $month]);
    $moods = $stmt->fetchAll();
    
    // Normalize mood names and scores for consistent UI
    function normalize_mood_name_small($n) {
        if (!$n) return $n;
        $map = [
            'happy'=>'Happy','joyful'=>'Joyful','calm'=>'Calm','peaceful'=>'Peaceful','neutral'=>'Neutral',
            'sad'=>'Sad','angry'=>'Angry','stressed'=>'Stressed','anxious'=>'Anxious','tired'=>'Tired',
            'fearful'=>'Anxious','disgusted'=>'Angry','surprised'=>'Confused','high_energy'=>'Joyful'
        ];
        $k = strtolower($n);
        return $map[$k] ?? ucfirst($k);
    }
    foreach ($moods as &$m) {
        if (isset($m['face_emotion'])) $m['face_emotion'] = normalize_mood_name_small($m['face_emotion']);
        if (isset($m['audio_emotion'])) $m['audio_emotion'] = normalize_mood_name_small($m['audio_emotion']);
        // expose selected_mood if present in meta to help calendar choose emoji
        if (!empty($m['meta'])) {
            $mm = json_decode($m['meta'], true);
            if (is_array($mm) && isset($mm['selected_mood'])) $m['selected_mood'] = normalize_mood_name_small($mm['selected_mood']);
        }
        // strip meta to avoid exposing raw meta in month response
        unset($m['meta']);
    }
    unset($m);

    echo json_encode(['moods' => $moods]);
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
?>
