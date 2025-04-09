<?php
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../SignUps/Alogin.php");
    exit();
}
?>
<footer class="bg-dark text-white py-2 fixed-bottom" style="height: 50px; box-shadow: 0 -2px 10px rgba(0,0,0,0.2);">
    <div class="container d-flex justify-content-between align-items-center h-100">
        <span class="small text-muted">&copy; <?= date('Y') ?> AttachME Admin</span>
        <div class="d-flex gap-3">
            <a href="#help" class="text-white small text-decoration-none">Help</a>
            <a href="#terms" class="text-white small text-decoration-none">Terms</a>
            <a href="#contact" class="text-white small text-decoration-none">Contact</a>
        </div>
    </div>
</footer>
