<?php
require_once "../../db.php";
session_start();

if ($_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/Alogin.php");
    exit();
}

try {
    $totalUsersStmt = $conn->query("SELECT COUNT(*) AS total FROM users");
    $totalUsers = $totalUsersStmt->fetch(PDO::FETCH_ASSOC)["total"];

    $totalCompaniesStmt = $conn->query("SELECT COUNT(*) AS total FROM companies");
    $totalCompanies = $totalCompaniesStmt->fetch(PDO::FETCH_ASSOC)["total"];

    $totalOpportunitiesStmt = $conn->query("SELECT COUNT(*) AS total FROM opportunities");
    $totalOpportunities = $totalOpportunitiesStmt->fetch(PDO::FETCH_ASSOC)["total"];

    $pendingApplicationsStmt = $conn->query("SELECT COUNT(*) AS total FROM applications WHERE status = 'Pending'");
    $pendingApplications = $pendingApplicationsStmt->fetch(PDO::FETCH_ASSOC)["total"];
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttachME Admin Dashboard</title>
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
                <li class="nav-item"><a href="../Admin/AOpportunities.php" class="nav-link text-white fw-bold fs-5">📢 Opportunities</a></li>
                <li class="nav-item"><a href="../Admin/AApplications.php" class="nav-link text-white fw-bold fs-5">📄 Applications</a></li>
                <li class="nav-item"><a href="../Admin/AAnalytics.php" class="nav-link text-white fw-bold fs-5">📊 Analytics</a></li>
                <li class="nav-item"><a href="../Admin/ASettings.php" class="nav-link text-white fw-bold fs-5">⚙️ Settings</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">Admin Dashboard</h1>
            <div class="d-flex align-items-center gap-3">
                <!-- <input type="text" class="form-control w-50" placeholder="Search..."> -->
                <!-- <button class="btn btn-outline-primary fw-bold fs-5">🔔 Notifications</button> -->
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle fw-bold fs-5" type="button" data-bs-toggle="dropdown">Admin</button>
                    <ul class="dropdown-menu">
                        <!-- <li><a class="dropdown-item" href="#profile">Profile</a></li> -->
                        <li><a class="dropdown-item" href="../api/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </header><br><br><br><br>

        <!-- Stats Cards -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-primary text-white rounded-lg">
                    <h5 class="fw-bold fs-5">Total Users</h5>
                    <h2 id="totalUsers" class="fw-bold fs-3"><?php echo $totalUsers; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-success text-white rounded-lg">
                    <h5 class="fw-bold fs-5">Companies</h5>
                    <h2 id="totalCompanies" class="fw-bold fs-3"><?php echo $totalCompanies; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-warning text-white rounded-lg">
                    <h5 class="fw-bold fs-5">Opportunities</h5>
                    <h2 id="totalOpportunities" class="fw-bold fs-3"><?php echo $totalOpportunities; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-danger text-white rounded-lg">
                    <h5 class="fw-bold fs-5">Pending Applications</h5>
                    <h2 id="totalApplications" class="fw-bold fs-3"><?php echo $pendingApplications; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Admin/Terms of service.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../Admin/Contact Support.php" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="../../Javascript/ADashboard.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.querySelector("input[placeholder='Search...']");
            const dropdown = document.createElement("div");
            dropdown.classList.add("dropdown-menu");
            searchInput.parentNode.insertBefore(dropdown, searchInput.nextSibling);

            searchInput.addEventListener("input", function() {
                const query = searchInput.value;
                console.log("Search query:", query); // Debugging log
                if (query.length > 0) {
                    fetch(`api/search.php?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log("Search results:", data); // Debugging log
                            dropdown.innerHTML = ""; // Clear previous results
                            if (data.length > 0) {
                                data.forEach(item => {
                                    const suggestion = document.createElement("a");
                                    suggestion.classList.add("dropdown-item");
                                    suggestion.href = "#"; // Add appropriate link if needed
                                    suggestion.textContent = item.name; // Display user name
                                    dropdown.appendChild(suggestion);
                                });
                            } else {
                                dropdown.innerHTML = "<div class='dropdown-item'>No results found</div>";
                            }
                        })
                        .catch(error => console.error('Error:', error));
                } else {
                    dropdown.innerHTML = ""; // Clear dropdown if input is empty
                }
            });

            // Logout functionality
            const logoutButton = document.querySelector(".dropdown-item[href='#logout']");
            logoutButton.addEventListener("click", function() {
                fetch("api/logout.php") // Create a logout.php file to handle logout
                    .then(() => {
                        window.location.href = "../../SignUps/Alogin.php"; // Redirect to login page
                    })
                    .catch(error => console.error('Logout error:', error));
            });
        });
    </script>
</body>
</html>
