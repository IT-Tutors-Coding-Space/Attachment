<?php
// Check if user is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: ../../SignUps/Clogin.php");
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
    <div class="container-fluid d-flex justify-content-between">
        <h2 class="text-white fw-bold fs-3">AttachME - Company Portal</h2>
        <ul class="navbar-nav d-flex flex-row gap-3">
            <li class="nav-item"><a href="../Companies/CDashboard.php" class="nav-link text-white fw-bold fs-5 active">🏠 Dashboard</a></li>
            <li class="nav-item"><a href="../Companies/CAbout.php" class="nav-link text-white fw-bold fs-5">📖 About Us</a></li>
            <li class="nav-item"><a href="../Companies/CPostOpportunity.php" class="nav-link text-white fw-bold fs-5">📝 Post Opportunity</a></li>
            <li class="nav-item"><a href="../Companies/CViewApplications.php" class="nav-link text-white fw-bold fs-5">📄 View Applications</a></li>
            <li class="nav-item"><a href="../Companies/CChat.php" class="nav-link text-white fw-bold fs-5">💬 Messages</a></li>
            <li class="nav-item"><a href="../Companies/CProfile.php" class="nav-link text-white fw-bold fs-5">👤 Profile</a></li>
        </ul>
    </div>
</nav>
