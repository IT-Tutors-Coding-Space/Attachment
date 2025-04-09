<?php
require_once "../../db.php";
session_start();

if ($_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/ALogin.php");
    exit();
}

require "../../Components/AdminNav.php";

// Get all companies with their opportunity counts
$companies = $conn->query("
    SELECT c.*, COUNT(o.opportunities_id) as opportunity_count
    FROM companies c
    LEFT JOIN opportunities o ON c.company_id = o.company_id
    GROUP BY c.company_id
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Management - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4e73df;
            --success: #1cc88a;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
        }
        
        .company-card {
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .company-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .company-logo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .opportunity-badge {
            background-color: var(--primary);
            color: white;
        }
        
        .search-box {
            border-radius: 20px;
            padding-left: 40px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%239C9C9C' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 15px center;
        }
    </style>
</head>
<body class="bg-light">
    <?php require "../../Components/AdminNav.php"; ?>

    <div class="container py-4"><br><br><br><br><br>
        <!-- Header with Search and Add Company -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div class="mb-3 mb-md-0">
                <h1 class="h3 mb-1 text-gray-800">Company Management</h1>
                <p class="mb-0 text-muted">Manage all registered companies</p>
            </div>
            <div class="d-flex flex-column flex-md-row gap-3 w-100 w-md-auto">
                <div class="position-relative flex-grow-1">
                    <input type="text" class="form-control search-box" id="searchCompanies" 
                           placeholder="Search companies..." onkeyup="searchCompanies()">
                </div>
                <a href="../../SignUps/CompanyReg.php" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Company
                </a>
            </div>
        </div>

        <!-- Companies Table -->
        <div class="card company-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 80px;">Logo</th>
                                <th>Company</th>
                                <th>Details</th>
                                <th>Opportunities</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="companyTableBody">
                            <?php foreach ($companies as $company): ?>
                            <tr>
                                <td>
                                    <img src="../../images/<?= $company['logo'] ?? 'default-company.png' ?>" 
                                         alt="Company Logo" class="company-logo">
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($company['company_name']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($company['email']) ?></small>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small><i class="fas fa-industry me-2"></i> <?= htmlspecialchars($company['industry']) ?></small>
                                        <small><i class="fas fa-map-marker-alt me-2"></i> <?= htmlspecialchars($company['location']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge opportunity-badge">
                                        <i class="fas fa-briefcase me-1"></i>
                                        <?= $company['opportunity_count'] ?> opportunities
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $company['status'] == 'Active' ? 'success' : 'danger' ?>">
                                        <?= $company['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary" 
                                            onclick="openEditCompanyModal(
                                                '<?= $company['company_id'] ?>',
                                                '<?= htmlspecialchars($company['company_name']) ?>',
                                                '<?= htmlspecialchars($company['email']) ?>',
                                                '<?= htmlspecialchars($company['industry']) ?>',
                                                '<?= htmlspecialchars($company['location']) ?>',
                                                '<?= $company['status'] ?>'
                                            )">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="deleteCompany.php" class="d-inline">
                                            <input type="hidden" name="company_id" value="<?= $company['company_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Company Modal -->
    <div class="modal fade" id="editCompanyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Company</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCompanyForm" method="POST" action="editCompany.php" enctype="multipart/form-data">
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
                            <label for="editLogo" class="form-label">Company Logo</label>
                            <input type="file" class="form-control" id="editLogo" name="logo" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require "../../Components/AdminFooter.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        function searchCompanies() {
            const input = document.getElementById('searchCompanies').value.toLowerCase();
            const rows = document.querySelectorAll('#companyTableBody tr');
            
            rows.forEach(row => {
                const companyName = row.cells[1].textContent.toLowerCase();
                const email = row.cells[1].textContent.toLowerCase();
                const industry = row.cells[2].textContent.toLowerCase();
                const location = row.cells[2].textContent.toLowerCase();
                
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
