<?php
// Strict header control - no whitespace before opening tag
require_once "../../db.php";
session_start();

// Verify admin role before any output
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../SignUps/Alogin.php");
    exit();
}

require "../../Components/AdminNav.php";

// 1. Get application statistics with precise schema references
try {
    $stats = $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM applications) as total_apps,
            (SELECT COUNT(*) FROM applications WHERE status = 'Accepted') as accepted,
            (SELECT COUNT(*) FROM applications WHERE status = 'Rejected') as rejected,
            (SELECT COUNT(*) FROM companies WHERE status = 'Active') as active_companies,
            (SELECT COUNT(DISTINCT student_id) FROM applications) as unique_applicants,
            (SELECT COUNT(*) FROM opportunities WHERE application_deadline >= CURDATE()) as active_opportunities,
            (SELECT COUNT(*) FROM applications 
             WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) as weekly_apps,
            (SELECT ROUND(AVG(DATEDIFF(CURDATE(), submitted_at)), 1) 
             FROM applications WHERE status IN ('Accepted', 'Rejected')) as avg_processing_days,
            (SELECT ROUND(AVG(DATEDIFF(reviewed_at, submitted_at)), 1)
             FROM applications WHERE status IN ('Accepted', 'Rejected')) as company_response_days
    ")->fetch(PDO::FETCH_ASSOC);

    // Debug output - remove in production
    error_log("Stats query results: " . print_r($stats, true));
    
    // Set default values if null
    $stats = array_map(function($value) {
        return $value ?? 0;
    }, $stats);
    
} catch (PDOException $e) {
    error_log("Database error in AAnalytics.php: " . $e->getMessage());
    $stats = [
        'total_apps' => 0,
        'accepted' => 0,
        'rejected' => 0,
        'active_companies' => 0,
        'unique_applicants' => 0,
        'active_opportunities' => 0,
        'weekly_apps' => 0,
        'avg_processing_days' => 0,
        'company_response_days' => 0
    ];
}

