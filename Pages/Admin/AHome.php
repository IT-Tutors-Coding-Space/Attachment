<?php
require_once "../../db.php";
session_start();

if ($_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/Alogin.php");
    exit();
}

require "../../Components/AdminNav.php";

try {
    // Get statistics
    $stats = [];
    $stats['users'] = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['companies'] = $conn->query("SELECT COUNT(*) FROM companies")->fetchColumn();
    $stats['opportunities'] = $conn->query("SELECT COUNT(*) FROM opportunities")->fetchColumn();
    $stats['pending_apps'] = $conn->query("SELECT COUNT(*) FROM applications WHERE status = 'Pending'")->fetchColumn();
    
    // Get recent activities
    $activities = $conn->query("
        SELECT a.*, s.full_name as student_name, o.title as opportunity_title 
        FROM applications a
        JOIN students s ON a.student_id = s.student_id
        JOIN opportunities o ON a.opportunities_id = o.opportunities_id
        ORDER BY a.submitted_at DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AttachME</title>
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
        
        body {
            background-color: var(--light);
            padding-bottom: 60px;
        }
        
        .stat-card {
            border-radius: 10px;
            border-left: 5px solid;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: 100%;
            background: white;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            font-size: 2rem;
            opacity: 0.7;
        }
        
        .card-primary {
            border-left-color: var(--primary);
        }
        
        .card-success {
            border-left-color: var(--success);
        }
        
        .card-warning {
            border-left-color: var(--warning);
        }
        
        .card-danger {
            border-left-color: var(--danger);
        }
        
        .dashboard-header {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .recent-table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .table thead {
            background-color: var(--primary);
            color: white;
        }
        
        .badge-pill {
            border-radius: 10rem;
            padding: 0.5em 0.8em;
        }
    </style>
</head>
<body>
    <?php require "../../Components/AdminNav.php"; ?>

    <div class="container py-4"><br><b></b><br><br><b></b><br><br>
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Dashboard Overview</h1>
                    <p class="mb-0 text-muted">Welcome back, Admin</p>
                </div>
                <button class="btn btn-primary">
                    <i class="fas fa-download fa-sm"></i> Generate Report
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card card-primary p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-primary mb-1">Total Users</h5>
                            <h2 class="mb-0"><?= $stats['users'] ?></h2>
                        </div>
                        <i class="stat-icon fas fa-users text-primary"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card card-success p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-success mb-1">Companies</h5>
                            <h2 class="mb-0"><?= $stats['companies'] ?></h2>
                        </div>
                        <i class="stat-icon fas fa-building text-success"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card card-warning p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-warning mb-1">Opportunities</h5>
                            <h2 class="mb-0"><?= $stats['opportunities'] ?></h2>
                        </div>
                        <i class="stat-icon fas fa-briefcase text-warning"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card card-danger p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-danger mb-1">Pending Apps</h5>
                            <h2 class="mb-0"><?= $stats['pending_apps'] ?></h2>
                        </div>
                        <i class="stat-icon fas fa-clock text-danger"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card recent-table mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-white">Recent Applications</h5>
                <a href="../Admin/AApplications.php" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Opportunity</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td><?= htmlspecialchars($activity['student_name']) ?></td>
                                <td><?= htmlspecialchars($activity['opportunity_title']) ?></td>
                                <td>
                                    <span class="badge badge-pill bg-<?= 
                                        $activity['status'] === 'Accepted' ? 'success' : 
                                        ($activity['status'] === 'Pending' ? 'warning' : 'danger') 
                                    ?>">
                                        <?= $activity['status'] ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($activity['submitted_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </button>
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
    <!-- Custom JavaScript -->
    <script src="../Javasript/ADashboard.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.querySelector("input[placeholder='Search...']");
            const dropdown = document.createElement("div");
            dropdown.classList.add("dropdown-menu");
            searchInput.parentNode.insertBefore(dropdown, searchInput.nextSibling);

            searchInput.addEventListener("input", function() {
                const query = searchInput.value;
