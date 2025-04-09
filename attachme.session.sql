-- Database schema for AttachME messaging system
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT,
    status ENUM('sent', 'delivered', 'read') DEFAULT 'sent',
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_attachment BOOLEAN DEFAULT FALSE,
    attachment_url VARCHAR(255),
    attachment_type VARCHAR(50),
    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES users(user_id)
);

CREATE TABLE typing_indicators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recipient_id INT NOT NULL,
    is_typing BOOLEAN DEFAULT FALSE,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (recipient_id) REFERENCES users(user_id)
);

CREATE TABLE chat_preferences (
    user_id INT PRIMARY KEY,
    dark_mode BOOLEAN DEFAULT FALSE,
    message_font VARCHAR(50) DEFAULT 'Arial',
    bubble_color VARCHAR(7) DEFAULT '#007bff',
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
