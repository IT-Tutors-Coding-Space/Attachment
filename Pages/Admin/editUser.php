<?php
require_once "../../db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Validate inputs
    if (empty($userId) || empty($email) || empty($role) || empty($status)) {
        echo "<script>
                alert('All fields are required.'); 
                window.history.back();
              </script>";
        exit();
    }

    try {
        // Update user in database
        $stmt = $conn->prepare("UPDATE users SET 
                              email = :email, 
                              role = :role, 
                              status = :status 
                              WHERE user_id = :user_id");
        
        $stmt->execute([
            ':email' => $email,
            ':role' => $role,
            ':status' => $status,
            ':user_id' => $userId
        ]);

        echo "<script>
                alert('User updated successfully!'); 
                window.location.href='AUsers.php';
              </script>";
    } catch (PDOException $e) {
        echo "<script>
                alert('Error updating user: " . addslashes($e->getMessage()) . "'); 
                window.history.back();
              </script>";
    }
    exit();
}

// If not POST request, redirect
header("Location: AUsers.php");
exit();
?>