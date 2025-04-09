-- SQL script to update system_settings table for ASettings functionality
-- Using MySQL-compatible syntax that checks for column existence

-- Add system_name if not exists
SET @dbname = DATABASE();
SET @tablename = 'system_settings';
SET @columnname = 'system_name';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE
    (TABLE_SCHEMA = @dbname)
    AND (TABLE_NAME = @tablename)
    AND (COLUMN_NAME = @columnname)
  ) = 0,
  'ALTER TABLE system_settings ADD COLUMN system_name VARCHAR(100) DEFAULT "AttachME"',
  'SELECT 1'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add maintenance_mode if not exists
SET @columnname = 'maintenance_mode';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE
    (TABLE_SCHEMA = @dbname)
    AND (TABLE_NAME = @tablename)
    AND (COLUMN_NAME = @columnname)
  ) = 0,
  'ALTER TABLE system_settings ADD COLUMN maintenance_mode BOOLEAN DEFAULT 0',
  'SELECT 1'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add auto_backup if not exists
SET @columnname = 'auto_backup';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE
    (TABLE_SCHEMA = @dbname)
    AND (TABLE_NAME = @tablename)
    AND (COLUMN_NAME = @columnname)
  ) = 0,
  'ALTER TABLE system_settings ADD COLUMN auto_backup BOOLEAN DEFAULT 0',
  'SELECT 1'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add backup_frequency if not exists
SET @columnname = 'backup_frequency';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE
    (TABLE_SCHEMA = @dbname)
    AND (TABLE_NAME = @tablename)
    AND (COLUMN_NAME = @columnname)
  ) = 0,
  'ALTER TABLE system_settings ADD COLUMN backup_frequency ENUM("daily","weekly","monthly") DEFAULT "weekly"',
  'SELECT 1'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add default_theme if not exists
SET @columnname = 'default_theme';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE
    (TABLE_SCHEMA = @dbname)
    AND (TABLE_NAME = @tablename)
    AND (COLUMN_NAME = @columnname)
  ) = 0,
  'ALTER TABLE system_settings ADD COLUMN default_theme ENUM("light","dark") DEFAULT "light"',
  'SELECT 1'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Insert default settings if table is empty
INSERT INTO system_settings (system_name, maintenance_mode, auto_backup, backup_frequency, default_theme)
SELECT 'AttachME', 0, 0, 'weekly', 'light'
WHERE NOT EXISTS (SELECT 1 FROM system_settings);
