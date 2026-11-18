<?php
require 'config.php';

$today = date('Y-m-d');
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';

// Fetch habits with optional search and category filter
$sql = "SELECT h.*,
            EXISTS(SELECT 1 FROM habit_checkins c WHERE c.habit_id = h.id AND c.checkin_date = :today) AS done_today,
            (SELECT COUNT(*) FROM habit_checkins c WHERE c.habit_id = h.id) AS total_checkins
        FROM habits h WHERE 1=1";
$params = [':today' => $today];

if ($search !== '') {
    $sql .= " AND h.name LIKE :search";
    $params[':search'] = '%' . $search . '%';
}
if ($categoryFilter !== 'all') {
    $sql .= " AND h.category = :category";
    $params[':category'] = $categoryFilter;
}
$sql .= " ORDER BY h.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$habits = $stmt->fetchAll();

// Calculate the current streak (consecutive days up to today or yesterday)
function currentStreak($pdo, $habitId, $today) {
    $stmt = $pdo->prepare("SELECT checkin_date FROM habit_checkins WHERE habit_id = :id ORDER BY checkin_date DESC");
    $stmt->execute([':id' => $habitId]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!$dates) return 0;

    $streak = 0;
    $day = new DateTime($today);
    // A streak counts from today if done today, otherwise from yesterday
    if ($dates[0] !== $today) {
        $day->modify('-1 day');
    }
    foreach ($dates as $date) {
        if ($date === $day->format('Y-m-d')) {
            $streak++;
            $day->modify('-1 day');
        } else {
            break;
        }
    }
    return $streak;
}

$totalHabits = count($habits);
$doneToday = 0;
foreach ($habits as &$habit) {
    $habit['streak'] = currentStreak($pdo, $habit['id'], $today);
    if ($habit['done_today']) $doneToday++;
}
unset($habit);

$progress = $totalHabits > 0 ? round(($doneToday / $totalHabits) * 100) : 0;
$categories = ['health' => 'Health', 'fitness' => 'Fitness', 'study' => 'Study', 'productivity' => 'Productivity', 'personal' => 'Personal'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HabitTrack - Simple Habit Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-4">
    <header class="text-center mb-4">
        <h1 class="app-title">HabitTrack</h1>
        <p class="text-muted">Build good habits, one day at a time.</p>
        <p class="text-muted small">Today: <?php echo date('l, F j, Y'); ?></p>
    </header>

    <!-- Today's progress -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">Today's Progress</h5>
                <span class="badge bg-primary fs-6"><?php echo $doneToday; ?> / <?php echo $totalHabits; ?> done</span>
            </div>
            <div class="progress" style="height: 22px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $progress; ?>%</div>
            </div>
        </div>
    </div>

    <!-- Add habit form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Add a New Habit</h5>
            <form action="add_habit.php" method="POST" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="name" class="form-control" placeholder="Habit name, e.g. Read for 20 minutes" required maxlength="255">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <?php foreach ($categories as $key => $label): ?>
                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="target_days" class="form-control" placeholder="Target days" min="1" max="365" value="21">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search and filter -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="search" name="search" class="form-control" placeholder="Search habits..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="all" <?php echo $categoryFilter === 'all' ? 'selected' : ''; ?>>All categories</option>
                <?php foreach ($categories as $key => $label): ?>
                    <option value="<?php echo $key; ?>" <?php echo $categoryFilter === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-secondary">Search</button>
        </div>
    </form>

    <!-- Habit list -->
    <?php if (empty($habits)): ?>
        <div class="alert alert-info text-center">No habits yet. Add your first habit above to get started.</div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($habits as $habit): ?>
                <div class="col-md-6">
                    <div class="card habit-card shadow-sm <?php echo $habit['done_today'] ? 'habit-done' : ''; ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($habit['name']); ?></h5>
                                <span class="badge bg-info text-dark"><?php echo $categories[$habit['category']]; ?></span>
                            </div>
                            <p class="text-muted small mb-2">
                                Streak: <strong class="text-warning"><?php echo $habit['streak']; ?> day<?php echo $habit['streak'] === 1 ? '' : 's'; ?></strong>
                                &middot; Total check-ins: <?php echo $habit['total_checkins']; ?>
                                &middot; Target: <?php echo $habit['target_days']; ?> days
                            </p>
                            <div class="d-flex gap-2">
                                <a href="toggle_habit.php?id=<?php echo $habit['id']; ?>" class="btn btn-sm <?php echo $habit['done_today'] ? 'btn-success' : 'btn-outline-success'; ?>">
                                    <?php echo $habit['done_today'] ? 'Done today' : 'Mark done today'; ?>
                                </a>
                                <a href="edit_habit.php?id=<?php echo $habit['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="delete_habit.php?id=<?php echo $habit['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirmDelete();">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <footer class="text-center text-muted small mt-5">
        Built by Hammad with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
