<?php
require_once "../../db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location:  ../SignUps/SLogin.php");
    exit();
}

$student_id = $_SESSION["user_id"];
try {
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    $applicationsStmt = $conn->prepare("SELECT * FROM applications WHERE student_id = ?");
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
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/student-styles.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME - Student Portal</h2>
            <ul class="navbar-nav d-flex flex-row gap-3">
                <li class="nav-item"><a href="../Students/SDashboard.php" class="nav-link text-white  fs-5 active">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="../Students/SAbout.php" class="nav-link text-white  fs-5 active">📖 About Us</a></li>

                <li class="nav-item"><a href="../Students/SBrowse.php" class="nav-link text-white  fs-5">🔍 Browse Opportunities</a></li>
                <li class="nav-item"><a href="../Students/SApplicationSubmission.php" class="nav-link text-white  fs-5">📄 My Applications</a></li>
                <li class="nav-item"><a href="../Students/SNotifications.php" class="nav-link text-white  fs-5">💬 Messages</a></li>
                <li class="nav-item"><a href="../Students/SProfile.php" class="nav-link text-white  fs-5">👤 Profile</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">Welcome, <?php echo htmlspecialchars($student["full_name"] ?? "Guest"); ?>!</h1>
            <p class="text-muted">Track your applications, explore new opportunities, and manage your profile.</p>
        </header><br><br><br>

        <!-- Dashboard Overview Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-primary text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Total Applications</h5>
                    <h2 id="totalApplications" class="fw-bold fs-3">0</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-success text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Accepted Applications</h5>
                    <h2 id="acceptedApplications" class="fw-bold fs-3">0</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-warning text-white rounded-lg text-center">
                    <h5 class="fw-bold fs-5">Pending Applications</h5>
                    <h2 id="pendingApplications" class="fw-bold fs-3">0</h2>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="recentApplicationsTable">
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($application["title"]); ?></td>
                            <td><?php echo htmlspecialchars($application["company_name"]); ?></td>
                            <td><span class="badge bg-<?php echo $application["status"] === "Accepted" ? "success" : ($application["status"] === "Pending" ? "warning" : "danger"); ?>">
                                <?php echo htmlspecialchars($application["status"]); ?>
                            </span></td>
                            <td><button class="btn btn-sm btn-outline-secondary">View</button></td>
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
            <a href="Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Students/Terms of service.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../Students/Contact Support.php" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javascript/SDashboard.js"></script>
</body>
</html>
