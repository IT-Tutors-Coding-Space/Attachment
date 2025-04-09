<?php
require_once "../../db.php";
session_start();

if ($_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/Alogin.php");
    exit();
}

require "../../Components/AdminNav.php";

// Get all opportunities with company info and application counts
$opportunities = $conn->query("
    SELECT o.*, 
           c.company_name, 
           c.logo as company_logo,
           COUNT(a.applications_id)as application_count
    FROM opportunities o
    JOIN companies c ON o.company_id = c.company_id
    LEFT JOIN applications a ON o.opportunities_id = a.opportunities_id
    GROUP BY o.opportunities_id
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opportunity Management - AttachME</title>
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
        
        .opportunity-card {
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .opportunity-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .company-logo-sm {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .deadline-indicator {
            position: relative;
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .deadline-progress {
            position: absolute;
            height: 100%;
            background: var(--primary);
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

    <div class="container py-4"><br><br><br><br>
        <!-- Header with Search and Add Opportunity -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div class="mb-3 mb-md-0">
                <h1 class="h3 mb-1 text-gray-800">Opportunity Management</h1>
                <p class="mb-0 text-muted">Manage all attachment opportunities</p>
            </div>
            <div class="d-flex flex-column flex-md-row gap-3 w-100 w-md-auto">
                <div class="position-relative flex-grow-1">
                    <input type="text" class="form-control search-box" id="searchOpportunities" 
                           placeholder="Search opportunities..." onkeyup="searchOpportunities()">
                </div>
                <a href="../Company/COpportunities.php" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Opportunity
                </a>
            </div>
        </div>

        <!-- Status Filter Tabs -->
        <ul class="nav nav-tabs mb-4" id="statusTabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-status="Open">Open</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-status="Closed">Closed</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-status="all">All</a>
            </li>
        </ul>

        <!-- Opportunities Table -->
        <div class="card opportunity-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>Opportunity</th>
                                <th>Company</th>
                                <th>Timeline</th>
                                <th>Applications</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="opportunityTableBody">
                            <?php foreach ($opportunities as $opp): 
                                $deadlineClass = (strtotime($opp['application_deadline']) < time()) ? 'text-danger' : 'text-success';
                                $daysLeft = round((strtotime($opp['application_deadline']) - time()) / (60 * 60 * 24));
                                $progressPercent = min(100, max(0, 100 - ($daysLeft * 5)));
                                $isClosed = strtotime($opp['application_deadline']) < time() || $opp['status'] === 'Closed';
                            ?>
                            <tr data-status="<?= $opp['status'] ?>" data-closed="<?= $isClosed ? 'true' : 'false' ?>">
                                <td>
                                    <img src="../../images/<?= $opp['company_logo'] ?? 'default-company.png' ?>" 
                                         alt="Company Logo" class="company-logo-sm">
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($opp['title']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($opp['location']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($opp['company_name']) ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small class="<?= $deadlineClass ?>">
                                            <i class="far fa-clock me-1"></i>
                                            <?= $daysLeft > 0 ? "$daysLeft days left" : "Closed" ?>
                                        </small>
                                        <div class="deadline-indicator mt-1">
                                            <div class="deadline-progress" style="width: <?= $progressPercent ?>%"></div>
                                        </div>
                                        <small class="text-muted">
                                            Deadline: <?= date('M d, Y', strtotime($opp['application_deadline'])) ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $opp['application_count'] > 0 ? 'info' : 'secondary' ?>">
                                        <i class="fas fa-users me-1"></i>
                                        <?= $opp['application_count'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $opp['status'] == 'Open' ? 'success' : 'danger' ?>">
                                        <?= $opp['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary" 
                                            onclick="openEditOpportunityModal(
                                                '<?= $opp['opportunities_id'] ?>',
                                                '<?= htmlspecialchars($opp['title']) ?>',
                                                '<?= htmlspecialchars($opp['description']) ?>',
                                                '<?= htmlspecialchars($opp['location']) ?>',
                                                '<?= $opp['application_deadline'] ?>',
                                                '<?= $opp['available_slots'] ?>',
                                                '<?= $opp['status'] ?>'
                                            )">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="deleteOpportunity.php" class="d-inline">
                                            <input type="hidden" name="opportunities_id" value="<?= $opp['opportunities_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this opportunity?')">
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

    <!-- Edit Opportunity Modal -->
    <div class="modal fade" id="editOpportunityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Opportunity</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editOpportunityForm" method="POST" action="editOpportunity.php">
                        <input type="hidden" name="opportunities_id" id="editOpportunityId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editTitle" class="form-label">Title*</label>
                                <input type="text" class="form-control" id="editTitle" name="title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editLocation" class="form-label">Location*</label>
                                <input type="text" class="form-control" id="editLocation" name="location" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description*</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="editDeadline" class="form-label">Deadline*</label>
                                <input type="date" class="form-control" id="editDeadline" name="application_deadline" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editSlots" class="form-label">Available Slots*</label>
                                <input type="number" class="form-control" id="editSlots" name="available_slots" min="1" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editStatus" class="form-label">Status*</label>
                                <select class="form-select" id="editStatus" name="status" required>
                                    <option value="Open">Open</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../Help Center.php" class="text-white fw-bold">Help Center</a>
            <a href="../Admin/Terms of service.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../Admin/Contact Support.php" class="text-white fw-bold">Contact Support</a>
        </div>
    </footer>
=======
    <?php require "../../Components/AdminFooter.php"; ?>
>>>>>>> d7a7306aa262dea58932b91eb35201da20f5463f
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // Enhanced status filtering with automatic deadline checking
        document.querySelectorAll('#statusTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const filterType = this.getAttribute('data-status');
                filterOpportunities(filterType);
                
                // Update active tab
                document.querySelectorAll('#statusTabs .nav-link').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        function filterOpportunities(filterType = 'Open') {
            const rows = document.querySelectorAll('#opportunityTableBody tr');
            
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                const isClosed = row.getAttribute('data-closed') === 'true';
                
                if (filterType === 'all') {
                    row.style.display = '';
                } 
                else if (filterType === 'Open') {
                    row.style.display = (status === 'Open' && !isClosed) ? '' : 'none';
                }
                else if (filterType === 'Closed') {
                    row.style.display = (status === 'Closed' || isClosed) ? '' : 'none';
                }
            });
        }

        // Apply initial filter to show open opportunities
        document.addEventListener('DOMContentLoaded', function() {
            filterOpportunities('Open');
        });

        function searchOpportunities() {
            const input = document.getElementById('searchOpportunities').value.toLowerCase();
            const rows = document.querySelectorAll('#opportunityTableBody tr');
            
            rows.forEach(row => {
                if (row.style.display === 'none') return;
                
                const title = row.cells[1].textContent.toLowerCase();
                const company = row.cells[2].textContent.toLowerCase();
                const location = row.cells[1].textContent.toLowerCase();
                
                if (title.includes(input) || company.includes(input) || location.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openEditOpportunityModal(id, title, description, location, deadline, slots, status) {
            document.getElementById('editOpportunityId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editDescription').value = description;
            document.getElementById('editLocation').value = location;
            document.getElementById('editDeadline').value = deadline;
            document.getElementById('editSlots').value = slots;
            document.getElementById('editStatus').value = status;
            
            const editModal = new bootstrap.Modal(document.getElementById('editOpportunityModal'));
            editModal.show();
        }
    </script>
</body>
</html>
