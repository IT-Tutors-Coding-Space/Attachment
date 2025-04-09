<?php
require_once "../../db.php";
session_start();

if ($_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/ALogin.php");
    exit();
}

require "../../Components/AdminNav.php";

// Get applications with joined data
$applications = $conn->query("
    SELECT a.*, 
           s.full_name as student_name, 
           s.email as student_email,
           o.title as opportunity_title,
           c.company_name,
           c.logo as company_logo,
           DATEDIFF(a.submitted_at, NOW()) as days_since_submission
    FROM applications a
    JOIN students s ON a.student_id = s.student_id
    JOIN opportunities o ON a.opportunities_id = o.opportunities_id
    JOIN companies c ON o.company_id = c.company_id
    ORDER BY a.submitted_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Management - AttachME</title>
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
        
        .application-card {
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .application-card:hover {
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
        
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .status-badge {
            min-width: 80px;
            text-align: center;
        }
        
        .timeline-indicator {
            position: relative;
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .timeline-progress {
            position: absolute;
            height: 100%;
            background: var(--primary);
        }
    </style>
</head>
<body class="bg-light">
    <?php require "../../Components/AdminNav.php"; ?>

    <div class="container py-4"><br><br><br><br>
        <!-- Header with Search and Filters -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div class="mb-3 mb-md-0">
                <h1 class="h3 mb-1 text-gray-800">Application Management</h1>
                <p class="mb-0 text-muted">Review and manage all student applications</p>
            </div>
            <div class="d-flex flex-column flex-md-row gap-3 w-100 w-md-auto">
                <div class="position-relative flex-grow-1">
                    <input type="text" class="form-control" id="searchApplications" 
                           placeholder="Search applications..." onkeyup="searchApplications()">
                </div>
            </div>
        </div>

        <!-- Status Filter Tabs -->
        <ul class="nav nav-tabs mb-4" id="statusTabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-status="all">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-status="Pending">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-status="Accepted">Accepted</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-status="Rejected">Rejected</a>
            </li>
        </ul>

        <!-- Applications Table -->
        <div class="card application-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>Student</th>
                                <th>Opportunity</th>
                                <th>Company</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="applicationsTableBody">
                            <?php foreach ($applications as $app): 
                                $statusClass = [
                                    'Pending' => 'bg-warning',
                                    'Accepted' => 'bg-success',
                                    'Rejected' => 'bg-danger'
                                ][$app['status']] ?? 'bg-secondary';
                                
                                $initials = implode('', array_map(function($n) { 
                                    return strtoupper($n[0]); 
                                }, explode(' ', $app['student_name'])));
                            ?>
                            <tr data-status="<?= $app['status'] ?>">
                                <td>
                                    <div class="student-avatar">
                                        <?= substr($initials, 0, 2) ?>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($app['student_name']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($app['student_email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($app['opportunity_title']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="../../images/<?= $app['company_logo'] ?? 'default-company.png' ?>" 
                                             alt="Company Logo" class="company-logo-sm">
                                        <span><?= htmlspecialchars($app['company_name']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small><?= date('M d, Y', strtotime($app['submitted_at'])) ?></small>
                                        <small class="text-muted">
                                            <?= abs($app['days_since_submission']) ?> days <?= 
                                            $app['days_since_submission'] > 0 ? 'ago' : 'from now' ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge status-badge <?= $statusClass ?>">
                                        <?= $app['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form method="POST" action="../../updateApplicationStatus_new.php" class="d-inline">
                                            <input type="hidden" name="applications_id" value="<?= $app['applications_id'] ?>">
                                            <input type="hidden" name="status" value="Accepted">
                                            <button type="submit" class="btn btn-sm btn-outline-success" 
                                                <?= $app['status'] === 'Accepted' ? 'disabled' : '' ?>>
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="../../updateApplicationStatus_new.php" class="d-inline">
                                            <input type="hidden" name="applications_id" value="<?= $app['applications_id'] ?>">
                                            <input type="hidden" name="status" value="Rejected">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                <?= $app['status'] === 'Rejected' ? 'disabled' : '' ?>>
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        <a href="get-application-details.php?id=<?= $app['applications_id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
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

    <?php require "../../Components/AdminFooter.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // Status tab filtering
        document.querySelectorAll('#statusTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const status = this.getAttribute('data-status');
                filterApplications(status);
                
                // Update active tab
                document.querySelectorAll('#statusTabs .nav-link').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        function filterApplications(status = 'all') {
            const rows = document.querySelectorAll('#applicationsTableBody tr');
            rows.forEach(row => {
                if (status === 'all' || row.getAttribute('data-status') === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function searchApplications() {
            const input = document.getElementById('searchApplications').value.toLowerCase();
            const rows = document.querySelectorAll('#applicationsTableBody tr');
            
            rows.forEach(row => {
                if (row.style.display === 'none') return;
                
                const student = row.cells[1].textContent.toLowerCase();
                const opportunity = row.cells[2].textContent.toLowerCase();
                const company = row.cells[3].textContent.toLowerCase();
                
                if (student.includes(input) || opportunity.includes(input) || company.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Apply initial filter
        document.addEventListener('DOMContentLoaded', function() {
            filterApplications('all');
        });
    </script>
</body>
</html>
