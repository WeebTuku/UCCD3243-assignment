<?php
include("auth.php");
include("database.php");

$student_id = $_SESSION['student_id'];
$id         = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load record for editing
$stmt = mysqli_prepare($con,
    "SELECT * FROM club_tracker WHERE club_tracker_id = ? AND student_id = ?"
);
mysqli_stmt_bind_param($stmt, 'ii', $id, $student_id);
mysqli_stmt_execute($stmt);
$res      = mysqli_stmt_get_result($stmt);
$editData = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$editData) {
    header("Location: club-tracker.php");
    exit();
}

// Update the record
if (isset($_POST['update'])) {
    $club_name = trim($_POST['club_name']);
    $club_role = trim($_POST['club_role']);
    $join_date = trim($_POST['join_date']);

    if (empty($club_name) || empty($club_role) || empty($join_date)) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'All fields are required.'];
        header("Location: club-tracker-form.php?id=$id");
        exit();
    }

    $stmt = mysqli_prepare($con,
        "UPDATE club_tracker SET club_name = ?, club_role = ?, join_date = ?
         WHERE club_tracker_id = ? AND student_id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'sssii', $club_name, $club_role, $join_date, $id, $student_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Club record updated successfully.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Failed to update club record. Please try again.'];
    }
    mysqli_stmt_close($stmt);

    header("Location: club-tracker.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Club Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Edit Club Tracker</h1>
    <div class="actions" style="margin-bottom:1rem;">
        <a class="btn" href="club-tracker.php">&larr; Back</a>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <?php $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
        <div class="form <?= $f['type'] === 'success' ? 'success' : 'error' ?>" style="max-width:100%;">
            <?= htmlspecialchars($f['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <form method="POST">
            <label>Club Name
                <input type="text" name="club_name" required
                       value="<?= htmlspecialchars($editData['club_name']) ?>">
            </label>

            <label>Role
                <select name="club_role" required>
                    <option value="President"      <?= $editData['club_role'] === 'President'      ? 'selected' : '' ?>>President</option>
                    <option value="Vice President" <?= $editData['club_role'] === 'Vice President' ? 'selected' : '' ?>>Vice President</option>
                    <option value="Treasurer"      <?= $editData['club_role'] === 'Treasurer'      ? 'selected' : '' ?>>Treasurer</option>
                    <option value="Secretary"      <?= $editData['club_role'] === 'Secretary'      ? 'selected' : '' ?>>Secretary</option>
                    <option value="Member"         <?= $editData['club_role'] === 'Member'         ? 'selected' : '' ?>>Member</option>
                </select>
            </label>

            <label>Join Date
                <input type="date" name="join_date" required
                       value="<?= htmlspecialchars($editData['join_date']) ?>">
            </label>

            <div class="actions">
                <button type="submit" name="update">Update Club Tracker</button>
                <a href="club-tracker.php" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>