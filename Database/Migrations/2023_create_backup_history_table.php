<?php

class CreateBackupHistoryTable {
    public function up($conn) {
        $sql = "CREATE TABLE backup_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            backup_name VARCHAR(255) NOT NULL,
            backup_type ENUM('manual', 'auto') NOT NULL DEFAULT 'manual',
            size BIGINT NOT NULL COMMENT 'Size in bytes',
            status ENUM('completed', 'failed', 'in_progress') NOT NULL DEFAULT 'in_progress',
            admin_id INT NULL COMMENT 'Admin who created the backup',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $conn->exec($sql);
    }

    public function down($conn) {
        $conn->exec("DROP TABLE IF EXISTS backup_history");
    }
}
