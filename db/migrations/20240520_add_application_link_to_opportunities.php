<?php
require_once "../db.php";

try {
    $conn->exec("ALTER TABLE opportunities ADD COLUMN application_link VARCHAR(255)");
    echo "Successfully added application_link column to opportunities table";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
