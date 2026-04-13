<?php
require 'database.php';
include 'auth.php';

$id         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title            = trim($_POST['title']            ?? '');
    $achievement_type = trim($_POST['achievement_type'] ?? '');
    $date_received    = trim($_POST['date_received']    ?? '');
    $organisation     = trim($_POST['organisation']     ?? '');
    $description      = trim($_POST['description']      ?? '');
    $post_id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if (empty($title) || empty($achievement_type) || empty($date_received) || empty($organisation) || empty($description)) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'All fields are required.'];
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if ($post_id > 0) {
        $stmt = mysqli_prepare($con,
            'UPDATE achievements
             SET title = ?, achievement_type = ?, date_received = ?, organisation = ?, description = ?
             WHERE id = ? AND student_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'sssssii',
            $title, $achievement_type, $date_received, $organisation, $description, $post_id, $student_id
        );
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Achievement updated successfully.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Failed to update achievement. Please try again.'];
        }
        mysqli_stmt_close($stmt);
    } else {
        $stmt = mysqli_prepare($con,
            'INSERT INTO achievements (student_id, title, achievement_type, date_received, organisation, description)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'isssss',
            $student_id, $title, $achievement_type, $date_received, $organisation, $description
        );
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Achievement added successfully.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Failed to add achievement. Please try again.'];
        }
        mysqli_stmt_close($stmt);
    }

    header('Location: achievement-tracker.php');
    exit;
}

$achievement = null;
if ($id > 0) {
    $stmt = mysqli_prepare($con, 'SELECT * FROM achievements WHERE id = ? AND student_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $id, $student_id);
    mysqli_stmt_execute($stmt);
    $res         = mysqli_stmt_get_result($stmt);
    $achievement = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$achievement) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Achievement not found or access denied.'];
        header('Location: achievement-tracker.php');
        exit;
    }
}

$types = ['Award', 'Certificate', 'Recognition', 'Scholarship', 'Other'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $id > 0 ? 'Edit Achievement' : 'Add Achievement' ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1><?= $id > 0 ? 'Edit Achievement' : 'Add Achievement' ?></h1>

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

            <label>Title
                <input name="title"
                       value="<?= htmlspecialchars($achievement['title'] ?? '') ?>"
                       required>
            </label>

            <label>Achievement Type
                <select name="achievement_type" required>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= $t ?>"
                            <?= (isset($achievement['achievement_type']) && $achievement['achievement_type'] === $t) ? 'selected' : '' ?>>
                            <?= $t ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>Date Received
                <input type="date" name="date_received"
                       value="<?= htmlspecialchars($achievement['date_received'] ?? '') ?>"
                       required>
            </label>

            <label>Issuing Organisation
                <input name="organisation"
                       value="<?= htmlspecialchars($achievement['organisation'] ?? '') ?>"
                       required>
            </label>

            <label>Description
                <textarea name="description" required><?= htmlspecialchars($achievement['description'] ?? '') ?></textarea>
            </label>

            <div class="actions">
                <button type="submit"><?= $id > 0 ? 'Save Changes' : 'Add Achievement' ?></button>
                <a class="btn" href="achievement-tracker.php">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>