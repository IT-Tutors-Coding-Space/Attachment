<?php
<<<<<<< HEAD
$host = 'localhost';
$dbname = 'attachme'; // Adjust database name as needed
$username = 'root'; // Default WAMP MySQL username
$password = 'Attachme@Admin'; // Default WAMP MySQL password (empty)
=======
$host = "localhost";
$dbname = "attachme";
$username = "root";
$password = "";

>>>>>>> 6f3d3a023608c00074b8df5b85ab0c12241a24bd

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
