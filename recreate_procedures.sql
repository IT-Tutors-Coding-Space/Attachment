DELIMITER $$

-- Recreate LogSystemEvent procedure
DROP PROCEDURE IF EXISTS LogSystemEvent;
CREATE PROCEDURE LogSystemEvent(
    IN p_log_type ENUM('INFO', 'WARNING', 'ERROR'),
    IN p_message TEXT,
    IN p_created_by VARCHAR(50)
)
BEGIN
    INSERT INTO system_logs (log_type, message, created_by)
    VALUES (p_log_type, p_message, p_created_by);
END$$

-- Recreate LogUserActivity procedure
DROP PROCEDURE IF EXISTS LogUserActivity;
CREATE PROCEDURE LogUserActivity(
    IN p_user_id INT,
    IN p_activity_type VARCHAR(50),
    IN p_description TEXT,
    IN p_ip_address VARCHAR(45)
)
BEGIN
    INSERT INTO user_activity_logs (user_id, activity_type, description, ip_address)
    VALUES (p_user_id, p_activity_type, p_description, p_ip_address);
END$$

-- Recreate LogError procedure
DROP PROCEDURE IF EXISTS LogError;
CREATE PROCEDURE LogError(
    IN p_error_message TEXT,
    IN p_error_code VARCHAR(10),
    IN p_stack_trace TEXT
)
BEGIN
    INSERT INTO error_logs (error_message, error_code, stack_trace)
    VALUES (p_error_message, p_error_code, p_stack_trace);
END$$

DELIMITER ;
