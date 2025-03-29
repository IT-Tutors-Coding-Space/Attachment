<?php
$host = "localhost";
$dbname = "attachme";
$username = "root";
$password = "Attachme@Admin";

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query the tables
    $tables = ['admins', 'companies', 'students'];
    foreach ($tables as $table) {
        echo "Contents of $table:\n";
        $stmt = $conn->query("SELECT * FROM $table");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($results);
        echo "\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
