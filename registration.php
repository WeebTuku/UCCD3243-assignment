<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>User Registration</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <?php
        require('database.php');

        if (isset($_POST['student_name'])) {
            $student_name = trim($_POST['student_name']);
            $email        = trim($_POST['email']);
            $password     = $_POST['password'];
            $dob          = $_POST['dob'];

            // Check if email already exists
            $check = mysqli_prepare($con, "SELECT id FROM students WHERE email = ?");
            mysqli_stmt_bind_param($check, 's', $email);
            mysqli_stmt_execute($check);
            mysqli_stmt_store_result($check);

            if (mysqli_stmt_num_rows($check) > 0) {
                echo "<div class='form error'><h3>Email is already registered.</h3>
                      <br/>Click here to <a href='login.php'>Login</a></div>";
            } else {
                mysqli_stmt_close($check);
                $hashed = md5($password);
                $stmt = mysqli_prepare($con,
                    "INSERT INTO students (name, email, dob, _password) VALUES (?, ?, ?, ?)"
                );
                mysqli_stmt_bind_param($stmt, 'ssss', $student_name, $email, $dob, $hashed);
                $result = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                if ($result) {
                    echo "<div class='form success'>
                            <h3>You are registered successfully.</h3>
                            <br/>Click here to <a href='login.php'>Login</a>
                          </div>";
                } else {
                    echo "<div class='form error'><h3>Registration failed. Please try again.</h3></div>";
                }
            }
        } else {
            ?>
            <div class="panel">
                <div class="form">
                    <h1>User Registration</h1>
                    <form name="registration" action="" method="post">
                        <label for="student_name">Full Name</label>
                        <input type="text" name="student_name" placeholder="Your Full Name" required /><br>
                        <label for="email">Email</label>
                        <input type="email" name="email" placeholder="Email" required /><br>
                        <label for="password">Password</label>
                        <input type="password" name="password" placeholder="Password" required /><br>
                        <label for="dob">Date of Birth</label>
                        <input type="date" name="dob" required /><br>
                        <input type="submit" name="submit" value="Register" />
                    </form>
                    <p>Already registered? <a href='login.php'>Login Here</a></p>
                </div>
            </div>
        <?php } ?>
    </div>
</body>

</html>