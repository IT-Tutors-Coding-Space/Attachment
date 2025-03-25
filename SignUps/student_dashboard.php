<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: Login.html");
    exit();
}

echo "<h2>Welcome, Student!</h2>";
echo "<a href='logout.php'>Logout</a>";
?>
