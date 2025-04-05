-- StudentApplications View: Combines student and application data
CREATE OR REPLACE VIEW StudentApplications AS
SELECT 
    s.student_id,
    s.full_name AS student_name,
    s.email AS student_email,
    s.course,
    a.applications_id,
    a.status AS application_status,
    a.submitted_at,
    o.title AS opportunity_title,
    c.company_name
FROM 
    students s
JOIN 
    applications a ON s.student_id = a.student_id
JOIN 
    opportunities o ON a.opportunities_id = o.opportunities_id
JOIN 
    companies c ON o.company_id = c.company_id;

-- CompanyOpportunities View: Shows companies with their open opportunities
CREATE OR REPLACE VIEW CompanyOpportunities AS
SELECT 
    c.company_id,
    c.company_name,
    c.industry,
    o.opportunities_id,
    o.title,
    o.description,
    o.available_slots,
    o.application_deadline,
    o.status AS opportunity_status
FROM 
    companies c
JOIN 
    opportunities o ON c.company_id = o.company_id
WHERE 
    o.status = 'open';

-- ApplicationStatus View: Current status of all applications
CREATE OR REPLACE VIEW ApplicationStatus AS
SELECT 
    a.applications_id,
    s.full_name AS student_name,
    o.title AS opportunity_title,
    c.company_name,
    a.status,
    a.submitted_at,
    a.reviewed_at
FROM 
    applications a
JOIN 
    students s ON a.student_id = s.student_id
JOIN 
    opportunities o ON a.opportunities_id = o.opportunities_id
JOIN 
    companies c ON o.company_id = c.company_id;

-- StudentProfiles View: Complete student information
CREATE OR REPLACE VIEW StudentProfiles AS
SELECT 
    s.*,
    COUNT(a.applications_id) AS total_applications,
    SUM(CASE WHEN a.status = 'Accepted' THEN 1 ELSE 0 END) AS accepted_applications
FROM 
    students s
LEFT JOIN 
    applications a ON s.student_id = a.student_id
GROUP BY 
    s.student_id;
