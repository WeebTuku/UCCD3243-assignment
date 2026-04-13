<?php
require('database.php');

$message   = '';
$show_form = true;
$email     = '';

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);

    $stmt = mysqli_prepare($con, "SELECT * FROM password_resets WHERE token = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $token);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if ($row) {
        $email = $row['email'];

        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['password'])) {
            $password        = $_POST['password'];
            $hashed_password = md5($password);

            $upd = mysqli_prepare($con, "UPDATE students SET _password = ? WHERE email = ?");
            mysqli_stmt_bind_param($upd, 'ss', $hashed_password, $email);
            mysqli_stmt_execute($upd);
            mysqli_stmt_close($upd);

            $del = mysqli_prepare($con, "DELETE FROM password_resets WHERE email = ?");
            mysqli_stmt_bind_param($del, 's', $email);
            mysqli_stmt_execute($del);
            mysqli_stmt_close($del);

            header("Location: login.php?reset_success=1");
            exit();
        }
    } else {
        $message   = 'Invalid or expired token.';
        $show_form = false;
    }
} else {
    $message   = 'No reset token provided.';
    $show_form = false;
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="form">
        <h1>Password Reset</h1>
        <?php if (!empty($message)): ?>
            <div class="error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($show_form): ?>
        <form action="" method="post">
            <input type="password" name="password" placeholder="Enter new password" required /><br>
            <input name="submit" type="submit" value="Reset Password" />
        </form>
        <?php endif; ?>
        <p><a href="login.php">Back to Login</a></p>
    </div>
</div>
</body>
</html>