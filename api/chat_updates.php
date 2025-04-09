<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

require "../../db.php";

// Get user ID from query string
$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    exit();
}

// Set unlimited execution time
set_time_limit(0);

// Function to send SSE message
function sendMessage($data) {
    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();
}

// Track last message ID
$last_message_id = 0;

// Main event loop
while (true) {
    // Check for new messages
    try {
        $stmt = $conn->prepare("
            SELECT m.*, 
                   IF(m.sender_id = ?, c.company_name, s.full_name) as sender_name
            FROM messages m
            LEFT JOIN companies c ON m.sender_id = c.company_id
            LEFT JOIN students s ON m.sender_id = s.student_id
            WHERE m.receiver_id = ? AND m.message_id > ?
            ORDER BY m.message_id DESC
            LIMIT 1
        ");
        $stmt->execute([$user_id, $user_id, $last_message_id]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($message) {
            $last_message_id = $message['message_id'];
            sendMessage([
                'type' => 'new_message',
                'sender_id' => $message['sender_id'],
                'sender_name' => $message['sender_name'],
                'message' => $message['message'],
                'sent_at' => $message['sent_at']
            ]);
        }
    } catch (PDOException $e) {
        // Log error but don't exit
        error_log("Database error: " . $e->getMessage());
    }

    // Check for typing indicators
    try {
        $stmt = $conn->prepare("
            SELECT * FROM typing_indicators
            WHERE recipient_id = ? AND last_updated > DATE_SUB(NOW(), INTERVAL 3 SECOND)
            ORDER BY last_updated DESC
            LIMIT 1
        ");
        $stmt->execute([$user_id]);
        $typing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($typing) {
            sendMessage([
                'type' => 'typing',
                'sender_id' => $typing['user_id'],
                'is_typing' => (bool)$typing['is_typing']
            ]);
        }
    } catch (PDOException $e) {
        // Log error but don't exit
        error_log("Database error: " . $e->getMessage());
    }

    // Sleep before next check
    sleep(1);
}
?>
