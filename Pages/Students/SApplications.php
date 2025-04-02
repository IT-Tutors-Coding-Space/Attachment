<?php
require '../../db.php';
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location:  ../SignUps/Loginn.php");
    exit();
}

$student_id = $_SESSION["user_id"];
try {
    $stmt = $conn->prepare("SELECT * FROM applications WHERE student_id = ?"); // Fetch student applications
    $stmt->execute([$student_id]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>My Applications</h1>
    <table>
        <thead>
            <tr>
                <th>Opportunity</th>
                <th>Status</th>
                <th>Date Applied</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?php echo htmlspecialchars($application["opportunity_title"]); ?></td>
                    <td><?php echo htmlspecialchars($application["status"]); ?></td>
                    <td><?php echo htmlspecialchars($application["date_applied"]); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>