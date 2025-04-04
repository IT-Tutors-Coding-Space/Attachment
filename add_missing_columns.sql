-- SQL to add missing columns to applications table
USE attachme;


ALTER TABLE applications
ADD COLUMN cover_letter TEXT AFTER feedback,
ADD COLUMN feedback TEXT AFTER reviewed_at;

-- Update existing records if needed
UPDATE applications SET 
    cover_letter = 'Application letter not provided',
    feedback = 'No feedback yet'
WHERE cover_letter IS NULL OR feedback IS NULL;
