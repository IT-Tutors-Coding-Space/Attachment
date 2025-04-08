<?php
require_once "db.php";

try {
    // Check if messages table exists
    $stmt = $conn->query("DESCRIBE messages");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Messages table structure:\n";
    print_r($structure);
    
} catch (PDOException $e) {
    if ($e->getCode() == '42S02') {
        echo "Messages table does not exist\n";
    } else {
        echo "Database error: " . $e->getMessage();
    }
}
?>
