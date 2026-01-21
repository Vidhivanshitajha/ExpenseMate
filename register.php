<?php
include "db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {

        /* Check if email already exists */
        $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");
        if (!$check) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Email already exists.";
        } else {

            /* Hash password */
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            /* Default values */
            $bank_balance = 30000;
            $daily_limit = 200;
            $monthly_limit_percent = 80;

            /* Insert user */
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users 
                (name, email, password, bank_balance, daily_limit, monthly_limit_percent)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                die("Prepare failed: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param(
                $stmt,
                "sssiii",
                $name,
                $email,
                $hashed,
                $bank_balance,
                $daily_limit,
                $monthly_limit_percent
            );

            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration successful! You can login now.";
            } else {
                $error = "Registration failed.";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f6f8;
            font-family: Arial, sans-serif;
        }

        .container {
            background: #ffffff;
            padding: 30px 40px;
            width: 380px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        input {
            width: 100%;
            padding: 8px;
            margin-top: 10px;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        a {
            text-decoration: none;
            color: #0066cc;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Register</h2>

    <p style="color:red;"><?php echo $error; ?></p>
    <p style="color:green;"><?php echo $success; ?></p>

    <form method="POST">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <p><a href="login.php">Go to Login</a></p>
</div>

</body>
</html>
