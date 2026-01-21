<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | Splitwise Clone</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
        }
        .box {
            width: 500px;
            margin: 100px auto;
            background: white;
            padding: 25px;
            border-radius: 5px;
            text-align: center;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: blue;
        }
    </style>
</head>
<body>

<div class="box">

<?php
// Check if user is logged in
if (isset($_SESSION["user_id"])) {
    echo "<h2>Welcome to Splitwise Clone Dashboard</h2>";
    echo "<p>You are successfully logged in.</p>";
    echo "<a href='expense.php'>Add Expense</a><br>";
    echo "<a href='logout.php'>Logout</a>";
} else {
    echo "<h2>Access Denied</h2>";
    echo "<p>You must log in to access this page.</p>";
    echo "<a href='login.php'>Go to Login</a>";
}
?>

</div>

</body>
</html>
