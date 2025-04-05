-- Create archive tables
CREATE TABLE IF NOT EXISTS students_archive (
    archive_id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT,
    user_id INT,
    full_name VARCHAR(50),
    email VARCHAR(50),
    level ENUM('Certificate','Diploma','Degree','Masters'),
    password VARCHAR(50),
    year_of_study INT,
    course VARCHAR(50),
    role ENUM('student','admin','company',''),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    picture VARCHAR(255),
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS companies_archive (
    archive_id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT,
    user_id INT,
    company_name VARCHAR(50),
    email VARCHAR(50),
    location VARCHAR(50),
    industry VARCHAR(50),
    password VARCHAR(50),
    role ENUM('admin','student','company',''),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    status ENUM('Active','Inactive','',''),
    logo VARCHAR(255),
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS opportunities_archive (
    archive_id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT,
    company_id INT,
    title VARCHAR(50),
    description TEXT,
    requirements TEXT,
    available_slots INT,
    application_deadline DATE,
    status ENUM('open','closed','',''),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    location VARCHAR(50),
    company_name VARCHAR(50),
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS applications_archive (
    archive_id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT,
    student_id INT,
    opportunities_id INT,
    status ENUM('Accepted','Rejected','Pending'),
    submitted_at TIMESTAMP,
    reviewed_at TIMESTAMP,
    feedback TEXT,
    cover_letter TEXT,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by VARCHAR(50)
);

-- Enhanced deletion triggers
CREATE TRIGGER archive_deleted_student BEFORE DELETE ON students
FOR EACH ROW
BEGIN
    INSERT INTO students_archive (
        original_id, user_id, full_name, email, level, password, 
        year_of_study, course, role, created_at, updated_at, picture, deleted_by
    ) VALUES (
        OLD.student_id, OLD.user_id, OLD.full_name, OLD.email, OLD.level, OLD.password,
        OLD.year_of_study, OLD.course, OLD.role, OLD.created_at, OLD.updated_at, OLD.picture,
        CURRENT_USER()
    );
END;

CREATE TRIGGER archive_deleted_company BEFORE DELETE ON companies
FOR EACH ROW
BEGIN
    INSERT INTO companies_archive (
        original_id, user_id, company_name, email, location, industry,
        password, role, created_at, updated_at, status, logo, deleted_by
    ) VALUES (
        OLD.company_id, OLD.user_id, OLD.company_name, OLD.email, OLD.location, OLD.industry,
        OLD.password, OLD.role, OLD.created_at, OLD.updated_at, OLD.status, OLD.logo,
        CURRENT_USER()
    );
END;

CREATE TRIGGER archive_deleted_opportunity BEFORE DELETE ON opportunities
FOR EACH ROW
BEGIN
    INSERT INTO opportunities_archive (
        original_id, company_id, title, description, requirements,
        available_slots, application_deadline, status, created_at,
        updated_at, location, company_name, deleted_by
    ) VALUES (
        OLD.opportunities_id, OLD.company_id, OLD.title, OLD.description, OLD.requirements,
        OLD.available_slots, OLD.application_deadline, OLD.status, OLD.created_at,
        OLD.updated_at, OLD.location, OLD.company_name, CURRENT_USER()
    );
END;

CREATE TRIGGER archive_deleted_application BEFORE DELETE ON applications
FOR EACH ROW
BEGIN
    INSERT INTO applications_archive (
        original_id, student_id, opportunities_id, status,
        submitted_at, reviewed_at, feedback, cover_letter, deleted_by
    ) VALUES (
        OLD.applications_id, OLD.student_id, OLD.opportunities_id, OLD.status,
        OLD.submitted_at, OLD.reviewed_at, OLD.feedback, OLD.cover_letter, CURRENT_USER()
    );
END;
