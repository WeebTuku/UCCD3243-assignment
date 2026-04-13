<?php
include("auth.php");
include("database.php");

$student_id = $_SESSION['student_id'];

// Add New Club Tracker
if (isset($_POST['new'])) {
    $club_name = trim($_POST['club_name']);
    $club_role = trim($_POST['club_role']);
    $join_date = trim($_POST['join_date']);

    if (empty($club_name) || empty($club_role) || empty($join_date)) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'All fields are required.'];
        header("Location: club-tracker.php");
        exit();
    }

    $stmt = mysqli_prepare($con,
        "INSERT INTO club_tracker (student_id, club_name, club_role, join_date) VALUES (?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'isss', $student_id, $club_name, $club_role, $join_date);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Club added successfully.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Failed to add club. Please try again.'];
    }
    mysqli_stmt_close($stmt);

    header("Location: club-tracker.php");
    exit();
}

// Delete Selected Club Tracker
if (isset($_GET['delete'])) {
    $id   = (int)$_GET['delete'];
    $stmt = mysqli_prepare($con,
        "DELETE FROM club_tracker WHERE club_tracker_id = ? AND student_id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'ii', $id, $student_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Club record deleted.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Failed to delete club record.'];
    }
    mysqli_stmt_close($stmt);

    header("Location: club-tracker.php");
    exit();
}

// Fetch records for display
$stmt = mysqli_prepare($con,
    "SELECT * FROM club_tracker WHERE student_id = ? ORDER BY club_tracker_id DESC"
);
mysqli_stmt_bind_param($stmt, 'i', $student_id);
mysqli_stmt_execute($stmt);
$view_result = mysqli_stmt_get_result($stmt);
$clubs = [];
while ($row = mysqli_fetch_assoc($view_result)) {
    $clubs[] = $row;
}
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Club Tracker</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Club Tracker</h1>
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
        <h2>Add New Club</h2>
        <form method="POST">
            <label>Club Name
                <input type="text" name="club_name" required>
            </label>

            <label>Role
                <select name="club_role" required>
                    <option value="">-- Select Role --</option>
                    <option value="President">President</option>
                    <option value="Vice President">Vice President</option>
                    <option value="Treasurer">Treasurer</option>
                    <option value="Secretary">Secretary</option>
                    <option value="Member">Member</option>
                </select>
            </label>

            <label>Join Date
                <input type="date" name="join_date" required>
            </label>

            <div class="actions">
                <button type="submit" name="new">Add Club</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <h2>Membership Information</h2>
        <?php if (count($clubs) === 0): ?>
            <p class="muted">No club memberships recorded yet.</p>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>Club Name</th>
                    <th>Role</th>
                    <th>Join Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($clubs as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['club_name']) ?></td>
                        <td><?= htmlspecialchars($row['club_role']) ?></td>
                        <td><?= htmlspecialchars($row['join_date']) ?></td>
                        <td class="nowrap">
                            <a class="btn" href="club-tracker-form.php?id=<?= $row['club_tracker_id'] ?>">Edit</a>
                            <a class="btn danger"
                               href="?delete=<?= $row['club_tracker_id'] ?>"
                               onclick="return confirm('Delete this record?')">Delete</a>
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