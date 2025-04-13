-- Create typing_indicators table for real-time chat features
CREATE TABLE IF NOT EXISTS typing_indicators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'ID of the user who is typing',
    recipient_id INT NOT NULL COMMENT 'ID of the user receiving the typing indicator',
    is_typing TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if user is typing, 0 if not',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_recipient (user_id, recipient_id),
    INDEX idx_last_updated (last_updated)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add comments to explain the table purpose
ALTER TABLE typing_indicators COMMENT 'Tracks real-time typing indicators for chat system';
