<?php
require_once "../../db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];

    // Delete user from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    if ($stmt->execute([$userId])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete user.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
