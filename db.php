<?php
$host = 'localhost';
$dbname = 'attachme'; // Adjust database name as needed
$username = 'root'; // Default WAMP MySQL username
$password = 'Attachme@Admin'; // Default WAMP MySQL password (empty)

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
