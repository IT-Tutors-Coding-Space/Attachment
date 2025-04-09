<?php
// Check if user is logged in as admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/Alogin.php");
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3 fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold fs-3 me-4" href="../Admin/AHome.php">
            <span class="text-primary">Attach</span><span class="text-white">ME</span> Admin
        </a>
        
        <div class="d-flex">
            <ul class="navbar-nav d-flex flex-row gap-3">
                <li class="nav-item">
                    <a href="../Admin/AHome.php" class="nav-link text-white fw-bold fs-5">🏠 Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="../Admin/AUsers.php" class="nav-link text-white fw-bold fs-5">👤 Users</a>
                </li>
                <li class="nav-item">
                    <a href="../Admin/ACompanies.php" class="nav-link text-white fw-bold fs-5">🏢 Companies</a>
                </li>
                <li class="nav-item">
                    <a href="../Admin/AOpportunities.php" class="nav-link text-white fw-bold fs-5">📢 Opportunities</a>
                </li>
                <li class="nav-item">
                    <a href="../Admin/AApplications.php" class="nav-link text-white fw-bold fs-5">📄 Applications</a>
                </li>
                <li class="nav-item">
                    <a href="../Admin/AAnalytics.php" class="nav-link text-white fw-bold fs-5">📊 Analytics</a>
                </li>
                <li class="nav-item">
                    <a href="../Admin/ASettings.php" class="nav-link text-white fw-bold fs-5">⚙️ Settings</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
