-- Temporarily disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- First update null records to valid values
UPDATE applications a
JOIN opportunities o ON a.opportunities_id = o.opportunities_id OR (a.opportunities_id IS NULL AND o.opportunities_id = 1)
SET a.opportunities_id = o.opportunities_id
WHERE a.opportunities_id IS NULL;

UPDATE applications 
SET cover_letter = '' 
WHERE cover_letter IS NULL;

-- Add NOT NULL constraints
ALTER TABLE applications 
MODIFY COLUMN opportunities_id INT NOT NULL,
MODIFY COLUMN cover_letter MEDIUMBLOB NOT NULL;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
