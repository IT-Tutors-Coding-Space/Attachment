<?php
require_once "../../db.php";
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header("Location: ../../SignUps/Alogin.php");
    exit();
}

// Get current admin data
$adminData = [];
try {
    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $adminData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$adminData) {
        session_destroy();
        header("Location: ../../SignUps/Alogin.php");
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "Database error occurred";
    header("Location: AHome.php");
    exit();
}

// Check maintenance mode
$stmt = $conn->query("SELECT maintenance_mode FROM system_settings LIMIT 1");
$maintenance = $stmt->fetch(PDO::FETCH_ASSOC);
if ($maintenance['maintenance_mode']) {
    header("Location: ../Maintenance.php");
    exit();
}
?>
