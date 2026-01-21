<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

/* Fetch categories */
$catResult = mysqli_query($conn, "SELECT * FROM categories");
if (!$catResult) {
    die("Category query failed: " . mysqli_error($conn));
}

/* Handle form submit */
if (isset($_POST['add_expense'])) {

    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $desc = $_POST['description'];

    /* Fetch user limits */
    $userResult = mysqli_query(
        $conn,
        "SELECT bank_balance, daily_limit, monthly_limit_percent
         FROM users WHERE user_id = '$user_id'"
    );

    if (!$userResult) {
        die("User fetch failed: " . mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($userResult);

    /* Today's expense */
    $todayResult = mysqli_query(
        $conn,
        "SELECT IFNULL(SUM(amount),0) AS total
         FROM expense
         WHERE user_id = '$user_id'
         AND expense_date = CURDATE()"
    );

    $today = mysqli_fetch_assoc($todayResult);

    if (($today['total'] + $amount) > $user['daily_limit']) {
        $message .= "Daily limit exceeded! ";
    }

    /* Monthly expense */
    $monthResult = mysqli_query(
        $conn,
        "SELECT IFNULL(SUM(amount),0) AS total
         FROM expense
         WHERE user_id = '$user_id'
         AND MONTH(expense_date) = MONTH(CURDATE())
         AND YEAR(expense_date) = YEAR(CURDATE())"
    );

    $month = mysqli_fetch_assoc($monthResult);

    $monthly_limit = ($user['monthly_limit_percent'] / 100) * $user['bank_balance'];

    if (($month['total'] + $amount) > $monthly_limit) {
        $message .= "Monthly limit exceeded!";
    }

    /* Insert expense */
    mysqli_query(
        $conn,
        "INSERT INTO expense (user_id, category_id, amount, expense_date, description)
         VALUES ('$user_id', '$category', '$amount', '$date', '$desc')"
    );

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Expense</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .box {
            width: 420px;
            background: #fff;
            padding: 25px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px;
        }

        button {
            padding: 10px 20px;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Add Expense</h2>

    <?php if ($message != "") { ?>
        <p class="error"><?php echo $message; ?></p>
    <?php } ?>

    <form method="POST">

        <select name="category" required>
            <?php while ($row = mysqli_fetch_assoc($catResult)) { ?>
                <option value="<?php echo $row['category_id']; ?>">
                    <?php echo $row['category_name']; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <input type="number" name="amount" placeholder="Amount" required><br><br>
        <input type="date" name="date" required><br><br>
        <textarea name="description" placeholder="Description"></textarea><br><br>

        <button type="submit" name="add_expense">Add Expense</button>
    </form>
</div>

</body>
</html>