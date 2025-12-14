<?php
// scripts/normalize_mood_logs.php
// Usage: php scripts/normalize_mood_logs.php
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();
$map = [
    'happy'=>'Happy','joyful'=>'Joyful','calm'=>'Calm','peaceful'=>'Peaceful','neutral'=>'Neutral',
    'sad'=>'Sad','angry'=>'Angry','stressed'=>'Stressed','anxious'=>'Anxious','tired'=>'Tired',
    'fearful'=>'Anxious','disgusted'=>'Angry','surprised'=>'Confused','high_energy'=>'Joyful'
];
function normalize($n, $map) {
    if ($n === null) return null;
    $k = strtolower($n);
    return $map[$k] ?? ucfirst($k);
}

try {
    $pdo->beginTransaction();
    $stmt = $pdo->query('SELECT id, face_emotion, audio_emotion, face_confidence, audio_score, meta FROM mood_logs');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $updated = 0;
    foreach ($rows as $r) {
        $id = $r['id'];
        $fe = normalize($r['face_emotion'], $map);
        $ae = normalize($r['audio_emotion'], $map);
        $fc = $r['face_confidence'];
        $as = $r['audio_score'];
        $changed = false;
        if ($fc !== null && $fc <= 1) { $fc = round($fc * 100); $changed = true; }
        if ($as !== null && $as <= 1) { $as = round($as * 100); $changed = true; }
        if ($fe !== $r['face_emotion']) $changed = true;
        if ($ae !== $r['audio_emotion']) $changed = true;
        $meta = $r['meta'];
        if ($meta) {
            $m = json_decode($meta, true);
            if (is_array($m) && isset($m['selected_mood'])) {
                $nm = normalize($m['selected_mood'], $map);
                if ($nm !== $m['selected_mood']) { $m['selected_mood'] = $nm; $meta = json_encode($m); $changed = true; }
            }
        }
        if ($changed) {
            $u = $pdo->prepare('UPDATE mood_logs SET face_emotion = :fe, face_confidence = :fc, audio_emotion = :ae, audio_score = :as, meta = :meta WHERE id = :id');
            $u->execute([':fe'=>$fe, ':fc'=>$fc, ':ae'=>$ae, ':as'=>$as, ':meta'=>$meta, ':id'=>$id]);
            $updated++;
        }
    }
    $pdo->commit();
    echo "Normalization complete. Updated {$updated} rows.\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

?>