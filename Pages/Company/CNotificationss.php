<?php
include 'db_connect.php';

$action = $_GET['action'] ?? '';

if ($action == 'fetch') {
    // Fetch Notifications
    $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
    $result = $conn->query($sql);

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }

    echo json_encode($notifications);
}

elseif ($action == 'mark_read') {
    $id = $_POST['id'] ?? 0;
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(["status" => "success"]);
}

elseif ($action == 'delete') {
    $id = $_POST['id'] ?? 0;
    $sql = "DELETE FROM notifications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(["status" => "deleted"]);
}

$conn->close();
?>
