<?php
require 'database.php';
include 'auth.php';

$action     = $_GET['action'] ?? '';
$student_id = $_SESSION['student_id'];

// Delete achievement — only if it belongs to the logged-in student
if ($action === 'delete' && isset($_GET['id'])) {
    $id   = (int)$_GET['id'];
    $stmt = mysqli_prepare($con, 'DELETE FROM achievements WHERE id = ? AND student_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $id, $student_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Achievement deleted.'];
    header('Location: achievement-tracker.php');
    exit;
}

// Search / filter / sort params
$q           = trim($_GET['q'] ?? '');
$filter_type = $_GET['type'] ?? '';
$sort        = $_GET['sort'] ?? 'date_received';
$order       = strtolower($_GET['order'] ?? 'desc') === 'desc' ? 'DESC' : 'ASC';

$allowed_sorts = ['title', 'date_received', 'achievement_type', 'organisation'];
if (!in_array($sort, $allowed_sorts)) $sort = 'date_received';

// Build query using prepared statement
$where  = 'WHERE student_id = ?';
$params = [$student_id];
$types  = 'i';

if ($q !== '') {
    $like    = '%' . $q . '%';
    $where  .= ' AND (title LIKE ? OR organisation LIKE ? OR description LIKE ?)';
    $types  .= 'sss';
    $params  = array_merge($params, [$like, $like, $like]);
}
if ($filter_type !== '') {
    $where  .= ' AND achievement_type = ?';
    $types  .= 's';
    $params[] = $filter_type;
}

$sql  = "SELECT * FROM achievements $where ORDER BY {$sort} {$order}";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$res  = mysqli_stmt_get_result($stmt);

$achievements = [];
while ($row = mysqli_fetch_assoc($res)) {
    $achievements[] = $row;
}
mysqli_stmt_close($stmt);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Achievement Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Achievement Tracker</h1>
    <div class="actions" style="margin-bottom:1rem;">
        <a class="btn" href="dashboard.php">&larr; Dashboard</a>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <?php $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
        <div class="form <?= $f['type'] === 'success' ? 'success' : 'error' ?>" style="max-width:100%;">
            <?= htmlspecialchars($f['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <div class="actions"><a class="btn" href="achievement-tracker-form.php">Add New Achievement</a></div>
        <form method="get" class="filters">
            <input type="text" name="q" placeholder="Search title, organisation, description"
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <select name="type">
                <option value="">All types</option>
                <option value="Award"       <?= (($_GET['type'] ?? '') === 'Award')       ? 'selected' : '' ?>>Award</option>
                <option value="Certificate" <?= (($_GET['type'] ?? '') === 'Certificate') ? 'selected' : '' ?>>Certificate</option>
                <option value="Recognition" <?= (($_GET['type'] ?? '') === 'Recognition') ? 'selected' : '' ?>>Recognition</option>
                <option value="Scholarship" <?= (($_GET['type'] ?? '') === 'Scholarship') ? 'selected' : '' ?>>Scholarship</option>
                <option value="Other"       <?= (($_GET['type'] ?? '') === 'Other')       ? 'selected' : '' ?>>Other</option>
            </select>
            <select name="sort">
                <option value="date_received"    <?= (($_GET['sort'] ?? '') === 'date_received')    ? 'selected' : '' ?>>Date</option>
                <option value="title"            <?= (($_GET['sort'] ?? '') === 'title')            ? 'selected' : '' ?>>Title</option>
                <option value="achievement_type" <?= (($_GET['sort'] ?? '') === 'achievement_type') ? 'selected' : '' ?>>Type</option>
                <option value="organisation"     <?= (($_GET['sort'] ?? '') === 'organisation')     ? 'selected' : '' ?>>Organisation</option>
            </select>
            <select name="order">
                <option value="desc" <?= (($_GET['order'] ?? 'desc') === 'desc') ? 'selected' : '' ?>>Newest first</option>
                <option value="asc"  <?= (($_GET['order'] ?? '')      === 'asc')  ? 'selected' : '' ?>>Oldest first</option>
            </select>
            <button class="btn" type="submit">Apply</button>
            <a class="btn" href="achievement-tracker.php">Reset</a>
        </form>
    </div>

    <div class="panel">
        <h2>Achievements</h2>
        <?php if (count($achievements) === 0): ?>
            <p class="muted">No achievements recorded yet.</p>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Date Received</th>
                    <th>Organisation</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($achievements as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['title']) ?></td>
                        <td><?= htmlspecialchars($a['achievement_type']) ?></td>
                        <td><?= htmlspecialchars($a['date_received']) ?></td>
                        <td><?= htmlspecialchars($a['organisation']) ?></td>
                        <td><?= nl2br(htmlspecialchars($a['description'])) ?></td>
                        <td class="nowrap">
                            <a class="btn" href="achievement-tracker-form.php?id=<?= $a['id'] ?>">Edit</a>
                            <a class="btn danger"
                               href="?action=delete&id=<?= $a['id'] ?>"
                               onclick="return confirm('Delete this achievement?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>
</body>
</html>