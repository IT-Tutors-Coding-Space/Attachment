<?php
require_once '../../db.php';
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Unauthorized access."]);
        exit();
    } else {
        header("Location: ../SignUps/Slogin.php");
        exit();
    }
}

$student_id = $_SESSION['user_id'];
$selected_company = isset($_GET['company_id']) ? (int)$_GET['company_id'] : null;
$messages = [];
$error = null;

try {
    if (!$conn) throw new PDOException("Failed to connect to database");

    if ($selected_company) {
        $stmt = $conn->prepare("
            SELECT m.*, c.name as company_name
            FROM messages m
            JOIN companies c ON (m.sender_id = c.user_id AND m.sender_role = 'company') 
                          OR (m.receiver_id = c.user_id AND m.receiver_role = 'company')
            WHERE (m.sender_id = ? AND m.sender_role = 'student' AND m.receiver_id = ? AND m.receiver_role = 'company')
               OR (m.sender_id = ? AND m.sender_role = 'company' AND m.receiver_id = ? AND m.receiver_role = 'student')
            ORDER BY m.sent_at ASC
        ");
        if (!$stmt) throw new PDOException("Failed to prepare statement");
        
        $stmt->execute([$student_id, $selected_company, $selected_company, $student_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark messages as read
        $stmt = $conn->prepare("
            UPDATE messages SET is_read = 1 
            WHERE receiver_id = ? AND receiver_role = 'student'
            AND sender_id = ? AND sender_role = 'company'
            AND is_read = 0
        ");
        if (!$stmt) throw new PDOException("Failed to prepare update statement");
        $stmt->execute([$student_id, $selected_company]);
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
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100">
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg p-3">
        <div class="container-fluid">
            <ul class="navbar-nav d-flex flex-row gap-4">
                <li class="nav-item"><a href="SHome.php" class="nav-link text-white fw-bold fs-5"> Dashboard</a></li>
                <li class="nav-item"><a href="SOpportunities.php" class="nav-link text-white fw-bold fs-5"> Opportunities</a></li>
                <li class="nav-item"><a href="SApplications.php" class="nav-link text-white fw-bold fs-5"> Applications</a></li>
                <li class="nav-item"><a href="SNotifications.php" class="nav-link text-white fw-bold fs-5 active"> Messages</a></li>
                <li class="nav-item"><a href="SProfile.php" class="nav-link text-white fw-bold fs-5"> Profile</a></li>
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
        <p class="text-muted">View and respond to messages from companies.</p>
        
        <div class="card shadow p-3 mb-4">
            <div class="chat-box d-flex flex-column" id="messageList">
                <?php foreach ($messages as $msg): ?>
                    <div class="message <?= ($msg['sender_id'] == $student_id && $msg['sender_role'] == 'student') ? 'sent' : 'received' ?>">
                        <div class="message-bubble">
                            <div class="fw-bold">
                                <?= ($msg['sender_id'] == $student_id && $msg['sender_role'] == 'student') ? 'You' : htmlspecialchars($msg['company_name']) ?>:
                            </div>
                            <div><?= htmlspecialchars($msg['message']) ?></div>
                            <div class="small text-muted">
                                <?= date('M j, g:i a', strtotime($msg['sent_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <form id="messageForm" class="mb-3">
            <input type="hidden" name="sender_id" value="<?= $student_id ?>">
            <div class="row g-2">
                <div class="col-md-4">
                    <select name="receiver_id" id="companySelect" class="form-select" required>
                        <option value="">Select company...</option>
                        <?php
                        try {
                            $stmt = $conn->prepare("
                                SELECT DISTINCT c.user_id, c.name 
                                FROM applications a
                                JOIN opportunities o ON a.opportunities_id = o.opportunities_id
                                JOIN companies c ON o.company_id = c.user_id
                                WHERE a.student_id = ?
                                AND a.status IN ('submitted', 'under_review', 'accepted', 'interview_scheduled', 'offer_received')
                                ORDER BY c.name
                            ");
                            $stmt->execute([$student_id]);
                            $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($companies as $company): ?>
                                <option value="<?= $company['user_id'] ?>" <?= $selected_company == $company['user_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($company['name']) ?>
                                </option>
                            <?php endforeach;
                        } catch (PDOException $e) {
                            echo "<!-- Error loading companies: " . htmlspecialchars($e->getMessage()) . " -->";
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
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --text-color: #2b2d42;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
        }
        
        .dark-mode {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --text-color: #f8f9fa;
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --border-color: #2d2d2d;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .message {
            display: flex;
            margin-bottom: 15px;
            max-width: 70%;
        }

        .message.sent {
            margin-left: auto;
            flex-direction: row-reverse;
        }

        .message.received {
            margin-right: auto;
        }

        .message-bubble {
            padding: 12px 16px;
            border-radius: 18px;
            word-wrap: break-word;
            position: relative;
            margin: 8px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-width: 80%;
        }

        .message.sent .message-bubble {
            background-color: var(--primary-color);
            color: white;
            border-bottom-right-radius: 4px;
            margin-left: 20%;
        }

        .message.received .message-bubble {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-bottom-left-radius: 4px;
            margin-right: 20%;
        }

        .message-bubble div {
            margin-bottom: 4px;
        }

        .typing-indicator {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background-color: var(--card-bg);
            border-radius: 18px;
            margin-bottom: 15px;
            width: fit-content;
        }

        .typing-dots {
            display: flex;
            margin-left: 8px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background-color: var(--text-color);
            border-radius: 50%;
            margin: 0 2px;
            animation: typingAnimation 1.4s infinite ease-in-out;
        }

        @keyframes typingAnimation {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.6; }
            30% { transform: translateY(-5px); opacity: 1; }
        }
    </style>
    <script src="../../Javasript/SNotifications.js?v=<?= time() ?>"></script>
</body>
</html>
