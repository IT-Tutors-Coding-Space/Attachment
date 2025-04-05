<?php
require_once "../../db.php";
session_start();

// Check if the user is logged in
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: Slogin.php"); // Redirect to login page if not authenticated
//     exit();
// }

if ($_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/Alogin.php");
    exit();
}
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
                <input type="text" class="form-control w-50" id="searchOpportunities" placeholder="Search opportunities..." onkeyup="searchOpportunities()">
                <!-- <a href="OpportunityReg.php" class="btn btn-primary fw-bold fs-5">+ Post Opportunity</a> -->
            </div>
        </header>

        <!-- Opportunities Table -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <h5 class="fw-bold fs-5 mb-3">Opportunities List</h5>
            <p class="text-muted">Below is a list of available attachment opportunities. You can filter or remove them as needed.</p>
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
                                <td>{$opportunity['company_id']}</td>
                                <td>{$opportunity['title']}</td>
                                <td>{$opportunity['location']}</td>
                                <td>{$opportunity['application_deadline']}</td>
                                <td>{$opportunity['available_slots']}</td>
                                <td><span class='badge " . ($opportunity['status'] == 'Open' ? 'bg-success' : 'bg-danger') . "'>{$opportunity['status']}</span></td>
                                <td>
                                    
                                    <form method='POST' action='deleteOpportunity.php' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this opportunity?\")'>
                                        <input type='hidden' name='opportunities_id' value='{$opportunity['opportunities_id']}'>
                                        <button type='submit' class='btn btn-sm btn-outline-danger'>Delete</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Opportunity Modal -->
    <div class="modal fade" id="editOpportunityModal" tabindex="-1" aria-labelledby="editOpportunityModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOpportunityModalLabel">Edit Opportunity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editOpportunityForm" method="POST" action="editOpportunity.php" onsubmit="return validateEditForm()">
                        <input type="hidden" name="opportunities_id" id="editOpportunityId">
                        <div class="mb-3">
                            <label for="editTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editLocation" class="form-label">Location</label>
                            <input type="text" class="form-control" id="editLocation" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDeadline" class="form-label">Application Deadline</label>
                            <input type="date" class="form-control" id="editDeadline" name="application_deadline" required>
                        </div>
                        <div class="mb-3">
                            <label for="editSlots" class="form-label">Available Slots</label>
                            <input type="number" class="form-control" id="editSlots" name="available_slots" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="Open">Open</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
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
    <script>
        function validateEditForm() {
            const title = document.getElementById('editTitle').value.trim();
            const description = document.getElementById('editDescription').value.trim();
            const location = document.getElementById('editLocation').value.trim();
            const deadline = document.getElementById('editDeadline').value;
            const slots = document.getElementById('editSlots').value;
            
            if (!title || !description || !location || !deadline || !slots) {
                alert('Please fill in all required fields');
                return false;
            }
            if (slots < 1) {
                alert('Available slots must be at least 1');
                return false;
            }
            return true;
        }

        // Display success/error messages if present in URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const error = urlParams.get('error');
            
            if (success) {
                alert(success);
                // Remove success param from URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            if (error) {
                alert(error);
                // Remove error param from URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        function searchOpportunities() {
            const input = document.getElementById('searchOpportunities').value.toLowerCase();
            const rows = document.querySelectorAll('#opportunityTableBody tr');
            
            rows.forEach(row => {
                const title = row.cells[1].textContent.toLowerCase();
                const location = row.cells[2].textContent.toLowerCase();
                const deadline = row.cells[3].textContent.toLowerCase();
                
                if (title.includes(input) || location.includes(input) || deadline.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        
    </script>
</body>
</html>
