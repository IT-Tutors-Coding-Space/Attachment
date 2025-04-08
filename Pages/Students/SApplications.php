<?php
require '../../db.php';
require_once '../auth.php';
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../SignUps/Slogin.php");
    exit();
}

$student_id = $_SESSION["user_id"];
try {
    $stmt = $conn->prepare("
        SELECT a.*, o.title, o.company_name 
        FROM applications a
        JOIN opportunities o ON a.opportunities_id = o.opportunities_id
        WHERE a.student_id = ?
        ORDER BY a.submitted_at DESC
    ");
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
    <title>My Applications | AttachMe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../CSS/styles.css">
</head>
<body>
    <?php include '../includes/student-nav.php'; ?>
    
    <div class="container mt-4">
        <div class="application-container">
            <h2 class="mb-4">My Applications</h2>
            
            <?php if (empty($applications)): ?>
                <div class="alert alert-info">
                    You haven't submitted any applications yet.
                    <a href="SBrowse.php" class="btn btn-primary mt-2">Browse Opportunities</a>
                </div>
            <?php else: ?>
                <div class="application-list">
                    <?php foreach ($applications as $app): ?>
                    <div class="application-item">
                        <div class="application-header">
                            <h4><?= htmlspecialchars($app['title']) ?></h4>
                            <span class="company"><?= htmlspecialchars($app['company_name']) ?></span>
                        </div>
                        <div class="application-details">
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span class="status-badge status-<?= strtolower($app['status']) ?>">
                                    <?= htmlspecialchars($app['status']) ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Applied:</span>
                                <span><?= date('M j, Y', strtotime($app['submitted_at'])) ?></span>
                            </div>
                        </div>
                        <div class="application-actions">
                            <a href="view_application.php?id=<?= $app['application_id'] ?>" 
                               class="btn btn-outline-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
