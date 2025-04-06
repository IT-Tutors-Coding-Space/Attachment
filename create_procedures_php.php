<?php
require 'db.php';

$sql = file_get_contents('recreate_procedures.sql');

try {
    // Split the SQL by delimiter
    $queries = explode('DELIMITER $$', $sql);
    $queries = array_filter($queries);
    
    foreach ($queries as $query) {
        // Remove DELIMITER statements and execute each query
        $query = str_replace(['DELIMITER $$', 'DELIMITER ;'], '', $query);
        if (!empty(trim($query))) {
            $conn->exec($query);
        }
    }
    
    echo "<h2>Procedure Creation Results</h2>";
    echo "<p style='color:green'>✓ Logging procedures created successfully</p>";
    
    // Verify creation
    $procedures = ['LogSystemEvent', 'LogUserActivity', 'LogError'];
    foreach ($procedures as $procedure) {
        $stmt = $conn->query("SHOW PROCEDURE STATUS WHERE Db = 'attachme' AND Name = '$procedure'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color:green'>✓ $procedure exists</p>";
        } else {
            echo "<p style='color:red'>✗ $procedure creation failed</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<h2>Error Creating Procedures</h2>";
    echo "<p style='color:red'>" . $e->getMessage() . "</p>";
    echo "<p>Check database permissions and try again.</p>";
}
?>
