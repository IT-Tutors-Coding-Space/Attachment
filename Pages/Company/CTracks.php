<?php
include 'db_connect.php';

$action = $_GET['action'] ?? '';

if ($action == 'fetch') {
    // Fetch Applications
    $sql = "SELECT * FROM applications ORDER BY submitted_at DESC";
    $result = $conn->query($sql);

    $applications = [];
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }

    echo json_encode($applications);
}

elseif ($action == 'update_status') {
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? 'Pending';

    $sql = "UPDATE applications SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    echo json_encode(["status" => "updated"]);
}

$conn->close();
?>