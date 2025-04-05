<?php
require_once "C:/wamp64/www/Attachment/db.php";

try {
    // Create password_reset_tokens table
    $sql = "CREATE TABLE IF NOT EXISTS password_reset_tokens (
        token_id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(255) NOT NULL,
        expiration DATETIME NOT NULL,
        used TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "Password reset tokens table created successfully";
    
    // Add SMTP configuration to db.php
    $smtp_config = "\n\n// SMTP Configuration\n";
    $smtp_config .= "\$smtp_host = 'smtp.gmail.com';\n";
    $smtp_config .= "\$smtp_port = 587;\n";
    $smtp_config .= "\$smtp_username = 'istechmyname@gmail.com';\n";
    $smtp_config .= "\$smtp_password = 'your_smtp_password';\n";
    $smtp_config .= "\$smtp_from_email = 'istechmyname@gmail.com';\n";
    $smtp_config .= "\$smtp_from_name = 'AttachME';\n";
    
    file_put_contents("../db.php", $smtp_config, FILE_APPEND);
    echo "SMTP configuration added to db.php";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
