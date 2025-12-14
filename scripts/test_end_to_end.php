<?php
// scripts/test_end_to_end.php
// Quick local test: insert/update today's mood for user 1 and include API endpoints to verify outputs
require_once __DIR__ . '/../config/db.php';
session_start();
$_SESSION['user_id'] = 1;

$pdo = getPDO();
$date = date('Y-m-d');

// upsert-like: update existing or insert
$stmt = $pdo->prepare('SELECT id FROM mood_logs WHERE user_id = :uid AND date = :date ORDER BY created_at DESC LIMIT 1');
$stmt->execute([':uid'=>1, ':date'=>$date]);
$row = $stmt->fetch();
if ($row) {
    $u = $pdo->prepare('UPDATE mood_logs SET face_emotion = :fe, face_confidence = :fc, audio_emotion = :ae, audio_score = :as, combined_score = :cs, meta = :meta, created_at = CURRENT_TIMESTAMP WHERE id = :id');
    $u->execute([':fe'=>'Happy', ':fc'=>80, ':ae'=>'Joyful', ':as'=>85, ':cs'=>82, ':meta'=>json_encode(['selected_mood'=>'Happy']), ':id'=>$row['id']]);
    echo "Updated existing row id={$row['id']}\n";
} else {
    $i = $pdo->prepare('INSERT INTO mood_logs (user_id, date, time, face_emotion, face_confidence, audio_emotion, audio_score, combined_score, meta) VALUES (:uid, :date, :time, :fe, :fc, :ae, :as, :cs, :meta)');
    $i->execute([':uid'=>1, ':date'=>$date, ':time'=>date('H:i:s'), ':fe'=>'Happy', ':fc'=>80, ':ae'=>'Joyful', ':as'=>85, ':cs'=>82, ':meta'=>json_encode(['selected_mood'=>'Happy'])]);
    echo "Inserted new row id=" . $pdo->lastInsertId() . "\n";
}

// include get_daily_log.php and capture output
ob_start();
$_GET['date'] = $date;
include __DIR__ . '/../api/get_daily_log.php';
$out = ob_get_clean();
echo "\nget_daily_log output:\n" . $out . "\n";

// include get_month_moods.php
ob_start();
$_GET['year'] = date('Y');
$_GET['month'] = date('m');
include __DIR__ . '/../api/get_month_moods.php';
$out2 = ob_get_clean();
echo "\nget_month_moods output (truncated):\n" . substr($out2, 0, 800) . "\n";

?>