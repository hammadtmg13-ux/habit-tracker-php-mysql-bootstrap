<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);
$categories = ['health' => 'Health', 'fitness' => 'Fitness', 'study' => 'Study', 'productivity' => 'Productivity', 'personal' => 'Personal'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $category = $_POST['category'] ?? 'personal';
    $targetDays = (int)($_POST['target_days'] ?? 21);

    $allowed = array_keys($categories);
    if (!in_array($category, $allowed, true)) {
        $category = 'personal';
    }
    if ($targetDays < 1 || $targetDays > 365) {
        $targetDays = 21;
    }

    if ($id > 0 && $name !== '') {
        $stmt = $pdo->prepare("UPDATE habits SET name = :name, category = :category, target_days = :target_days WHERE id = :id");
        $stmt->execute([':name' => $name, ':category' => $category, ':target_days' => $targetDays, ':id' => $id]);
    }
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM habits WHERE id = :id");
$stmt->execute([':id' => $id]);
$habit = $stmt->fetch();

if (!$habit) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Habit - HabitTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-3">Edit Habit</h4>
                    <form action="edit_habit.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $habit['id']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Habit name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($habit['name']); ?>" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <?php foreach ($categories as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $habit['category'] === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target days</label>
                            <input type="number" name="target_days" class="form-control" min="1" max="365" value="<?php echo $habit['target_days']; ?>">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
