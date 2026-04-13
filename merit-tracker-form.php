<?php
require 'database.php';
include 'auth.php';

$id         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hours       = (int)($_POST['hours'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $date        = trim($_POST['date'] ?? '');
    $post_id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($hours <= 0 || empty($description) || empty($date)) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'All fields are required and hours must be greater than 0.'];
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if ($post_id > 0) {
        $stmt = mysqli_prepare($con,
            'UPDATE merits SET hours = ?, description = ?, date = ? WHERE id = ? AND student_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'issii', $hours, $description, $date, $post_id, $student_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Merit record updated successfully.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Failed to update merit record. Please try again.'];
        }
        mysqli_stmt_close($stmt);
    } else {
        $stmt = mysqli_prepare($con,
            'INSERT INTO merits (student_id, hours, description, date) VALUES (?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'iiss', $student_id, $hours, $description, $date);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Merit record added successfully.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Failed to add merit record. Please try again.'];
        }
        mysqli_stmt_close($stmt);
    }

    header('Location: merit-tracker.php');
    exit;
}

$merit = null;
if ($id > 0) {
    $stmt = mysqli_prepare($con, 'SELECT * FROM merits WHERE id = ? AND student_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $id, $student_id);
    mysqli_stmt_execute($stmt);
    $res   = mysqli_stmt_get_result($stmt);
    $merit = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$merit) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Record not found or access denied.'];
        header('Location: merit-tracker.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $id > 0 ? 'Edit Merit Record' : 'Add Merit Record' ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1><?= $id > 0 ? 'Edit Merit Record' : 'Add Merit Record' ?></h1>

    <?php if (!empty($_SESSION['flash'])): ?>
        <?php $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
        <div class="form <?= $f['type'] === 'success' ? 'success' : 'error' ?>" style="max-width:100%;">
            <?= htmlspecialchars($f['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <form method="post" action="">
            <?php if ($id > 0): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>

            <label>Contribution Hours
                <input type="number" name="hours"
                       value="<?= htmlspecialchars($merit['hours'] ?? '') ?>"
                       min="1" required>
            </label>

            <label>Activity Description
                <textarea name="description" required><?= htmlspecialchars($merit['description'] ?? '') ?></textarea>
            </label>

            <label>Date
                <input type="date" name="date"
                       value="<?= htmlspecialchars($merit['date'] ?? '') ?>"
                       required>
            </label>

            <div class="actions">
                <button type="submit"><?= $id > 0 ? 'Save Changes' : 'Add Merit Record' ?></button>
                <a class="btn" href="merit-tracker.php">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>