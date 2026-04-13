<?php
require 'database.php';
include 'auth.php';

$action     = $_GET['action'] ?? '';
$student_id = $_SESSION['student_id'];

// Delete event — only if it belongs to the logged-in student
if ($action === 'delete' && isset($_GET['id'])) {
    $id   = (int)$_GET['id'];
    $stmt = mysqli_prepare($con, 'DELETE FROM events WHERE id = ? AND student_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $id, $student_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Event deleted successfully.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Failed to delete event. Please try again.'];
    }
    mysqli_stmt_close($stmt);
    header('Location: event-tracker.php');
    exit;
}

// Search / filter / sort params
$q           = trim($_GET['q'] ?? '');
$filter_type = $_GET['type'] ?? '';
$sort        = $_GET['sort'] ?? 'date_time';
$order       = strtolower($_GET['order'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

$allowed_sorts = ['event_name', 'date_time', 'event_loc', 'event_type'];
if (!in_array($sort, $allowed_sorts)) $sort = 'date_time';

// Build query using prepared statements — only show logged-in student's events
$where  = 'WHERE student_id = ?';
$params = [$student_id];
$types  = 'i';

if ($q !== '') {
    $like    = '%' . $q . '%';
    $where  .= ' AND (event_name LIKE ? OR event_loc LIKE ? OR description LIKE ?)';
    $types  .= 'sss';
    $params  = array_merge($params, [$like, $like, $like]);
}
if ($filter_type !== '') {
    $where  .= ' AND event_type = ?';
    $types  .= 's';
    $params[] = $filter_type;
}

$sql  = "SELECT * FROM events $where ORDER BY {$sort} {$order}";
$stmt = mysqli_prepare($con, $sql);

$events = [];
if ($stmt) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    if (mysqli_stmt_execute($stmt)) {
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) {
            $events[] = $row;
        }
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Failed to load events. Please try again.'];
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Database error. Please try again.'];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Event Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Event Tracker</h1>
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
        <div class="actions"><a class="btn" href="event-tracker-form.php">Add New Event</a></div>
        <form method="get" class="filters">
            <input type="text" name="q" placeholder="Search title, location, description"
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <select name="type">
                <option value="">All types</option>
                <option value="Event"       <?= (($_GET['type'] ?? '') === 'Event')       ? 'selected' : '' ?>>Event</option>
                <option value="Competition" <?= (($_GET['type'] ?? '') === 'Competition') ? 'selected' : '' ?>>Competition</option>
                <option value="Workshop"    <?= (($_GET['type'] ?? '') === 'Workshop')    ? 'selected' : '' ?>>Workshop</option>
                <option value="Talks"       <?= (($_GET['type'] ?? '') === 'Talks')       ? 'selected' : '' ?>>Talk</option>
            </select>
            <select name="sort">
                <option value="date_time"  <?= (($_GET['sort'] ?? 'date_time') === 'date_time')  ? 'selected' : '' ?>>Date</option>
                <option value="event_name" <?= (($_GET['sort'] ?? '')          === 'event_name') ? 'selected' : '' ?>>Title</option>
                <option value="event_loc"  <?= (($_GET['sort'] ?? '')          === 'event_loc')  ? 'selected' : '' ?>>Location</option>
                <option value="event_type" <?= (($_GET['sort'] ?? '')          === 'event_type') ? 'selected' : '' ?>>Type</option>
            </select>
            <select name="order">
                <option value="asc"  <?= (($_GET['order'] ?? 'asc') === 'asc')  ? 'selected' : '' ?>>Asc</option>
                <option value="desc" <?= (($_GET['order'] ?? '')    === 'desc') ? 'selected' : '' ?>>Desc</option>
            </select>
            <button class="btn" type="submit">Apply</button>
            <a class="btn" href="event-tracker.php">Reset</a>
        </form>
    </div>

    <div class="panel">
        <h2>My Events</h2>
        <?php if (count($events) === 0): ?>
            <p class="muted">No events recorded yet.</p>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Event Type</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($events as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['event_name']) ?></td>
                        <td><?= htmlspecialchars($e['date_time']) ?></td>
                        <td><?= htmlspecialchars($e['event_loc']) ?></td>
                        <td><?= htmlspecialchars($e['event_type']) ?></td>
                        <td><?= nl2br(htmlspecialchars($e['description'])) ?></td>
                        <td class="nowrap">
                            <a class="btn" href="event-tracker-form.php?id=<?= $e['id'] ?>">Edit</a>
                            <a class="btn danger"
                               href="?action=delete&id=<?= $e['id'] ?>"
                               onclick="return confirm('Delete this event?')">Delete</a>
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