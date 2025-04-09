<?php
require_once "db.php";

try {
    $stmt = $conn->query("DESCRIBE messages");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($columns, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
