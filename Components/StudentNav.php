<?php
// Check if user is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../../SignUps/Slogin.php");
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3 fixed-top" style="z-index: 1030;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold fs-3 me-4" href="../Students/SDashboard.php">
            <span class="text-primary">Attach</span><span class="text-white">ME</span>
        </a>
        
        <div class="d-flex">
            <!-- Main Navigation Links -->
            <ul class="navbar-nav d-flex flex-row gap-4 me-4">
                <li class="nav-item">
                    <a href="../Students/SDashboard.php" class="nav-link text-white fw-bold fs-5">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="../Students/SBrowse.php" class="nav-link text-white fw-bold fs-5"> Browse</a>
                </li>
                <li class="nav-item">
                    <a href="../Students/SApplicationSubmission.php" class="nav-link text-white fw-bold fs-5"> Applications</a>
                </li>
            </ul>
            
            <!-- Secondary Navigation Links -->
            <ul class="navbar-nav d-flex flex-row gap-3">
                <li class="nav-item">
                    <a href="../Students/SNotifications.php" class="nav-link text-white fw-bold fs-5"> Messages</a>
                </li>
                <li class="nav-item">
                    <a href="../Students/SProfile.php" class="nav-link text-white fw-bold fs-5">👤 Profile</a>
                </li>
                <li class="nav-item">
                    <a href="../Students/SAbout.php" class="nav-link text-white fw-bold fs-5"> About</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
