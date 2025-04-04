<?php
require_once "db.php";

try {
    // Check applications table structure
    $stmt = $conn->query("DESCRIBE applications");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Applications Table Structure</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Check if required fields exist
    $required = ['application_id', 'user_id', 'opportunity_id', 'status', 'application_date', 'cover_letter'];
    $missing = [];
    foreach ($required as $field) {
        if (!in_array($field, array_column($columns, 'Field'))) {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        echo "<h3>Missing Required Fields:</h3>";
        echo "<ul>";
        foreach ($missing as $field) {
            echo "<li>$field</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>All required fields are present</p>";
    }

} catch (PDOException $e) {
    die("Error checking database: " . $e->getMessage());
}
?>
