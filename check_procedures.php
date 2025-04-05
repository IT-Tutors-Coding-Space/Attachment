<?php
require 'db.php';

try {
    // Check if procedures exist
    $procedures = ['LogSystemEvent', 'LogUserActivity', 'LogError'];
    $results = [];

    foreach ($procedures as $procedure) {
        $stmt = $conn->query("SHOW PROCEDURE STATUS WHERE Db = 'attachme' AND Name = '$procedure'");
        $exists = $stmt->rowCount() > 0;
        $results[$procedure] = $exists;
    }

    // Display results
    echo "<h2>Procedure Verification</h2>";
    foreach ($results as $name => $exists) {
        $color = $exists ? 'green' : 'red';
        $status = $exists ? '✓ Exists' : '✗ Missing';
        echo "<p style='color:$color'>$name: $status</p>";
    }

    // If any are missing, show recreation option
    if (in_array(false, $results)) {
        echo "<h3>Next Steps</h3>";
        echo "<p>Some logging procedures are missing. Would you like to:</p>";
        echo "<ol>";
        echo "<li>Recreate the missing procedures</li>";
        echo "<li>Check the database permissions</li>";
        echo "<li>View the procedure creation SQL</li>";
        echo "</ol>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red'>Error checking procedures: " . $e->getMessage() . "</p>";
}
?>
