<?php
require_once '../../db.php';
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Unauthorized access."]);
        exit();
    } else {
        header("Location: ../SignUps/Clogin.php");
        exit();
    }
}

$company_id = $_SESSION['user_id'];
$selected_student = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;
$messages = [];
$error = null;

try {
    if (!$conn) throw new PDOException("Failed to connect to database");

    if ($selected_student) {
        $stmt = $conn->prepare("
            SELECT m.*, s.name as student_name
            FROM messages m
            JOIN students s ON (m.sender_id = s.user_id AND m.sender_role = 'student') 
                          OR (m.receiver_id = s.user_id AND m.receiver_role = 'student')
            WHERE (m.sender_id = ? AND m.sender_role = 'company' AND m.receiver_id = ? AND m.receiver_role = 'student')
               OR (m.sender_id = ? AND m.sender_role = 'student' AND m.receiver_id = ? AND m.receiver_role = 'company')
            ORDER BY m.sent_at ASC
        ");
        if (!$stmt) throw new PDOException("Failed to prepare statement");
        
        $stmt->execute([$company_id, $selected_student, $selected_student, $company_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark messages as read
        $stmt = $conn->prepare("
            UPDATE messages SET is_read = 1 
            WHERE receiver_id = ? AND receiver_role = 'company'
            AND sender_id = ? AND sender_role = 'student'
            AND is_read = 0
        ");
        if (!$stmt) throw new PDOException("Failed to prepare update statement");
        $stmt->execute([$company_id, $selected_student]);
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $error]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - AttachME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .chat-box { max-height: 500px; overflow-y: auto; padding: 1rem; }
        .message { max-width: 70%; padding: 0.75rem; border-radius: 1rem; margin-bottom: 0.5rem; }
        .sent { align-self: flex-end; background-color: #0d6efd; color: white; }
        .received { align-self: flex-start; background-color: #f8f9fa; }
    </style>
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <!-- <a class="navbar-brand fw-bold text-white" href="CHome.php"> AttachME - Messages</a> -->
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="CHome.php" class="nav-link text-white fw-bold fs-5"> Dashboard</a></li>
                <li class="nav-item"><a href="COpportunities.php" class="nav-link text-white fw-bold fs-5"> Opportunities</a></li>
                <li class="nav-item"><a href="CTrack.php" class="nav-link text-white fw-bold fs-5"> Applications</a></li>
                <li class="nav-item"><a href="CNotifications.php" class="nav-link text-white fw-bold fs-5 active"> Messages</a></li>
                <li class="nav-item"><a href="CProfile.php" class="nav-link text-white fw-bold fs-5"> Profile</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container p-5 flex-grow-1">
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h4 class="fw-bold text-primary"> Messages</h4>
        <p class="text-muted">View and respond to messages from students.</p>
        
        <div class="card shadow p-3 mb-4">
            <div class="chat-box d-flex flex-column" id="messageList">
                <?php foreach ($messages as $msg): ?>
                    <div class="message <?= ($msg['sender_id'] == $company_id && $msg['sender_role'] == 'company') ? 'sent' : 'received' ?>">
                        <div class="fw-bold">
                            <?= ($msg['sender_id'] == $company_id && $msg['sender_role'] == 'company') ? 'You' : htmlspecialchars($msg['student_name']) ?>:
                        </div>
                        <div><?= htmlspecialchars($msg['message']) ?></div>
                        <div class="small text-muted">
                            <?= date('M j, g:i a', strtotime($msg['sent_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <form id="messageForm" class="mb-3">
            <input type="hidden" name="sender_id" value="<?= $company_id ?>">
            <div class="row g-2">
                <div class="col-md-4">
                    <select name="receiver_id" id="studentSelect" class="form-select" required>
                        <option value="">Select student...</option>
                        <?php
                        try {
                            $stmt = $conn->prepare("
                                SELECT DISTINCT s.user_id, s.name 
                                FROM applications a
                                JOIN students s ON a.student_id = s.user_id
                                JOIN opportunities o ON a.opportunities_id = o.opportunities_id
                                WHERE o.company_id = ?
                                AND a.status IN ('submitted', 'under_review', 'accepted')
                                ORDER BY s.name
                            ");
                            $stmt->execute([$company_id]);
                            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($students as $student): ?>
                                <option value="<?= $student['user_id'] ?>" <?= $selected_student == $student['user_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($student['name']) ?>
                                </option>
                            <?php endforeach;
                        } catch (PDOException $e) {
                            echo "<!-- Error loading students: " . htmlspecialchars($e->getMessage()) . " -->";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" name="message" id="messageInput" class="form-control" placeholder="Type your message..." required>
                        <button type="submit" id="sendMessage" class="btn btn-primary">
                            <i class="fa fa-paper-plane"></i> Send
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

     <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0">&copy; 2025 AttachME. All rights reserved.</p>
        <div class="d-flex justify-content-center gap-4 mt-2">
            <a href="../../help-center.php" class="text-white fw-bold">Help Center</a>
            <a href="../../terms.php" class="text-white fw-bold">Terms of Service</a>
            <a href="../../contact.php" class="text-white fw-bold">Contact Support:</a>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../Javasript/CNotifications.js?v=<?= time() ?>"></script>
</body>
</html>