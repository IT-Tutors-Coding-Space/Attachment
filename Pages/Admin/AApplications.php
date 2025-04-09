<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../db.php";
session_start();

// Check if the user is logged in
if ($_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/ALogin.php");
    exit();
}

// Debug database connection
if (!$conn) {
    die("Database connection failed: " . print_r($conn->errorInfo(), true));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttachME Admin - Applications</title>
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
                <li class="nav-item"><a href="../Admin/AApplications.php" class="nav-link text-white fw-bold fs-5 active">📄 Applications</a></li>
                <li class="nav-item"><a href="../Admin/AAnalytics.php" class="nav-link text-white fw-bold fs-5">📊 Analytics</a></li>
                <li class="nav-item"><a href="../Admin/ASettings.php" class="nav-link text-white fw-bold fs-5">⚙️ Settings</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">Manage Applications</h1>
            <div class="d-flex align-items-center gap-3">
                <input type="text" class="form-control w-50" id="searchApplications" placeholder="Search applications..." onkeyup="searchApplications()">
            </div>
        </header>

        <!-- Applications Table -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <h5 class="fw-bold fs-5 mb-3">Applications List</h5>
            <p class="text-muted">Below is a list of all student applications. You can filter, review, or update their status.</p>
            <table class="table table-striped">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>Student</th>
                        <th>Opportunity</th>
                        <th>Company</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="applicationsTableBody">
                <?php
                    // Get applications with joined data
                    $sql = "SELECT a.*, s.full_name, s.email, 
                            o.title AS opportunity_title, c.company_name AS company_name
                            FROM applications a
                            JOIN students s ON a.student_id = s.student_id
                            JOIN opportunities o ON a.opportunities_id = o.opportunities_id
                            JOIN companies c ON o.company_id = c.company_id
                            ORDER BY a.submitted_at DESC";
                    
                    echo "<!-- SQL Query: $sql -->";
                    
                    try {
                        $applicationsStmt = $conn->query($sql);
                        if (!$applicationsStmt) {
                            throw new Exception("Query failed: " . implode(" ", $conn->errorInfo()));
                        }
                        
                        $rowCount = $applicationsStmt->rowCount();
                        echo "<!-- Found $rowCount applications -->";
                        
                        if ($rowCount === 0) {
                            echo "<!-- No applications found in database -->";
                            echo "<tr><td colspan='6' class='text-center'>No applications found</td></tr>";
                        }
                        
                        while ($application = $applicationsStmt->fetch(PDO::FETCH_ASSOC)) {
                            $statusClass = '';
                            if ($application['status'] == 'Accepted') {
                                $statusClass = 'bg-success';
                            } elseif ($application['status'] == 'Rejected') {
                                $statusClass = 'bg-danger';
                            } else {
                                $statusClass = 'bg-warning';
                            }
                            
                            echo "<tr>
                                    <td>
                                        {$application['full_name']}<br>
                                        <small class='text-muted'>{$application['email']}</small>
                                    </td>
                                    <td>{$application['opportunity_title']}</td>
                                    <td>{$application['company_name']}</td>
                                    <td>{$application['submitted_at']}</td>
                                    <td><span class='badge {$statusClass}'>{$application['status']}</span></td>
                                    <td>
                                        <form method='POST' action='../../updateApplicationStatus_new.php' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to accept this application?\")'>
                                            <input type='hidden' name='applications_id' value='{$application['applications_id']}'>
                                            <input type='hidden' name='status' value='Accepted'>
                                            <button type='submit' class='btn btn-sm btn-outline-success'>Accept</button>
                                        </form>
                                        <form method='POST' action='../../updateApplicationStatus_new.php' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to reject this application?\")'>
                                            <input type='hidden' name='applications_id' value='{$application['applications_id']}'>
                                            <input type='hidden' name='status' value='Rejected'>
                                            <button type='submit' class='btn btn-sm btn-outline-danger'>Reject</button>
                                        </form>
                                        <form method='GET' action='get-application-details.php' style='display:inline;' target='_blank'>
                                            <input type='hidden' name='id' value='{$application['applications_id']}'>
                                            <button type='submit' class='btn btn-sm btn-outline-primary'>View Details</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                    } catch (Exception $e) {
                        echo "<!-- Error: " . htmlspecialchars($e->getMessage()) . " -->";
                        echo "<tr><td colspan='6' class='text-danger'>Error loading applications: " . 
                             htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Application Details Modal -->
    <div class="modal fade" id="applicationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Application Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="applicationDetails">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Admin/Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Admin/Terms of service.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../Admin/Contact support.php" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script>
        // Handle view application button clicks
        document.querySelectorAll('.view-application').forEach(button => {
            button.addEventListener('click', function() {
                const applicationId = this.getAttribute('data-id');
                fetch(`../get-application-details.php?id=${applicationId}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('applicationDetails').innerHTML = data;
                    })
                    .catch(error => {
                        document.getElementById('applicationDetails').innerHTML = 
                            `<div class="alert alert-danger">Error loading application details</div>`;
                    });
            });
        });

        // Live search functionality
        function searchApplications() {
            const input = document.getElementById('searchApplications');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('applicationsTableBody');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j]) {
                        const txtValue = cells[j].textContent || cells[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        }

        // Display success/error messages
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const error = urlParams.get('error');
            
            if (success) {
                alert(success);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            if (error) {
                alert(error);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            
            // Initialize search on page load
            document.getElementById('searchApplications').addEventListener('keyup', searchApplications);
        });
    </script>
</body>
</html>