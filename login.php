<?php
session_start();

// Auto-login if remember_me cookie exists
require('database.php');
if (!isset($_SESSION['student_id']) && isset($_COOKIE['remember_email'])) {
    $email = $_COOKIE['remember_email'];
    $stmt  = mysqli_prepare($con, "SELECT * FROM students WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    if ($user) {
        $_SESSION['student_name'] = $user['name'];
        $_SESSION['student_id']   = $user['id'];
        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Login</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php
        if (!isset($con)) require('database.php');

        if (isset($_GET['reset_success'])) {
            echo "<p style='color:green;text-align:center;'>Password reset successful! You can now log in.</p>";
        }

        if (isset($_POST['email'])) {
            $email    = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                echo "<div class='form error'><h3>Please fill in all fields.</h3></div>";
            } else {
                $stmt   = mysqli_prepare($con, "SELECT * FROM students WHERE email = ? AND _password = ?");
                $hashed = md5($password);
                mysqli_stmt_bind_param($stmt, 'ss', $email, $hashed);
                mysqli_stmt_execute($stmt);
                $res  = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($res);
                mysqli_stmt_close($stmt);

                if ($user) {
                    $_SESSION['student_name'] = $user['name'];
                    $_SESSION['student_id']   = $user['id'];

                    // Remember Me cookie — 30 days
                    if (isset($_POST['remember_me'])) {
                        setcookie('remember_email', $email, time() + (86400 * 30), '/');
                    } else {
                        // Clear cookie if not checked
                        setcookie('remember_email', '', time() - 3600, '/');
                    }

                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo "<div class='form error'><h3>Email or password is incorrect.</h3>
                          <br/>Click here to <a href='login.php'>try again</a></div>";
                }
            }
        } else {
            $remembered_email = isset($_COOKIE['remember_email']) ? htmlspecialchars($_COOKIE['remember_email']) : '';
            ?>
            <div class="form">
                <h1>User Log In</h1>
                <form action="" method="post" name="login">
                    <input type="email" name="email" placeholder="Your Email"
                           value="<?= $remembered_email ?>" required /><br>
                    <input type="password" name="password" placeholder="Password" required /><br>
                    <label style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <input type="checkbox" name="remember_me"
                               <?= $remembered_email ? 'checked' : '' ?>
                               style="width:auto;display:inline;margin:0;">
                        Remember Me
                    </label>
                    <input name="submit" type="submit" value="Login" />
                </form>
                <p>Not registered yet? <a href='registration.php'>Register Here</a></p>
                <p>Forgot password? <a href='forgot_password.php'>Click here</a></p>
            </div>
        <?php } ?>
    </div>
</body>
</html>