// 2. Get timeline data with proper date handling
$timelineData = $conn->query("
    SELECT 
        DATE_FORMAT(submitted_at, '%b %d') as date,
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Accepted' THEN 1 ELSE 0 END) as accepted,
        SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
    FROM applications
    WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(submitted_at)
    ORDER BY submitted_at ASC
")->fetchAll(PDO::FETCH_ASSOC);

// 3. Get top companies with proper joins
$companyData = $conn->query("
    SELECT 
        c.company_id,
        c.company_name, 
        c.logo, 
        COUNT(a.applications_id) as application_count
    FROM companies c
    LEFT JOIN opportunities o ON c.company_id = o.company_id
    LEFT JOIN applications a ON o.opportunities_id = a.opportunities_id
    WHERE c.status = 'Active'
    GROUP BY c.company_id
    ORDER BY application_count DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// 4. Get status distribution
$statusData = $conn->query("
    SELECT 
        status,
        COUNT(*) as count,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM applications), 1) as percentage
    FROM applications 
    GROUP BY status
    ORDER BY count DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard | AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #a5b4fc;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --dark: #1f2937;
            --light: #f9fafb;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card {
            border-left: 4px solid;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .stat-card:hover {
            transform: scale(1.03);
        }
        
        .chart-container {
            position: relative;
            height: 40vh;
            min-height: 250px;
            max-height: 350px;
            margin-bottom: 1rem;
        }
        
        .company-logo {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
    <?php require "../../Components/AdminNav.php"; ?>

    <div class="container-fluid py-4"><br><b></b><br><b></b><br><br><br>
        <!-- Dashboard Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800 fw-bold">Analytics Dashboard</h1>
                <p class="text-muted">Comprehensive insights and performance metrics</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-calendar-alt me-2"></i> Last 30 Days
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                    <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                    <li><a class="dropdown-item" href="#">Last 90 Days</a></li>
                </ul>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row g-4 mb-4">
            <!-- Total Applications -->
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card glass-card p-3 border-left-primary">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Applications</h6>
                            <h3 class="mb-0"><?= number_format($stats['total_apps']) ?></h3>
                        </div>
                        <div class="icon-circle bg-primary-light text-primary">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-success fw-bold">+<?= round($stats['weekly_apps']/$stats['total_apps']*100, 1) ?>%</span>
                        <span class="text-muted ms-2">this week</span>
                    </div>
                </div>
            </div>
            
            <!-- Accepted -->
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card glass-card p-3 border-left-success">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Accepted</h6>
                            <h3 class="mb-0"><?= number_format($stats['accepted']) ?></h3>
                        </div>
                        <div class="icon-circle bg-success-light text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="progress progress-thin mt-2">
                        <div class="progress-bar bg-success" style="width: <?= round($stats['accepted']/$stats['total_apps']*100) ?>%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Rejected -->
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card glass-card p-3 border-left-danger">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Rejected</h6>
                            <h3 class="mb-0"><?= number_format($stats['rejected']) ?></h3>
                        </div>
                        <div class="icon-circle bg-danger-light text-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                    <div class="progress progress-thin mt-2">
                        <div class="progress-bar bg-danger" style="width: <?= round($stats['rejected']/$stats['total_apps']*100) ?>%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Processing Time -->
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card glass-card p-3 border-left-info">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Avg. Processing</h6>
                            <h3 class="mb-0"><?= $stats['avg_processing_days'] ?> days</h3>
                        </div>
                        <div class="icon-circle bg-info-light text-info">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Response time</small>
                    </div>
                </div>
            </div>
            
            <!-- Active Companies -->
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card glass-card p-3 border-left-warning">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Companies</h6>
                            <h3 class="mb-0"><?= number_format($stats['active_companies']) ?></h3>
                        </div>
                        <div class="icon-circle bg-warning-light text-warning">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Unique Students -->
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card glass-card p-3 border-left-secondary">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Students</h6>
                            <h3 class="mb-0"><?= number_format($stats['unique_applicants']) ?></h3>
                        </div>
                        <div class="icon-circle bg-secondary-light text-secondary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row g-4">
            <!-- Applications Timeline -->
            <div class="col-lg-8">
                <div class="glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-line text-primary me-2"></i> Applications Timeline
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary active">30 Days</button>
                            <button class="btn btn-outline-primary">90 Days</button>
                            <button class="btn btn-outline-primary">1 Year</button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="timelineChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Status Distribution -->
            <div class="col-lg-4">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-pie text-primary me-2"></i> Status Distribution
                    </h5>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <?php foreach($statusData as $status): ?>
                        <div class="d-flex align-items-center mb-2">
                            <div class="badge-dot bg-<?= 
                                $status['status'] == 'Accepted' ? 'success' : 
                                ($status['status'] == 'Rejected' ? 'danger' : 'warning') 
                            ?> me-2"></div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <span><?= $status['status'] ?></span>
                                    <span class="fw-bold"><?= number_format($status['count']) ?> (<?= $status['percentage'] ?>%)</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row -->
        <div class="row g-4 mt-4">
            <!-- Top Companies -->
            <div class="col-lg-6">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-trophy text-primary me-2"></i> Top Companies
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Applications</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($companyData as $company): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../../images/<?= $company['logo'] ?? 'default-company.png' ?>" 
                                                 class="company-logo me-2">
                                            <span><?= htmlspecialchars($company['company_name']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= number_format($company['application_count']) ?></td>
                                    <td>
                                        <div class="progress progress-thin">
                                            <div class="progress-bar gradient-primary" 
                                                 style="width: <?= min(100, $company['application_count']/max(1,max(array_column($companyData, 'application_count')))*100) ?>%">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Performance Metrics -->
            <div class="col-lg-6">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-tachometer-alt text-primary me-2"></i> Performance Metrics
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="glass-card p-3 gradient-primary text-white rounded-lg">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">Acceptance Rate</h6>
                                        <h3 class="mb-0">
                                            <?php 
                                                $rate = round($stats['accepted']/max(1,$stats['total_apps'])*100);
                                                echo $rate > 0 ? $rate.'%' : 'N/A';
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x opacity-25"></i>
                                </div>
                                <small class="opacity-75">
                                    <?= $stats['accepted'] ?> of <?= $stats['total_apps'] ?> applications
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="glass-card p-3 gradient-success text-white rounded-lg">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">Avg. Response Time</h6>
                                        <h3 class="mb-0">
                                            <?php 
                                                $days = $stats['avg_processing_days'];
                                                echo $days > 0 ? $days.' days' : 'N/A';
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="fas fa-stopwatch fa-2x opacity-25"></i>
                                </div>
                                <small class="opacity-5">From submission to decision</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="glass-card p-3 gradient-warning text-white rounded-lg">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">Student Engagement</h6>
                                        <h3 class="mb-0">
                                            <?php 
                                                $engagement = round($stats['unique_applicants']/max(1,$stats['total_apps'])*100);
                                                echo $engagement > 0 ? $engagement.'%' : 'N/A';
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="fas fa-users fa-2x opacity-25"></i>
                                </div>
                                <small class="opacity-75"><?= $stats['unique_applicants'] ?> unique applicants</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="glass-card p-3 gradient-info text-white rounded-lg">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">Opportunity Fill Rate</h6>
                                        <h3 class="mb-0">
                                            <?php 
                                                $fillRate = round($stats['accepted']/max(1,$stats['active_opportunities'])*100);
                                                echo $fillRate > 0 ? $fillRate.'%' : 'N/A';
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="fas fa-briefcase fa-2x opacity-25"></i>
                                </div>
                                <small class="opacity-75">Of <?= $stats['active_opportunities'] ?> opportunities</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="glass-card p-3 gradient-danger text-white rounded-lg">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">Weekly Growth</h6>
                                        <h3 class="mb-0">
                                            <?php 
                                                $growth = round($stats['weekly_apps']/max(1,$stats['total_apps']-$stats['weekly_apps'])*100);
                                                echo $growth > 0 ? '+'.$growth.'%' : 'N/A';
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="fas fa-chart-line fa-2x opacity-25"></i>
                                </div>
                                <small class="opacity-75"><?= $stats['weekly_apps'] ?> new applications</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="glass-card p-3 gradient-secondary text-white rounded-lg">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">Company Response</h6>
                                        <h3 class="mb-0">
                                            <?php 
                                                $responseDays = $stats['company_response_days'];
                                                echo $responseDays > 0 ? $responseDays.' days' : 'N/A';
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="fas fa-building fa-2x opacity-25"></i>
                                </div>
                                <small class="opacity-75">Avg. time to review</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require "../../Components/AdminFooter.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Timeline Chart
            const timelineCtx = document.getElementById('timelineChart').getContext('2d');
            new Chart(timelineCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_column($timelineData, 'date')) ?>,
                    datasets: [
                        {
                            label: 'Total Applications',
                            data: <?= json_encode(array_column($timelineData, 'total')) ?>,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Accepted',
                            data: <?= json_encode(array_column($timelineData, 'accepted')) ?>,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            tension: 0.4
                        },
                        {
                            label: 'Rejected',
                            data: <?= json_encode(array_column($timelineData, 'rejected')) ?>,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.5,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Status Distribution Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_column($statusData, 'status')) ?>,
                    datasets: [{
                        data: <?= json_encode(array_column($statusData, 'count')) ?>,
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)', // Accepted - green
                            'rgba(239, 68, 68, 0.8)',   // Rejected - red
                            'rgba(245, 158, 11, 0.8)'   // Pending - yellow
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    family: 'Inter'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const value = context.raw;
                                    const percentage = Math.round((value / total) * 100);
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
