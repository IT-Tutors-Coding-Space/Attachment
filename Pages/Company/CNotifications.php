<?php
session_start();
require_once "../../db.php";

// Check if user is authenticated
if (!isset($_SESSION["user_id"])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Unauthorized access."]);
        exit();
    } else {
        header("Location: /login.php");
        exit();
    }
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION["user_id"];
$message = "";
$messageType = "";
$showForm = true;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Invalid CSRF token."]);
            exit();
        } else {
            $message = "Invalid CSRF token.";
            $messageType = "danger";
        }
    }

    // Validate input
    $notificationMessage = trim($_POST["message"] ?? '');
    if (empty($notificationMessage)) {
        $message = "Message cannot be empty.";
        $messageType = "danger";
    }

    // If no validation errors, proceed with database operation
    if (empty($message)) {
        try {
            $conn->beginTransaction();

            // Insert into notifications table
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, status, created_at, updated_at) VALUES (?, ?, 'unread', NOW(), NOW())");
            $stmt->execute([$user_id, $notificationMessage]);

            $conn->commit();

            $message = "Notification sent successfully!";
            $messageType = "success";
            $showForm = false;

            // For AJAX requests
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode([
                    "success" => true,
                    "message" => $message,
                    "notification" => [
                        "notification_id" => $conn->lastInsertId(),
                        "message" => $notificationMessage,
                        "status" => "unread",
                        "created_at" => date("Y-m-d H:i:s")
                    ]
                ]);
                exit();
            }

        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $message = "Database error: " . $e->getMessage();
            $messageType = "danger";

            // For AJAX requests
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                http_response_code(500);
                echo json_encode(["success" => false, "message" => $message]);
                exit();
            }
        }
    } else {
        // For AJAX requests
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => $message]);
            exit();
        }
    }
}

// Fetch all notifications for this user
$notifications = [];
try {
    $stmt = $conn->prepare("SELECT notification_id, message, status, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error fetching notifications: " . $e->getMessage();
    $messageType = "danger";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/CNotifications.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-primary">Notifications</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($showForm): ?>
            <form method="POST" action="notifications.php" class="mb-4" id="notificationForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="mb-3">
                    <label for="message" class="form-label">Create Notification:</label>
                    <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Notification</button>
            </form>
        <?php endif; ?>

        <h4>Your Notifications</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody id="notificationsTableBody">
                    <?php foreach ($notifications as $notification): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($notification['notification_id']); ?></td>
                            <td><?php echo htmlspecialchars($notification['message']); ?></td>
                            <td>
                                <span
                                    class="badge bg-<?php echo $notification['status'] === 'read' ? 'success' : 'warning'; ?>">
                                    <?php echo htmlspecialchars($notification['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($notification['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($notifications)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No notifications found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <script src="../../Javasript/CNotifications.js?v=<?= time() ?>"></script>
</body>

</html>