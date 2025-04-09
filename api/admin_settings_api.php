<?php
require_once "../db.php";
session_start();

// Verify admin authentication
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized access']));
}

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_settings':
            // Update system settings
            $stmt = $conn->prepare("UPDATE system_settings SET 
                system_name = ?,
                maintenance_mode = ?,
                auto_backup = ?,
                backup_frequency = ?,
                default_theme = ?
                WHERE id = 1");
            
            $stmt->execute([
                $_POST['system_name'],
                isset($_POST['maintenance_mode']) ? 1 : 0,
                isset($_POST['auto_backup']) ? 1 : 0,
                $_POST['backup_frequency'],
                $_POST['default_theme']
            ]);
            
            echo json_encode(['success' => true]);
            break;

        case 'create_backup':
            // Backup database functionality
            $backupFile = 'backups/backup-' . date("Y-m-d-H-i-s") . '.sql';
            $command = "mysqldump -u username -ppassword database_name > $backupFile";
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                // Log backup in database
                $stmt = $conn->prepare("INSERT INTO backup_history 
                    (backup_name, backup_type, size, status) 
                    VALUES (?, 'manual', ?, 'completed')");
                $stmt->execute([
                    basename($backupFile),
                    filesize($backupFile)
                ]);
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Backup failed');
            }
            break;

        case 'add_admin':
            // Add new admin user with password hashing
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admins 
                (full_name, email, password) 
                VALUES (?, ?, ?)");
            $stmt->execute([
                $_POST['full_name'],
                $_POST['email'],
                $hashedPassword
            ]);
            echo json_encode(['success' => true]);
            break;

        case 'edit_admin':
            // Edit admin user
            $stmt = $conn->prepare("UPDATE admins SET 
                full_name = ?, email = ? 
                WHERE admin_id = ?");
            $stmt->execute([
                $_POST['full_name'],
                $_POST['email'],
                $_POST['admin_id']
            ]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_admin':
            // Delete admin user (prevent self-deletion)
            if ($_POST['admin_id'] == $_SESSION['user_id']) {
                throw new Exception('Cannot delete your own account');
            }
            $stmt = $conn->prepare("DELETE FROM admins WHERE admin_id = ?");
            $stmt->execute([$_POST['admin_id']]);
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
