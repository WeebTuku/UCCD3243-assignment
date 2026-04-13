<?php
require('database.php');

// Create password_resets table if it doesn't exist
mysqli_query($con, "CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$message = '';
$msg_type = '';

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);

    $stmt = mysqli_prepare($con, "SELECT id FROM students WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        $token = bin2hex(random_bytes(50));

        // Delete any existing token for this email first
        $del = mysqli_prepare($con, "DELETE FROM password_resets WHERE email = ?");
        mysqli_stmt_bind_param($del, 's', $email);
        mysqli_stmt_execute($del);
        mysqli_stmt_close($del);

        $ins = mysqli_prepare($con, "INSERT INTO password_resets (email, token) VALUES (?, ?)");
        mysqli_stmt_bind_param($ins, 'ss', $email, $token);
        mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);

        $message  = "Password reset link: <a href='reset_password.php?token=$token'>Reset Password</a>";
        $msg_type = 'success';
    } else {
        mysqli_stmt_close($stmt);
        $message  = "Email not found.";
        $msg_type = 'error';
    }
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forgot Password</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <div class="form">
        <h1>Forgot Password</h1>
        <?php if (!empty($message)): ?>
            <div class="<?= $msg_type ?>"><?= $message ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <input type="email" name="email" placeholder="Enter your email" required /><br>
            <input name="submit" type="submit" value="Submit" />
        </form>
        <p><a href="login.php">Back to Login</a></p>
    </div>
</div>

</body>
</html>