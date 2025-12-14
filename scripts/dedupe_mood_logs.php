<?php
// scripts/dedupe_mood_logs.php
// Usage: php scripts/dedupe_mood_logs.php
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();
try {
    $pdo->beginTransaction();
    // find user/date groups with more than one entry
    $g = $pdo->query('SELECT user_id, date, COUNT(*) as c FROM mood_logs GROUP BY user_id, date HAVING c > 1');
    $groups = $g->fetchAll(PDO::FETCH_ASSOC);
    $deleted = 0;
    foreach ($groups as $grp) {
        $uid = $grp['user_id'];
        $date = $grp['date'];
        // select ids ordered newest first
        $s = $pdo->prepare('SELECT id FROM mood_logs WHERE user_id = :uid AND date = :date ORDER BY created_at DESC');
        $s->execute([':uid'=>$uid, ':date'=>$date]);
        $ids = $s->fetchAll(PDO::FETCH_COLUMN);
        // keep first, delete the rest
        $keep = array_shift($ids);
        if (count($ids) > 0) {
            $in = str_repeat('?,', count($ids) - 1) . '?';
            $del = $pdo->prepare("DELETE FROM mood_logs WHERE id IN ($in)");
            $del->execute($ids);
            $deleted += $del->rowCount();
        }
    }
    $pdo->commit();
    echo "Deduplication complete. Deleted {$deleted} rows.\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

?>