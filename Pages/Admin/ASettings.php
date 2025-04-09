<?php
require_once "../../db.php";
session_start();

<<<<<<< HEAD

if ($_SESSION["role"] !== "admin") {
    header("Location: ../../ALogin.php");
=======
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../SignUps/Alogin.php");
>>>>>>> d7a7306aa262dea58932b91eb35201da20f5463f
    exit();
}

require "../../Components/AdminNav.php";

<<<<<<< HEAD
// Get current admin data with proper session validation
$adminData = [];
if (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id'])) {
    try {
        // First try with user_id since that's what's in the session
        $stmt = $conn->prepare("SELECT admin_id, full_name, email FROM admins WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $adminData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$adminData) {
            // Fallback to admin_id if user_id doesn't work
            $stmt = $conn->prepare("SELECT admin_id, full_name, email FROM admins WHERE admin_id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $adminData = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        if (!$adminData) {
            // Debug: Log session and query details
            error_log("Failed to fetch admin data. Session: " . print_r($_SESSION, true));
            error_log("Last query: " . $stmt->queryString);
            session_destroy();
            header("Location: ../../Alogin.php");
            exit();
        } else {
            // Debug: Log successful data retrieval
            error_log("Admin data retrieved: " . print_r($adminData, true));
        }
    } catch (PDOException $e) {
        error_log("Database error in ASettings.php: " . $e->getMessage());
        error_log("SQL Query: SELECT * FROM admins WHERE admin_id = " . $_SESSION['admin_id']);
        $_SESSION['error'] = "Failed to load admin profile. Please try again.";
        header("Location: ASettings.php");
        exit();
    }
}

// Ensure system_settings table exists
$conn->exec("CREATE TABLE IF NOT EXISTS system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_timeout INT DEFAULT 30,
    maintenance_mode BOOLEAN DEFAULT FALSE,
    email_notifications BOOLEAN DEFAULT TRUE,
    max_login_attempts INT DEFAULT 5,
    password_reset_expiry INT DEFAULT 24,
    file_upload_limit INT DEFAULT 10,
    default_user_role VARCHAR(50) DEFAULT 'student',
    system_log_retention INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Insert default record if not exists
$conn->exec("INSERT INTO system_settings (id) VALUES (1) ON DUPLICATE KEY UPDATE id=id");

// Get system settings
$systemSettings = [];
$stmt = $conn->query("SELECT * FROM system_settings LIMIT 1");
$systemSettings = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle profile update
        if (isset($_POST['update_profile'])) {
            $adminName = trim($_POST['full_name']);
            $adminEmail = trim($_POST['email']);
            $adminPassword = trim($_POST['password']);
            
            // Input validation
            if (empty($adminName) || empty($adminEmail)) {
                throw new Exception('Name and Email are required.');
            }
            if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format.');
            }
            if (!empty($adminPassword) && strlen($adminPassword) < 6) {
                throw new Exception('Password must be at least 6 characters long.');
            }
            
            // Update admin details
            $stmt = $conn->prepare("UPDATE admins SET full_name = ?, email = ? WHERE admin_id = ?");
            $stmt->execute([$adminName, $adminEmail, $_SESSION['admin_id']]);
            
            // Update password if provided
            if (!empty($adminPassword)) {
                $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
                $stmt->execute([$hashedPassword, $_SESSION['admin_id']]);
            }
            
            $_SESSION['success'] = 'Profile updated successfully.';
        }
        
        // Handle system settings update
        if (isset($_POST['update_settings'])) {
            $sessionTimeout = intval($_POST['session_timeout'] ?? 30);
            $maintenanceMode = isset($_POST['maintenance_mode']) ? 1 : 0;
            $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
            $maxLoginAttempts = intval($_POST['max_login_attempts'] ?? 8);
            $passwordResetExpiry = intval($_POST['password_reset_expiry'] ?? 24);
            $fileUploadLimit = intval($_POST['file_upload_limit'] ?? 10);
            $defaultUserRole = $_POST['default_user_role'] ?? 'student';
            $systemLogRetention = intval($_POST['system_log_retention'] ?? 30);
            
            $stmt = $conn->prepare("UPDATE system_settings SET 
                session_timeout = ?,
                maintenance_mode = ?,
                email_notifications = ?,
                max_login_attempts = ?,
                password_reset_expiry = ?,
                file_upload_limit = ?,
                default_user_role = ?,
                system_log_retention = ?
                WHERE id = 1");
            $stmt->execute([
                $sessionTimeout,
                $maintenanceMode,
                $emailNotifications,
                $maxLoginAttempts,
                $passwordResetExpiry,
                $fileUploadLimit,
                $defaultUserRole,
                $systemLogRetention
            ]);
            
            $_SESSION['success'] = 'System settings updated successfully.';
            
            // Log the changes
            $logMessage = "System settings updated by admin ID: {$_SESSION['admin_id']}\n" 
                . print_r($_POST, true);
            file_put_contents('../../logs/system_settings.log', date('Y-m-d H:i:s') . " - " . $logMessage . "\n", FILE_APPEND);
            
            // Send email notification if enabled
            if ($emailNotifications) {
                $to = 'hedmonachacha@gmail.com'; // Replace with actual admin email
                $subject = 'System Settings Updated';
                $message = "The following system settings were updated:\n\n"
                    . "Session Timeout: $sessionTimeout minutes\n"
                    . "Maintenance Mode: " . ($maintenanceMode ? 'ON' : 'OFF') . "\n"
                    . "Max Login Attempts: $maxLoginAttempts\n"
                    . "Password Reset Expiry: $passwordResetExpiry hours\n"
                    . "File Upload Limit: $fileUploadLimit MB\n"
                    . "Default User Role: $defaultUserRole\n"
                    . "System Log Retention: $systemLogRetention days";
                
                mail($to, $subject, $message);
            }
        }
        
if (isset($_POST['logout'])) {
    // Destroy the session and redirect to login
    session_destroy();
    header("Location: ../../Alogin.php");
    exit();
}

if (isset($_POST['delete_account'])) {
    // Delete the admin account
    $stmt = $conn->prepare("DELETE FROM admins WHERE admin_id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    
    // Destroy the session and redirect to login
    session_destroy();
    header("Location: ../../Alogin.php");
    exit();
}

if (isset($_POST['logout_all'])) {
            // Invalidate all sessions except current
            $stmt = $conn->prepare("UPDATE admins SET session_token = NULL WHERE admin_id = ? AND session_token != ?");
            $stmt->execute([$_SESSION['admin_id'], session_id()]);
            
            $_SESSION['success'] = 'Logged out from all other devices.';
        }
        
        // Refresh data after updates
        header("Location: ASettings.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
=======
// Get current settings
$settings = $conn->query("SELECT * FROM system_settings")->fetch(PDO::FETCH_ASSOC);
$adminUsers = $conn->query("SELECT * FROM admins")->fetchAll(PDO::FETCH_ASSOC);
$backupHistory = $conn->query("SELECT * FROM backup_history ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
>>>>>>> d7a7306aa262dea58932b91eb35201da20f5463f
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
<<<<<<< HEAD
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid d-flex justify-content-between">
            <h2 class="text-white fw-bold fs-3">AttachME</h2>
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="../Admin/AHome.php" class="nav-link text-white fw-bold fs-5"> Dashboard</a></li>
                <li class="nav-item"><a href="../Admin/AUsers.php" class="nav-link text-white fw-bold fs-5"> Users</a></li>
                <li class="nav-item"><a href="../Admin/ACompanies.php" class="nav-link text-white fw-bold fs-5"> Companies</a></li>
                <li class="nav-item"><a href="../Admin/AOpportunities.php" class="nav-link text-white fw-bold fs-5"> Opportunities</a></li>
                <li class="nav-item"><a href="../Admin/AApplications.php" class="nav-link text-white fw-bold fs-5">Applications</a></li>
                <li class="nav-item"><a href="../Admin/AAnalytics.php" class="nav-link text-white fw-bold fs-5"> Analytics</a></li>
                <li class="nav-item"><a href="../Admin/ASettings.php" class="nav-link text-white fw-bold fs-5 active"> Settings</a></li>
            </ul>
=======
<body class="bg-light">
    <?php require "../../Components/AdminNav.php"; ?>

    <div class="container py-4">
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
>>>>>>> d7a7306aa262dea58932b91eb35201da20f5463f
        </div>

<<<<<<< HEAD
        <form method="POST" action="">
            <div class="row g-4">
                <!-- Account Settings -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                        <h5 class="fw-bold fs-5 mb-3">Account Settings</h5>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info mb-3">
                            <strong>Logged in as:</strong> <?php echo htmlspecialchars($adminData['full_name'] ?? 'Administrator'); ?>
                            <div><small>Admin ID: <?php echo htmlspecialchars($_SESSION['admin_id'] ?? ''); ?></small></div>
                            <!-- DEBUG: <?php var_dump($adminData); ?> -->
                        </div>
                        <label class="fw-bold">Full Name</label>
                        <input type="text" class="form-control mb-3" id="adminName" name="full_name" 
                            value="<?php echo htmlspecialchars($adminData['full_name'] ?? ''); ?>" 
                            required>
                        <label class="fw-bold">Email Address</label>
                        <input type="email" class="form-control mb-3" id="adminEmail" name="email" 
                            value="<?php echo htmlspecialchars($adminData['email'] ?? ''); ?>" 
                            pattern="[a-zA-Z0-9._%+-]+@admin\.attachme$" 
                            title="Admin email must be in format username@admin.attachme" required>
                        <label class="fw-bold">Change Password</label>
                        <input type="password" class="form-control mb-3" id="adminPassword" name="password" placeholder="Enter new password">
                        <button type="submit" name="update_profile" class="btn btn-primary w-100">Save Changes</button>
                        <button type="submit" name="delete_account" class="btn btn-danger w-100 mt-3" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">Delete Account</button>
                    </div>
=======
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
>>>>>>> d7a7306aa262dea58932b91eb35201da20f5463f
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
                            <form>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">System Name</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($settings['system_name'] ?? 'AttachME') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Timezone</label>
                                        <select class="form-select">
                                            <option>UTC</option>
                                            <option selected>Africa/Nairobi</option>
                                            <option>America/New_York</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Default Theme</label>
                                        <select class="form-select">
                                            <option>Light</option>
                                            <option>Dark</option>
                                            <option selected>System</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Maintenance Mode</label>
                                        <div class="d-flex align-items-center">
                                            <label class="toggle-switch me-3">
                                                <input type="checkbox" <?= ($settings['maintenance_mode'] ?? 0) ? 'checked' : '' ?>>
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
                                            <th>Actions</th>
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
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
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
                            
                            <h6 class="fw-bold mt-4 mb-3">Recent Backups</h6>
                            <div class="list-group">
                                <?php foreach($backupHistory as $backup): ?>
                                <div class="list-group-item backup-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($backup['backup_name']) ?></h6>
                                            <small class="text-muted"><?= date('M d, Y H:i', strtotime($backup['created_at'])) ?></small>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary me-2">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
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
    </script>
</body>
</html>
