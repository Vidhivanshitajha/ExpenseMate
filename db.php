<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "expensemate";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connection successful<br>";
$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);
echo "<pre>";
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    print_r($row);
}
echo "</pre>";
?>
