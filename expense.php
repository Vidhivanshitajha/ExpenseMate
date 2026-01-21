<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("db.php");

// Get logged-in user id
$user_id = $_SESSION['user_id'];

// When form is submitted
if (isset($_POST['submit'])) {

    $category_id = $_POST['category_id'];
    $amount = $_POST['amount'];

    // Insert expense
    $sql = "INSERT INTO expenses (user_id, category_id, amount)
            VALUES ('$user_id', '$category_id', '$amount')";
    echo "<br><a href='dashboard.php'>Back to Dashboard</a>";

    if (mysqli_query($conn, $sql)) {
        echo "Expense added successfully";
    } else {
        echo "Error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Expense</title>
</head>
<body>

<h2>Add Expense</h2>

<form method="POST">
    Category:
    <select name="category_id">
        <option value="1">Food</option>
        <option value="2">Travel</option>
        <option value="3">Shopping</option>
    </select>
    <br><br>

    Amount:
    <input type="number" name="amount">
    <br><br>

    <button type="submit" name="submit">Add</button>
</form>

</body>
</html>