<?php
// Check if user is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../../SignUps/Slogin.php");
    exit();
}
?>
<footer class="bg-dark text-white p-3 fixed-bottom" style="z-index: 1030;">
    <div class="container-fluid text-center">
        <p class="mb-0">© <?= date('Y') ?> AttachME Student Portal</p>
        <div class="d-flex justify-content-center gap-3 mt-2">
            <a href="../Students/Terms of service.php" class="text-white">Terms of Service</a>
            <a href="../Students/Contact Support.php" class="text-white">Contact Support</a>
        </div>
    </div>
</footer>
