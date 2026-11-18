<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = $_POST['category'] ?? 'personal';
    $targetDays = (int)($_POST['target_days'] ?? 21);

    $allowed = ['health', 'fitness', 'study', 'productivity', 'personal'];
    if (!in_array($category, $allowed, true)) {
        $category = 'personal';
    }
    if ($targetDays < 1 || $targetDays > 365) {
        $targetDays = 21;
    }

    if ($name !== '') {
        $stmt = $pdo->prepare("INSERT INTO habits (name, category, target_days) VALUES (:name, :category, :target_days)");
        $stmt->execute([':name' => $name, ':category' => $category, ':target_days' => $targetDays]);
    }
}

header('Location: index.php');
exit;
