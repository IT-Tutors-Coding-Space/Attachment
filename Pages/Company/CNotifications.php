<?php
session_start();
require_once "../../db.php";

// Set content type for JSON responses
header('Content-Type: application/json');

// Ensure the user is authenticated and is a company
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    http_response_code(403); // Forbidden
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

$companyId = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "create") {
        // Validate input
        if (empty($_POST["message"])) {
            echo json_encode(["success" => false, "message" => "Message cannot be empty."]);
            exit();
        }

        $message = $_POST["message"];

        try {
            $stmt = $conn->prepare("INSERT INTO notifications (company_id, message, is_read, created_at) VALUES (:company_id, :message, 0, NOW())");
            $stmt->execute([
                ':company_id' => $companyId,
                ':message' => $message
            ]);

            echo json_encode(["success" => true, "message" => "Notification created."]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
        exit();
    }

    elseif ($action === "mark_read") {
        $id = $_POST["id"] ?? null;

        if (!$id || !ctype_digit($id)) {
            echo json_encode(["success" => false, "message" => "Invalid notification ID."]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE company_id  = :id AND company_id = :company_id");
        $stmt->execute([':id' => $id, ':company_id' => $companyId]);

        echo json_encode(["success" => true, "message" => "Notification marked as read."]);
        exit();
    }

    elseif ($action === "delete") {
        $id = $_POST["id"] ?? null;

        if (!$id || !ctype_digit($id)) {
            echo json_encode(["success" => false, "message" => "Invalid notification ID."]);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM notifications WHERE id = :id AND company_id = :company_id");
        $stmt->execute([':id' => $id, ':company_id' => $companyId]);

        echo json_encode(["success" => true, "message" => "Notification deleted."]);
        exit();
    }

    elseif ($action === "clear_all") {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE company_id = :company_id");
        $stmt->execute([':company_id' => $companyId]);

        echo json_encode(["success" => true, "message" => "All notifications cleared."]);
        exit();
    }

    else {
        echo json_encode(["success" => false, "message" => "Invalid action."]);
        exit();
    }
}

elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Fetch notifications
    try {
        $stmt = $conn->prepare("SELECT id, message, is_read, created_at FROM notifications WHERE company_id = :company_id ORDER BY created_at DESC");
        $stmt->execute([':company_id' => $companyId]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "data" => $notifications]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Company Notifications - AttachME</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Custom CSS (optional) -->
  <link rel="stylesheet" href="css/company.css"/>
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">

  <!-- Navbar -->
  <nav class="navbar navbar-dark bg-dark shadow-lg p-3">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold text-white" href="dashboard.html">🏢 AttachME - Notifications</a>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container py-5 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold text-primary">📢 Notifications</h4>
      <button class="btn btn-danger" id="clearAllBtn"><i class="fa fa-trash-alt"></i> Clear All</button>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>Message</th>
            <th>Status</th>
            <th>Received At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="notificationsTable">
          <!-- Notifications will be injected here by JavaScript -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-3 mt-auto">
    <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
    <div class="d-flex justify-content-center gap-4 mt-2">
      <a href="help-center.php" class="text-white fw-bold">Help Center</a>
      <a href="terms.php" class="text-white fw-bold">Terms</a>
      <a href="contact.php" class="text-white fw-bold">Support: 0700234362</a>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Example: Fetch notifications
    async function loadNotifications() {
      const res = await fetch("CompanyNotifications.php");
      const data = await res.json();
      const table = document.getElementById("notificationsTable");
      table.innerHTML = "";

      if (data.success) {
        data.data.forEach((n) => {
          const row = document.createElement("tr");

          row.innerHTML = `
            <td>${n.message}</td>
            <td><span class="badge ${n.is_read ? 'bg-success' : 'bg-warning'}">
              ${n.is_read ? 'Read' : 'Unread'}</span></td>
            <td>${new Date(n.created_at).toLocaleString()}</td>
            <td>
              ${n.is_read
                ? `<button class="btn btn-secondary btn-sm" disabled><i class="fa fa-check"></i> Read</button>`
                : `<button class="btn btn-success btn-sm" onclick="markAsRead(${n.id})"><i class="fa fa-check"></i> Mark as Read</button>`}
              <button class="btn btn-danger btn-sm ms-1" onclick="deleteNotification(${n.id})"><i class="fa fa-trash"></i> Delete</button>
            </td>
          `;
          table.appendChild(row);
        });
      } else {
        table.innerHTML = `<tr><td colspan="4" class="text-danger">${data.message}</td></tr>`;
      }
    }

    async function markAsRead(id) {
      await fetch("CompanyNotifications.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=mark_read&id=${id}`
      });
      loadNotifications();
    }

    async function deleteNotification(id) {
      await fetch("CompanyNotifications.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=delete&id=${id}`
      });
      loadNotifications();
    }

    document.getElementById("clearAllBtn").addEventListener("click", async () => {
      if (confirm("Are you sure you want to clear all notifications?")) {
        await fetch("CompanyNotifications.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `action=clear_all`
        });
        loadNotifications();
      }
    });

    // Load on page load
    window.onload = loadNotifications;
  </script>
</body>
</html>
