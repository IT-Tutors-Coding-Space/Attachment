<?php
require_once "../../db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Update user in the database
    $stmt = $conn->prepare("UPDATE users SET email = ?, role = ? WHERE user_id = ?");
    if ($stmt->execute([$email, $role, $userId])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update user.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
