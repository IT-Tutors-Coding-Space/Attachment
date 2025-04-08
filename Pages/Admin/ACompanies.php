<?php
require_once "../../db.php";
session_start();

// Check if the user is logged in
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: Alogin.php"); // Redirect to login page if not authenticated
//     exit();
// }
if ($_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/Alogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttachME Admin - Companies</title>
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
                <li class="nav-item"><a href="../Admin/ACompanies.php" class="nav-link text-white fw-bold fs-5 active">🏢 Companies</a></li>
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
            <h1 class="text-3xl fw-bold">Manage Companies</h1>
            <div class="d-flex align-items-center gap-3">
                <input type="text" class="form-control w-50" id="searchCompanies" placeholder="Search companies..." onkeyup="searchCompanies()">
                <a href="../../SignUps/CompanyReg.php" class="btn btn-primary fw-bold fs-5">+ Add Company</a>
            </div>
        </header>

        <!-- Companies Table -->
        <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
            <h5 class="fw-bold fs-5 mb-3">Company List</h5>
            <table class="table table-striped">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>Logo</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Industry</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="companyTableBody">
                <?php
                    $companiesStmt = $conn->query("SELECT * FROM companies");
                    while ($company = $companiesStmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td><img src='images/{$company['logo']}' alt='Company Logo' class='img-thumbnail' width='50'></td>
                                <td>{$company['company_name']}</td>
                                <td>{$company['email']}</td>
                                <td>{$company['industry']}</td>
                                <td>{$company['location']}</td>
                                <td><span class='badge " . ($company['status'] == 'Active' ? 'bg-success' : 'bg-danger') . "'>{$company['status']}</span></td>
                                <td>
                                    <button class='btn btn-sm btn-outline-warning mb-1' 
                                        onclick='openEditCompanyModal(
                                            \"{$company['company_id']}\",
                                            \"{$company['company_name']}\",
                                            \"{$company['email']}\",
                                            \"{$company['industry']}\",
                                            \"{$company['location']}\",
                                            \"{$company['status']}\"
                                        )'>Edit</button>
                                    <form method='POST' action='deleteCompany.php' style='display:inline;'>
                                        <input type='hidden' name='company_id' value='{$company['company_id']}'>
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

    <!-- Edit Company Modal -->
    <div class="modal fade" id="editCompanyModal" tabindex="-1" aria-labelledby="editCompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCompanyModalLabel">Edit Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCompanyForm" method="POST" action="editCompany.php">
                        <input type="hidden" name="company_id" id="editCompanyId">
                        <div class="mb-3">
                            <label for="editCompanyName" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="editCompanyName" name="company_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editIndustry" class="form-label">Industry</label>
                            <input type="text" class="form-control" id="editIndustry" name="industry" required>
                        </div>
                        <div class="mb-3">
                            <label for="editLocation" class="form-label">Location</label>
                            <input type="text" class="form-control" id="editLocation" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
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
            <a href="../Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Admin/Terms of service.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../Admin/Contact Support.php" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchCompanies() {
            const input = document.getElementById('searchCompanies').value.toLowerCase();
            const rows = document.querySelectorAll('#companyTableBody tr');
            
            rows.forEach(row => {
                const companyName = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const industry = row.cells[3].textContent.toLowerCase();
                const location = row.cells[4].textContent.toLowerCase();
                
                if (companyName.includes(input) || email.includes(input) || 
                    industry.includes(input) || location.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openEditCompanyModal(companyId, companyName, email, industry, location, status) {
            document.getElementById('editCompanyId').value = companyId;
            document.getElementById('editCompanyName').value = companyName;
            document.getElementById('editEmail').value = email;
            document.getElementById('editIndustry').value = industry;
            document.getElementById('editLocation').value = location;
            document.getElementById('editStatus').value = status;
            
            const editModal = new bootstrap.Modal(document.getElementById('editCompanyModal'));
            editModal.show();
        }
    </script>
</body>
</html>
