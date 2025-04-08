<?php
require_once "../../db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../../SignUps/Clogin.php");
    exit();
}

$company_id = $_SESSION["user_id"];

try {
    // Fetch company name
    $companyStmt = $conn->prepare("SELECT company_name FROM companies WHERE company_id = ?");
    $companyStmt->execute([$company_id]);
    $company = $companyStmt->fetch(PDO::FETCH_ASSOC);
    $company_name = $company ? htmlspecialchars($company["company_name"]) : "Unknown Company";

    // Fetch opportunities
    $opportunitiesStmt = $conn->prepare("SELECT * FROM opportunities WHERE company_id = ?");
    $opportunitiesStmt->execute([$company_id]);
    $opportunities = $opportunitiesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch applications (UNCOMMENTED)
    $applicationsStmt = $conn->prepare("SELECT applications.*, students.full_name, opportunities.title 
                                        FROM applications 
                                        JOIN students ON applications.student_id = students.student_id
                                        JOIN opportunities ON applications.opportunities_id = opportunities.opportunities_id
                                        WHERE opportunities.company_id = ?");
    $applicationsStmt->execute([$company_id]);
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
    <title>Company Dashboard - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME - Company Portal</h2>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="../Company/CHome.php" class="nav-link text-white fw-bold fs-5 active">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="../Company/COpportunities.php" class="nav-link text-white fw-bold fs-5">📢 Opportunities</a></li>
                <li class="nav-item"><a href="../Company/CTrack.php" class="nav-link text-white fw-bold fs-5">📄 Applications</a></li>
                <li class="nav-item"><a href="../Company/CNotifications.php" class="nav-link text-white fw-bold fs-5">💬 Messages</a></li>
                <li class="nav-item"><a href="../Company/CProfile.php" class="nav-link text-white fw-bold fs-5">🏢 Profile</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container p-5 flex-grow-1"><br><br>
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
        <span class="text-white fw-bold fs-5">Welcome, <?php echo $company_name; ?>! 🏢</span>

            <p class="text-muted">Monitor your posted opportunities, applications, and engagement insights.</p>
        </header>
        
        <div class="row g-5 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-primary text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Total Opportunities</h5>
                    <h2 id="totalOpportunities" class="fw-bold fs-3"><?php echo count($opportunities); ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-success text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Total Applications</h5>
                    <h2 id="totalApplications" class="fw-bold fs-3"><?php echo count($applications); ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-warning text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Pending Applications</h5>
                    <h2 id="pendingApplications" class="fw-bold fs-3">
                        <?php echo count(array_filter($applications, fn($app) => $app["status"] === "Pending")); ?>
                    </h2>
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg"><br>
            <h5 class="fw-bold fs-5 mb-3">Recent Applications</h5>
            <table class="table table-striped">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>Student</th>
                        <th>Opportunity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="recentApplicationsTable">
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($application["full_name"]); ?></td>
                            <td><?php echo htmlspecialchars($application["title"]); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo ($application["status"] === "Accepted") ? "success" :
                                         (($application["status"] === "Pending") ? "warning" : "danger"); ?>">
                                    <?php echo htmlspecialchars($application["status"]); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-success">Accept</button>
                                <button class="btn btn-sm btn-outline-danger">Reject</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="#help" class="text-white fw-bold">Help Center</a>
            <a href="#terms" class="text-white fw-bold">Terms of Service</a>
            <a href="#contact" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javasggript/CHome.js"></script>
</body>
</html>
