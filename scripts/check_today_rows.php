<?php
require_once __DIR__ . '/../config/db.php';
$pdo = getPDO();
$stmt = $pdo->prepare('SELECT user_id, date, COUNT(*) as c, GROUP_CONCAT(id) as ids FROM mood_logs WHERE date = CURRENT_DATE() GROUP BY user_id');
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "user_id={$r['user_id']} date={$r['date']} count={$r['c']} ids={$r['ids']}\n";
}
?>