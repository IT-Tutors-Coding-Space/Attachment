<?php
require_once "../db.php";
session_start();

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    exit();
}

try {
    $conn->beginTransaction();
    
    // Update system settings
    $stmt = $conn->prepare("UPDATE system_settings SET 
        system_name = ?,
        timezone = ?,
        default_theme = ?,
        maintenance_mode = ?,
        auto_backup = ?,
        backup_frequency = ?
    ");
    
    $stmt->execute([
        $_POST['system_name'] ?? 'AttachME',
        $_POST['timezone'] ?? 'Africa/Nairobi',
        $_POST['default_theme'] ?? 'system',
        isset($_POST['maintenance_mode']) ? 1 : 0,
        isset($_POST['auto_backup']) ? 1 : 0,
        $_POST['backup_frequency'] ?? 'weekly'
    ]);
    
    $conn->commit();
    header("Location: ../Pages/Admin/ASettings.php?success=1");
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    error_log("Settings update failed: " . $e->getMessage());
    header("Location: ../Pages/Admin/ASettings.php?error=1");
    exit();
}
