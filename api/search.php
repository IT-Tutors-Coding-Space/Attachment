<?php
require_once "../db.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

$searchTerm = isset($_GET['query']) ? $_GET['query'] : '';

if ($searchTerm) {
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE name LIKE :searchTerm OR email LIKE :searchTerm");
        $stmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode([]);
}
?>
