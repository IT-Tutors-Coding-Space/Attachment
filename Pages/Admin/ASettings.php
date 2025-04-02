<?php
require_once "../../db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminName = trim($_POST['full_name']);
    $adminEmail = trim($_POST['email']);
    $adminPassword = trim($_POST['password']);
    
    // Input validation
    if (empty($adminName) || empty($adminEmail)) {
        echo "<script>alert('Name and Email are required.');</script>";
    } elseif (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
    } elseif (!empty($adminPassword) && strlen($adminPassword) < 6) {
        echo "<script>alert('Password must be at least 6 characters long.');</script>";
    } else {
        // Update admin details in the database
        $stmt = $conn->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$adminName, $adminEmail, $_SESSION['admin_id']]);
        
        // Update password if provided
        if (!empty($adminPassword)) {
            $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $_SESSION['admin_id']]);
        }
        
        echo "<script>alert('Settings updated successfully.');</script>";
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
                        <label class="fw-bold">Admin Name</label>
                        <input type="text" class="form-control mb-3" id="adminName" name="full_name" value="<?php echo htmlspecialchars($adminName['full_name']); ?>">
                        <label class="fw-bold">Email Address</label>
                        <input type="email" class="form-control mb-3" id="adminEmail" name="email" value="<?php echo htmlspecialchars($adminE['email']); ?>">
                        <label class="fw-bold">Change Password</label>
                        <input type="password" class="form-control mb-3" id="adminPassword" name="password" placeholder="Enter new password">
                        <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                    </div>
                </div>
                
                <!-- Security Settings -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm p-4 bg-white rounded-lg">
                        <h5 class="fw-bold fs-5 mb-3">Security Settings</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="sessionTimeout">
                            <label class="form-check-label fw-bold" for="sessionTimeout">Enable Session Timeout</label>
                        </div>
                        <button class="btn btn-danger w-100" id="logoutAll">Log Out from All Devices</button>
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
