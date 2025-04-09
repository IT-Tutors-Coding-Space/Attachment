<?php
require_once "../../db.php";
session_start();

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../SignUps/Alogin.php");
    exit();
}

require "../../Components/AdminNav.php";

// Get current settings
$settings = $conn->query("SELECT * FROM system_settings")->fetch(PDO::FETCH_ASSOC);
$adminUsers = $conn->query("SELECT * FROM admins")->fetchAll(PDO::FETCH_ASSOC);
$backupHistory = $conn->query("SELECT * FROM backup_history ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings | AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #a5b4fc;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1f2937;
            --light: #f9fafb;
        }
        
        .settings-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .tab-content {
            background: transparent;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: var(--dark);
            font-weight: 500;
            padding: 1rem 1.5rem;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
            background: transparent;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: bold;
        }
        
        .backup-item {
            border-left: 3px solid var(--success);
            transition: all 0.3s ease;
        }
        
        .backup-item:hover {
            transform: translateX(5px);
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--success);
        }
        
        input:checked + .slider:before {
            transform: translateX(30px);
        }
    </style>
</head>
<body class="bg-light">
    <?php require "../../Components/AdminNav.php"; ?>

    <div class="container py-4"><br><br><br><br>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800 fw-bold">System Settings</h1>
                <p class="text-muted">Configure application preferences and system parameters</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog me-2"></i> Quick Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-database me-2"></i> Backup Now</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-bell me-2"></i> Notification Settings</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user-shield me-2"></i> Audit Log</a></li>
                </ul>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3">
                <div class="settings-card p-4 h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-sliders-h me-2"></i> Configuration
                    </h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#general">
                                <i class="fas fa-cog me-2"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#notifications">
                                <i class="fas fa-bell me-2"></i> Notifications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#security">
                                <i class="fas fa-shield-alt me-2"></i> Security
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#backups">
                                <i class="fas fa-database me-2"></i> Backups
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#admins">
                                <i class="fas fa-users-cog me-2"></i> Admin Users
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-9">
                <div class="settings-card p-4">
                    <div class="tab-content">
                        <!-- General Settings Tab -->
                        <div class="tab-pane fade show active" id="general">
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-cog me-2"></i> General Settings
                            </h5>
                            <form method="POST" action="../../api/update_settings.php">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">System Name</label>
                                        <input type="text" name="system_name" class="form-control" value="<?= htmlspecialchars($settings['system_name'] ?? 'AttachME') ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-none"></div>
                                    </div>
                                </div>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Default Theme</label>
                                        <select class="form-select" name="default_theme">
                                            <option selected>Light</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Maintenance Mode</label>
                                        <div class="d-flex align-items-center">
                                            <label class="toggle-switch me-3">
                                                <input type="checkbox" name="maintenance_mode" <?= ($settings['maintenance_mode'] ?? 0) ? 'checked' : '' ?>>
                                                <span class="slider"></span>
                                            </label>
                                            <span><?= ($settings['maintenance_mode'] ?? 0) ? 'Enabled' : 'Disabled' ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </form>
                        </div>
                        
                        <!-- Admin Users Tab -->
                        <div class="tab-pane fade" id="admins">
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-users-cog me-2"></i> Admin Users
                            </h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Last Active</th>
                                            <!-- <th>Actions</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($adminUsers as $admin): ?>
                                        <tr>
                                            <td>
                                                <div class="admin-avatar">
                                                    <?= strtoupper(substr($admin['full_name'], 0, 1)) ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($admin['full_name']) ?></td>
                                            <td><?= htmlspecialchars($admin['email']) ?></td>
                                            <td><?= $admin['last_login'] ? date('M d, Y', strtotime($admin['last_login'])) : 'Never' ?></td>
                                            <!-- <td>
                                                <button class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td> -->
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i> Add New Admin
                            </button>
                        </div>
                        
                        <!-- Backups Tab -->
                        <div class="tab-pane fade" id="backups">
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-database me-2"></i> Backup Management
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="settings-card p-4 h-100">
                                        <h6 class="fw-bold mb-3">Create Backup</h6>
                                        <p class="text-muted mb-4">Manually create a new system backup</p>
                                        <button class="btn btn-primary w-100">
                                            <i class="fas fa-database me-2"></i> Backup Now
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="settings-card p-4 h-100">
                                        <h6 class="fw-bold mb-3">Auto Backup</h6>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="autoBackup" <?= ($settings['auto_backup'] ?? 0) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="autoBackup">Enable Automatic Backups</label>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Frequency</label>
                                            <select class="form-select">
                                                <option>Daily</option>
                                                <option selected>Weekly</option>
                                                <option>Monthly</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="fw-bold mt-4 mb-3">Backup History</h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($backupHistory as $backup): ?>
                                        <tr class="backup-item">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-database text-primary me-2"></i>
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($backup['backup_name']) ?></h6>
                                                        <small class="text-muted">ID: <?= $backup['id'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $backup['backup_type'] === 'manual' ? 'primary' : 'info' ?>">
                                                    <?= ucfirst($backup['backup_type']) ?>
                                                </span>
                                            </td>
                                            <td><?= formatSizeUnits($backup['size']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $backup['status'] === 'completed' ? 'success' : 'warning' ?>">
                                                    <?= ucfirst($backup['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= date('M d, Y', strtotime($backup['created_at'])) ?>
                                                <small class="d-block text-muted"><?= date('H:i', strtotime($backup['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <button class="btn btn-sm btn-outline-primary me-2 download-backup" data-id="<?= $backup['id'] ?>">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger delete-backup" data-id="<?= $backup['id'] ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
            </div>
        </div>
    </div>

    <?php require "../../Components/AdminFooter.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // Initialize tab functionality
        const triggerTabList = [].slice.call(document.querySelectorAll('a[data-bs-toggle="tab"]'));
        triggerTabList.forEach(function (triggerEl) {
            const tabTrigger = new bootstrap.Tab(triggerEl);
            
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });

        // Toggle switch functionality
        document.querySelectorAll('.toggle-switch input').forEach(switchEl => {
            switchEl.addEventListener('change', function() {
                const statusText = this.nextElementSibling.nextElementSibling;
                statusText.textContent = this.checked ? 'Enabled' : 'Disabled';
            });
        });

        // AJAX form submission for settings
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_settings');
            
            fetch('../../api/admin_settings_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Settings saved successfully', 'success');
                } else {
                    throw new Error(data.error || 'Failed to save settings');
                }
            })
            .catch(error => showAlert(error.message, 'danger'));
        });

        // Backup functionality
        document.querySelector('.backup-now').addEventListener('click', function() {
            if (confirm('Create database backup now?')) {
                const formData = new FormData();
                formData.append('action', 'create_backup');
                
                fetch('../../api/admin_settings_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Backup created successfully', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        throw new Error(data.error || 'Backup failed');
                    }
                })
                .catch(error => showAlert(error.message, 'danger'));
            }
        });

        // Admin user management
        document.querySelectorAll('.edit-admin').forEach(btn => {
            btn.addEventListener('click', function() {
                const adminId = this.dataset.id;
                // Open edit modal with admin details
                fetchAdminDetails(adminId);
            });
        });

        document.querySelectorAll('.delete-admin').forEach(btn => {
            btn.addEventListener('click', function() {
                const adminId = this.dataset.id;
                if (confirm('Are you sure you want to delete this admin?')) {
                    const formData = new FormData();
                    formData.append('action', 'delete_admin');
                    formData.append('admin_id', adminId);
                    
                    fetch('../../api/admin_settings_api.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('Admin deleted successfully', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            throw new Error(data.error || 'Delete failed');
                        }
                    })
                    .catch(error => showAlert(error.message, 'danger'));
                }
            });
        });

        // Add new admin
        document.getElementById('addAdminForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_admin');
            
            fetch('../../api/admin_settings_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Admin added successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(data.error || 'Failed to add admin');
                }
            })
            .catch(error => showAlert(error.message, 'danger'));
        });

        function fetchAdminDetails(adminId) {
            // Implement fetch to get admin details for editing
            // Then populate and show the edit modal
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.querySelector('.container.py-4').prepend(alertDiv);
            setTimeout(() => alertDiv.remove(), 5000);
        }
    </script>
</body>
</html>
