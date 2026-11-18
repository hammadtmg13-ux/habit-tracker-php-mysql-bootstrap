<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);
$today = date('Y-m-d');

if ($id > 0) {
    // If already checked in today, remove the check-in (undo). Otherwise add it.
    $stmt = $pdo->prepare("SELECT id FROM habit_checkins WHERE habit_id = :id AND checkin_date = :today");
    $stmt->execute([':id' => $id, ':today' => $today]);

    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM habit_checkins WHERE habit_id = :id AND checkin_date = :today");
        $stmt->execute([':id' => $id, ':today' => $today]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO habit_checkins (habit_id, checkin_date) VALUES (:id, :today)");
        $stmt->execute([':id' => $id, ':today' => $today]);
    }
}

header('Location: index.php');
exit;
