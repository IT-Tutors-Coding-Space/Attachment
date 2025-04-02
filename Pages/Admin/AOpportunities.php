<?php
require_once "../../db.php";
session_start();

// Check if the user is logged in
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: Slogin.php"); // Redirect to login page if not authenticated
//     exit();
// }
// ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttachME Admin - Opportunities</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/admin-styles.css">
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME</h2>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="../Admin/AHome.php" class="nav-link text-white fw-bold fs-5">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="../Admin/AUsers.php" class="nav-link text-white fw-bold fs-5">👤 Users</a></li>
                <li class="nav-item"><a href="../Admin/ACompanies.php" class="nav-link text-white fw-bold fs-5">🏢 Companies</a></li>
                <li class="nav-item"><a href="../Admin/AOpportunities.php" class="nav-link text-white fw-bold fs-5 active">📢 Opportunities</a></li>
                <li class="nav-item"><a href="../Admin/AApplications.php" class="nav-link text-white fw-bold fs-5">📄 Applications</a></li>
                <li class="nav-item"><a href="../Admin/AAnalytics.php" class="nav-link text-white fw-bold fs-5">📊 Analytics</a></li>
                <li class="nav-item"><a href="../Admin/ASettings.php" class="nav-link text-white fw-bold fs-5">⚙️ Settings</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">Manage Opportunities</h1>
            <div class="d-flex align-items-center gap-3">
                <input type="text" class="form-control w-50" id="searchOpportunities" placeholder="Search opportunities...">
                <button class="btn btn-primary fw-bold fs-5" id="addOpportunity">+ Post Opportunity</button>
            </div>
        </header>

        <!-- Opportunities Table -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <h5 class="fw-bold fs-5 mb-3">Opportunities List</h5>
            <p class="text-muted">Below is a list of available attachment opportunities. You can filter, edit, or remove them as needed.</p>
            <table class="table table-striped">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>Company</th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Deadline</th>
                        <th>Slots</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="opportunityTableBody">
                <?php
                    $opportunitiesStmt = $conn->query("SELECT * FROM opportunities");
                    while ($opportunity = $opportunitiesStmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>{$opportunity['company']}</td>
                                <td>{$opportunity['title']}</td>
                                <td>{$opportunity['location']}</td>
                                <td>{$opportunity['deadline']}</td>
                                <td>{$opportunity['slots']}</td>
                                <td><span class='badge " . ($opportunity['status'] == 'Open' ? 'bg-success' : 'bg-danger') . "'>{$opportunity['status']}</span></td>
                                <td>
                                    <button class='btn btn-sm btn-outline-warning'>Edit</button>
                                    <button class='btn btn-sm btn-outline-danger'>Delete</button>
                                </td>
                              </tr>";
                    }
                ?>
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
    <script src="../../Javasript/AOpportunities.js"></script>
</body>
</html>
