<?php
session_start();
require "../../db.php";

// Check if user is logged in as student
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../../SignUps/Slogin.php");
    exit();
}

$student_id = $_SESSION["user_id"];
require "../../Components/StudentNav.php";
$company_id = $_GET["company_id"] ?? null;

// Get list of companies the student has applied to
try {
    $stmt = $conn->prepare("
        SELECT o.company_id, c.company_name, 
               (SELECT COUNT(*) FROM messages 
                WHERE receiver_id = ? AND sender_id = o.company_id AND status = 'unread') as unread_count
        FROM applications a
        JOIN opportunities o ON a.opportunities_id = o.opportunities_id
        JOIN companies c ON o.company_id = c.company_id
        WHERE a.student_id = ? AND a.status != 'draft'
        GROUP BY o.company_id, c.company_name
    ");
    $stmt->execute([$student_id]);
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching companies: " . $e->getMessage();
}

// Get messages if company is selected
$messages = [];
if ($company_id) {
    try {
        // Mark messages as read when opened
        $conn->prepare("
            UPDATE messages 
            SET status = 'read' 
            WHERE receiver_id = ? AND sender_id = ? AND status = 'unread'
        ")->execute([$student_id, $company_id]);

        // Get conversation
        $stmt = $conn->prepare("
            SELECT m.*, 
                   IF(m.sender_id = ?, 'sent', 'received') as message_type
            FROM messages m
            WHERE (sender_id = ? AND receiver_id = ?)
               OR (sender_id = ? AND receiver_id = ?)
            ORDER BY sent_at ASC
        ");
        $stmt->execute([$student_id, $student_id, $company_id, $company_id, $student_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching messages: " . $e->getMessage();
    }
}

// Handle new message submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["message"])) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO messages 
            (sender_id, receiver_id, message, status, sent_at) 
            VALUES (?, ?, ?, 'sent', NOW())
        ");
        $stmt->execute([$student_id, $company_id, $_POST["message"]]);
        header("Location: SNotifications.php?company_id=$company_id");
        exit();
    } catch (PDOException $e) {
        $error = "Error sending message: " . $e->getMessage();
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
    <link rel="stylesheet" href="../../CSS/styles.css">
   
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

        .chat-container {
            display: flex;
            height: 80vh;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .chat-sidebar {
            width: 350px;
            background-color: var(--card-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 15px;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }

        .chat-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .chat-item:hover, .chat-item.active {
            background-color: rgba(67, 97, 238, 0.1);
        }

        .chat-item-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .chat-item-content {
            flex: 1;
            min-width: 0;
        }

        .chat-item-name {
            font-weight: 600;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item-preview {
            font-size: 0.9em;
            color: var(--text-color);
            opacity: 0.7;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item-time {
            font-size: 0.8em;
            color: var(--text-color);
            opacity: 0.6;
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--card-bg);
        }

        .message-container {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: var(--bg-color);
        }

        .message-day-divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .message-day-divider span {
            background-color: var(--card-bg);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8em;
            position: relative;
            z-index: 1;
        }

        .message-day-divider:before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: var(--border-color);
            z-index: 0;
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

        .message-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 10px;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .message-content {
            display: flex;
            flex-direction: column;
        }

        .message-bubble {
            padding: 12px 16px;
            border-radius: 18px;
            word-wrap: break-word;
            position: relative;
        }

        .message.sent .message-bubble {
            background-color: var(--primary-color);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.received .message-bubble {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-bottom-left-radius: 4px;
        }

        .message-time {
            font-size: 0.75em;
            margin-top: 4px;
            color: var(--text-color);
            opacity: 0.7;
            display: flex;
            align-items: center;
        }

        .message.sent .message-time {
            justify-content: flex-end;
        }

        .message-status {
            margin-left: 5px;
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

        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typingAnimation {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.6; }
            30% { transform: translateY(-5px); opacity: 1; }
        }

        .message-input-container {
            padding: 15px;
            border-top: 1px solid var(--border-color);
            background-color: var(--card-bg);
            display: flex;
            align-items: center;
        }

        .message-input {
            flex: 1;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 10px 15px;
            margin-right: 10px;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .attachment-btn, .emoji-btn {
            background: none;
            border: none;
            font-size: 1.2em;
            margin: 0 5px;
            cursor: pointer;
            color: var(--text-color);
            opacity: 0.7;
        }

        .attachment-btn:hover, .emoji-btn:hover {
            opacity: 1;
        }

        .send-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .dark-mode-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 100;
        }
    </style>
</head>
<body>
    
    
    <div class="container mt-4">
        <div class="row">
            <!-- Company List -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4>Companies</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-3 border-bottom">
                            <input type="text" id="companySearch" class="form-control" placeholder="Search companies...">
                        </div>
                        <div class="company-list" style="height: calc(70vh - 56px); overflow-y: auto;">
                        <?php if (empty($companies)): ?>
                            <div class="alert alert-info">You haven't applied to any companies yet.</div>
                        <?php else: ?>
                            <?php foreach ($companies as $company): ?>
                                <div class="company-item p-3 border-bottom <?= $company['company_id'] == $company_id ? 'active' : '' ?>"
                                     onclick="window.location='SNotifications.php?company_id=<?= $company['company_id'] ?>'"
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><?= htmlspecialchars($company['company_name']) ?></span>
                                        <?php if ($company['unread_count'] ?? 0 > 0): ?>
                                            <span class="unread-count"><?= $company['unread_count'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>
                            <?php if ($company_id): ?>
                                Chat with <?= htmlspecialchars($companies[array_search($company_id, array_column($companies, 'company_id'))]['company_name'] ?? 'Company') ?>
                            <?php else: ?>
                                Select a company to chat
                            <?php endif; ?>
                        </h4>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($company_id): ?>
                            <div class="chat-container mb-3" id="chatContainer">
                                <?php if (empty($messages)): ?>
                                    <div class="text-center text-muted">No messages yet. Start the conversation!</div>
                                <?php else: ?>
                                    <?php foreach ($messages as $message): ?>
                                        <div class="message <?= $message['message_type'] ?>">
                                            <div><?= htmlspecialchars($message['message']) ?></div>
                                            <div class="message-time">
                                                <?= date('M j, g:i a', strtotime($message['sent_at'])) ?>
                                                <?php if ($message['message_type'] === 'sent'): ?>
                                                    <span class="ms-2">
                                                        <?php if ($message['status'] === 'read'): ?>
                                                            <i class="fas fa-check-double text-info" title="Read"></i>
                                                        <?php elseif ($message['status'] === 'delivered'): ?>
                                                            <i class="fas fa-check-double text-muted" title="Delivered"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-check text-muted" title="Sent"></i>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <form method="POST" class="d-flex">
                                <input type="text" name="message" class="form-control me-2" placeholder="Type your message..." required>
                                <button type="submit" class="btn btn-primary">Send</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info">Please select a company to view messages</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require "../../Components/StudentFooter.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        // Dark mode toggle
        const darkModeToggle = document.createElement('button');
        darkModeToggle.className = 'dark-mode-toggle';
        darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        document.body.appendChild(darkModeToggle);

        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            darkModeToggle.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
            
            // Save preference to localStorage
            localStorage.setItem('darkMode', isDark);
        });

        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
            darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }

        // Real-time message updates
        function setupEventSource() {
            const eventSource = new EventSource('../../API/chat_updates.php?user_id=<?= $student_id ?>');
            
            eventSource.onmessage = function(e) {
                const data = JSON.parse(e.data);
                
                if (data.type === 'new_message' && data.sender_id === '<?= $company_id ?>') {
                    // Add new message to chat
                    const chatContainer = document.getElementById('chatContainer');
                    const messageHTML = `
                        <div class="message received">
                            <div class="message-avatar">${data.sender_name.charAt(0)}</div>
                            <div class="message-content">
                                <div class="message-bubble">${data.message}</div>
                                <div class="message-time">
                                    ${new Date(data.sent_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                </div>
                            </div>
                        </div>
                    `;
                    chatContainer.insertAdjacentHTML('beforeend', messageHTML);
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
                
                if (data.type === 'typing' && data.sender_id === '<?= $company_id ?>') {
                    // Show typing indicator
                    const typingIndicator = document.getElementById('typingIndicator');
                    if (data.is_typing) {
                        typingIndicator.style.display = 'flex';
                    } else {
                        typingIndicator.style.display = 'none';
                    }
                }
            };
        }

        // Typing indicator
        const messageInput = document.querySelector('input[name="message"]');
        let typingTimeout;
        
        messageInput?.addEventListener('input', () => {
            // Send typing start event
            fetch('../../API/typing_indicator.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: <?= $student_id ?>,
                    recipient_id: <?= $company_id ?>,
                    is_typing: true
                })
            });
            
            // Clear previous timeout
            clearTimeout(typingTimeout);
            
            // Set timeout to send typing stop event
            typingTimeout = setTimeout(() => {
                fetch('../../API/typing_indicator.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: <?= $student_id ?>,
                        recipient_id: <?= $company_id ?>,
                        is_typing: false
                    })
                });
            }, 2000);
        });

        // Initialize real-time updates
        if (<?= $company_id ? 'true' : 'false' ?>) {
            setupEventSource();
            
            // Add typing indicator element
            const chatContainer = document.getElementById('chatContainer');
            chatContainer.insertAdjacentHTML('beforeend', `
                <div id="typingIndicator" class="typing-indicator" style="display: none;">
                    <div class="message-avatar">C</div>
                    <div class="typing-dots">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            `);
        }

        // Auto-scroll to bottom of chat
        const chatContainer = document.getElementById('chatContainer');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Live search functionality
        document.getElementById('companySearch')?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const companyItems = document.querySelectorAll('.company-item');
            
            companyItems.forEach(item => {
                const companyName = item.textContent.toLowerCase();
                item.style.display = companyName.includes(searchTerm) ? 'block' : 'none';
            });
        });

        // Enhanced message submission with file upload
        document.querySelector('form[method="POST"]')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.textContent;
            
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Sending...';
            
            try {
                const formData = new FormData(form);
                const response = await fetch('', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    form.reset();
                    location.reload();
                } else {
                    alert('Error sending message');
                }
            } catch (error) {
                alert('Network error - please try again');
            } finally {
                button.disabled = false;
                button.textContent = originalText;
            }
        });
    </script>
</body>
</html>
