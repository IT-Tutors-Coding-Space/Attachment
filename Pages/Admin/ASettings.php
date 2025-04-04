<?php
require_once "../../db.php";
session_start();

// Check maintenance mode
$stmt = $conn->query("SELECT maintenance_mode FROM system_settings LIMIT 1");
$maintenance = $stmt->fetch(PDO::FETCH_ASSOC);
if ($maintenance['maintenance_mode'] && !isset($_SESSION['admin_id'])) {
    header("Location: ../Maintenance.php");
    exit();
}

// Get current admin data
$adminData = [];
if (isset($_SESSION['admin_id'])) {
    $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $adminData = $stmt->fetch(PDO::FETCH_ASSOC);
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
            $stmt = $conn->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$adminName, $adminEmail, $_SESSION['admin_id']]);
            
            // Update password if provided
            if (!empty($adminPassword)) {
                $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $_SESSION['admin_id']]);
            }
            
            $_SESSION['success'] = 'Profile updated successfully.';
        }
        
        // Handle system settings update
        if (isset($_POST['update_settings'])) {
            $sessionTimeout = intval($_POST['session_timeout'] ?? 30);
            $maintenanceMode = isset($_POST['maintenance_mode']) ? 1 : 0;
            $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
            $maxLoginAttempts = intval($_POST['max_login_attempts'] ?? 5);
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
                $to = 'admin@example.com'; // Replace with actual admin email
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
        
        // Handle logout all devices
        if (isset($_POST['logout_all'])) {
            // Invalidate all sessions except current
            $stmt = $conn->prepare("UPDATE admins SET session_token = NULL WHERE id = ? AND session_token != ?");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttachME Admin - Settings</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                <li class="nav-item"><a href="../Admin/AApplications.php" class="nav-link text-white fw-bold fs-5">📄 Applications</a></li>
                <li class="nav-item"><a href="../Admin/AAnalytics.php" class="nav-link text-white fw-bold fs-5">📊 Analytics</a></li>
                <li class="nav-item"><a href="../Admin/ASettings.php" class="nav-link text-white fw-bold fs-5 active">⚙️ Settings</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">Platform Settings</h1>
            <p class="text-muted">Manage account settings, system configurations, and security preferences.</p>
        </header>

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
                        
                        <label class="fw-bold">Admin Name</label>
                        <input type="text" class="form-control mb-3" id="adminName" name="full_name" 
                            value="<?php echo htmlspecialchars($adminData['name'] ?? ''); ?>" required>
                        <label class="fw-bold">Email Address</label>
                        <input type="email" class="form-control mb-3" id="adminEmail" name="email" 
                            value="<?php echo htmlspecialchars($adminData['email'] ?? ''); ?>" required>
                        <label class="fw-bold">Change Password</label>
                        <input type="password" class="form-control mb-3" id="adminPassword" name="password" placeholder="Enter new password">
                        <button type="submit" name="update_profile" class="btn btn-primary w-100">Save Changes</button>
                    </div>
                </div>
                
                <!-- Security Settings -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                        <h5 class="fw-bold fs-5 mb-3">Security Settings</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Session Timeout (minutes)</label>
                                <input type="number" class="form-control" name="session_timeout" 
                                    value="<?php echo $systemSettings['session_timeout'] ?? 30; ?>" min="1" max="1440">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Max Login Attempts</label>
                                <input type="number" class="form-control" name="max_login_attempts" 
                                    value="<?php echo $systemSettings['max_login_attempts'] ?? 5; ?>" min="1" max="20">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Password Reset Expiry (hours)</label>
                                <input type="number" class="form-control" name="password_reset_expiry" 
                                    value="<?php echo $systemSettings['password_reset_expiry'] ?? 24; ?>" min="1" max="72">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">File Upload Limit (MB)</label>
                                <input type="number" class="form-control" name="file_upload_limit" 
                                    value="<?php echo $systemSettings['file_upload_limit'] ?? 10; ?>" min="1" max="100">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">System Log Retention (days)</label>
                                <input type="number" class="form-control" name="system_log_retention" 
                                    value="<?php echo $systemSettings['system_log_retention'] ?? 30; ?>" min="1" max="365">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Default User Role</label>
                                <select class="form-select" name="default_user_role">
                                    <option value="student" <?php echo ($systemSettings['default_user_role'] ?? 'student') === 'student' ? 'selected' : ''; ?>>Student</option>
                                    <option value="company" <?php echo ($systemSettings['default_user_role'] ?? 'student') === 'company' ? 'selected' : ''; ?>>Company</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode" 
                                <?php echo ($systemSettings['maintenance_mode'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="maintenanceMode">Maintenance Mode</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotifications" 
                                <?php echo ($systemSettings['email_notifications'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="emailNotifications">Email Notifications</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="update_settings" class="btn btn-primary">Update System Settings</button>
                            <button type="submit" name="logout_all" class="btn btn-danger">Log Out from All Devices</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
    <!-- Custom JavaScript -->
    <script src="../../Javasript/ASettings.js"></script>
</body>
</html>
