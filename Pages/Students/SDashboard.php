<?php
require_once "../../db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location:  ../SignUps/SLogin.php");
    exit();
}

$student_id = $_SESSION["user_id"];
require "../../Components/StudentNav.php";

try {
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get application counts
    $countStmt = $conn->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Accepted' THEN 1 ELSE 0 END) as accepted,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending
        FROM applications 
        WHERE student_id = ?
    ");
    $countStmt->execute([$student_id]);
    $counts = $countStmt->fetch(PDO::FETCH_ASSOC);

    // Get application details
    $applicationsStmt = $conn->prepare("
        SELECT a.*, o.title, c.company_name 
        FROM applications a
        LEFT JOIN opportunities o ON a.opportunities_id = o.opportunities_id
        LEFT JOIN companies c ON o.company_id = c.company_id
        WHERE a.student_id = ?
        ORDER BY a.submitted_at DESC
        LIMIT 10
    ");
    $applicationsStmt->execute([$student_id]);
    $applications = $applicationsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttachME Student Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../CSS/styles.css">
</head>
<body>
    <main class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h3 class="text-3xl fw-bold">Welcome, <?php echo htmlspecialchars($student["full_name"] ?? "Guest"); ?>!</h3>
            <p class="text-muted">Track your applications, explore new opportunities, and manage your profile.</p>
        </header><br><br><br>

        <!-- Dashboard Overview Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-primary text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Total Applications</h5>
                    <h2 id="totalApplications" class="fw-bold fs-3"><?php echo $counts['total'] ?? 0; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-success text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Accepted Applications</h5>
                    <h2 id="acceptedApplications" class="fw-bold fs-3"><?php echo $counts['accepted'] ?? 0; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-warning text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Pending Applications</h5>
                    <h2 id="pendingApplications" class="fw-bold fs-3"><?php echo $counts['pending'] ?? 0; ?></h2>
                </div>
            </div>
        </div><br><br><br>

        <!-- Recent Applications -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <h5 class="fw-bold fs-5 mb-3">My Recent Applications</h5>
            <table class="table table-striped">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>Opportunity</th>
                        <th>Company</th>
                        <th>Status</th>
                        <!-- <th>Actions</th> -->
                    </tr>
                </thead>
                <tbody id="recentApplicationsTable">
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?php echo isset($application["title"]) ? htmlspecialchars($application["title"]) : "Opportunity Not Found"; ?></td>
                            <td><?php echo isset($application["company_name"]) ? htmlspecialchars($application["company_name"]) : "Company Not Found"; ?></td>
                            <td><span class="badge bg-<?php echo $application["status"] === "Accepted" ? "success" : ($application["status"] === "Pending" ? "warning" : "danger"); ?>">
                                <?php echo htmlspecialchars($application["status"]); ?>
                            </span></td>
                            <!-- <td><button class="btn btn-sm btn-outline-secondary">View</button></td> -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <?php require "../../Components/StudentFooter.php"; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javasript/SDashboard.js"></script>
</body>
</html>
