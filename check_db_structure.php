<?php
require_once "db.php";

try {
    // Check applications table structure
    $stmt = $conn->query("DESCRIBE applications");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Applications table structure:\n";
    print_r($structure);
    
    // Check if uploads directory exists and is writable
    $upload_dir = "../uploads/resumes/";
    if (!file_exists($upload_dir)) {
        echo "\nUploads directory does not exist. Attempting to create...\n";
        if (mkdir($upload_dir, 0777, true)) {
            echo "Uploads directory created successfully\n";
        } else {
            echo "Failed to create uploads directory\n";
        }
    } else {
        echo "\nUploads directory exists\n";
    }
    
    // Test if directory is writable
    $test_file = $upload_dir . 'test_write.txt';
    if (file_put_contents($test_file, 'test') !== false) {
        unlink($test_file);
        echo "Uploads directory is writable\n";
    } else {
        echo "Uploads directory is not writable\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
