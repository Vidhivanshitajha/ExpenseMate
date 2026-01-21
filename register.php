<?php
include "db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {

        // CHECK EMAIL QUERY
        $sql = "SELECT id FROM users WHERE email = ?";
        $check = mysqli_prepare($conn, $sql);

        if (!$check) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Email already registered.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = mysqli_prepare(
                $conn,
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );

            if (!$insert) {
                die("Prepare failed: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($insert, "sss", $name, $email, $hashed_password);

            if (mysqli_stmt_execute($insert)) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed.";
            }

            mysqli_stmt_close($insert);
        }

        mysqli_stmt_close($check);
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Register | Splitwise Clone</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; }
        .box {
            width: 350px;
            margin: 100px auto;
            background: white;
            padding: 25px;
            border-radius: 5px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

<div class="box">
    <h2>Create Account</h2>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?php echo $success; ?></p>
        <p><a href="login.php">Go to Login</a></p>
    <?php else: ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
