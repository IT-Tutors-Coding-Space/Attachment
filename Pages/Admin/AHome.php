<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttachME Admin Dashboard</title>
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
                <li class="nav-item"><a href="../Admin/AHome.html" class="nav-link text-white fw-bold fs-5">🏠 Dashboard</a></li>
                <li class="nav-item"><a href="../Admin/AUsers.html" class="nav-link text-white fw-bold fs-5">👤 Users</a></li>
                <li class="nav-item"><a href="../Admin/ACompanies.html" class="nav-link text-white fw-bold fs-5">🏢 Companies</a></li>
                <li class="nav-item"><a href="../Admin/AOpportunities.html" class="nav-link text-white fw-bold fs-5">📢 Opportunities</a></li>
                <li class="nav-item"><a href="../Admin/AApplications.html" class="nav-link text-white fw-bold fs-5">📄 Applications</a></li>
                <li class="nav-item"><a href="../Admin/AAnalytics.html" class="nav-link text-white fw-bold fs-5">📊 Analytics</a></li>
                <li class="nav-item"><a href="../Admin/ASettings.html" class="nav-link text-white fw-bold fs-5">⚙️ Settings</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container p-5 flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 shadow rounded">
            <h1 class="text-3xl fw-bold">Admin Dashboard</h1>
            <div class="d-flex align-items-center gap-3">
                <input type="text" class="form-control w-50" placeholder="Search...">
                <button class="btn btn-outline-primary fw-bold fs-5">🔔 Notifications</button>
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle fw-bold fs-5" type="button" data-bs-toggle="dropdown">Admin</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#profile">Profile</a></li>
                        <li><a class="dropdown-item" href="#logout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </header><br><br><br><br>

        <!-- Stats Cards -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-primary text-white rounded-lg">
                    <h5 class="fw-bold fs-5">Total Users</h5>
                    <h2 id="totalUsers" class="fw-bold fs-3">0</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-success text-white rounded-lg">
                    <h5 class="fw-bold fs-5">Companies</h5>
                    <h2 id="totalCompanies" class="fw-bold fs-3">0</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-warning text-white rounded-lg">
                    <h5 class="fw-bold fs-5">Opportunities</h5>
                    <h2 id="totalOpportunities" class="fw-bold fs-3">0</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 bg-danger text-white rounded-lg">
                    <h5 class="fw-bold fs-5">Pending Applications</h5>
                    <h2 id="totalApplications" class="fw-bold fs-3">0</h2>
                </div>
            </div>
        </div>
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
    <script src="../../Javasript/ADashboard.js"></script>
</body>
</html>
