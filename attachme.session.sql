ALTER TABLE applications
ADD COLUMN cover_letter TEXT AFTER feedback;

-- Update existing records if needed
UPDATE applications SET 
    cover_letter = 'Application letter not provided',
    feedback = 'No feedback yet'
WHERE cover_letter IS NULL OR feedback IS NULL;