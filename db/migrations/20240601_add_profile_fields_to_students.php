<?php
require_once "../../db.php";

try {
    // Add profile_picture column
    $conn->exec("
        ALTER TABLE students
        ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL
    ");

    // Add password column
    $conn->exec("
        ALTER TABLE students
        ADD COLUMN password VARCHAR(255) NOT NULL
    ");

    echo "Migration successful: Added profile_picture and password fields to students table\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
