<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Fetch user info */
$userQuery = "
    SELECT name, bank_balance, daily_limit, monthly_limit_percent
    FROM users
    WHERE user_id = '$user_id'
";

$userResult = mysqli_query($conn, $userQuery);
if (!$userResult) {
    die("User query failed: " . mysqli_error($conn));
}
$user = mysqli_fetch_assoc($userResult);

/* Fetch today's expense */
$todayQuery = "
    SELECT IFNULL(SUM(amount),0) AS total
    FROM expense
    WHERE user_id = '$user_id'
    AND expense_date = CURDATE()
";

$todayResult = mysqli_query($conn, $todayQuery);
if (!$todayResult) {
    die("Today expense query failed: " . mysqli_error($conn));
}
$today = mysqli_fetch_assoc($todayResult);

/* Fetch monthly expense */
$monthQuery = "
    SELECT IFNULL(SUM(amount),0) AS total
    FROM expense
    WHERE user_id = '$user_id'
    AND MONTH(expense_date) = MONTH(CURDATE())
    AND YEAR(expense_date) = YEAR(CURDATE())
";

$monthResult = mysqli_query($conn, $monthQuery);
if (!$monthResult) {
    die("Monthly expense query failed: " . mysqli_error($conn));
}
$month = mysqli_fetch_assoc($monthResult);

/* Monthly limit calculation */
$monthly_limit = ($user['monthly_limit_percent'] / 100) * $user['bank_balance'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>

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
            width: 420px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        .alert {
            color: red;
            font-weight: bold;
        }

        a {
            display: block;
            margin-top: 10px;
            text-decoration: none;
            color: #0066cc;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>

    <h3>Summary</h3>
    <p>Today's Expense: ₹<?php echo $today['total']; ?></p>
    <p>Monthly Expense: ₹<?php echo $month['total']; ?></p>

    <h3>Limits</h3>
    <p>Daily Limit: ₹<?php echo $user['daily_limit']; ?></p>
    <p>Monthly Limit: ₹<?php echo $monthly_limit; ?></p>

    <?php
    if ($today['total'] > $user['daily_limit']) {
        echo "<p class='alert'>Daily limit exceeded!</p>";
    }

    if ($month['total'] > $monthly_limit) {
        echo "<p class='alert'>Monthly limit exceeded!</p>";
    }
    ?>

    <a href="expenseform.php">Add Expense</a>
    <a href="logout.php">Logout</a>
</div>

</body>
</html>

