-- UniFreelance Database Schema
-- Version 1.0

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS unifreelance;
USE unifreelance;

-- Users table (main authentication table)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'client', 'admin') NOT NULL,
    status ENUM('active', 'pending', 'suspended') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_status (status)
);

-- Students table (extends users)
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    full_name VARCHAR(100),
    university VARCHAR(100),
    major VARCHAR(100),
    year ENUM('Freshman', 'Sophomore', 'Junior', 'Senior', 'Graduate'),
    skills TEXT,
    bio TEXT,
    phone VARCHAR(20),
    id_document VARCHAR(255),
    id_verified ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_earnings DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_id_verified (id_verified)
);

-- Clients table (extends users)
CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    company_name VARCHAR(100),
    contact_person VARCHAR(100),
    industry VARCHAR(100),
    website VARCHAR(255),
    bio TEXT,
    phone VARCHAR(20),
    address TEXT,
    id_document VARCHAR(255),
    id_verified ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_spent DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_id_verified (id_verified)
);

-- Jobs table
CREATE TABLE jobs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    student_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('web_development', 'mobile_development', 'graphic_design', 
                  'content_writing', 'data_entry', 'research', 'tutoring', 'other') NOT NULL,
    skills_required TEXT,
    budget_type ENUM('fixed', 'hourly') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    deadline DATE NOT NULL,
    status ENUM('open', 'in_progress', 'completed', 'cancelled', 'disputed', 
                'awaiting_payment', 'paid') DEFAULT 'open',
    payment_released_at TIMESTAMP NULL,
    refunded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (student_id) REFERENCES users(id),
    INDEX idx_client_id (client_id),
    INDEX idx_student_id (student_id),
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_deadline (deadline),
    CHECK (amount >= 10 AND amount <= 5000)
);

-- Applications table
CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    student_id INT NOT NULL,
    proposal TEXT NOT NULL,
    bid_amount DECIMAL(10,2) NOT NULL,
    estimated_days INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id),
    INDEX idx_job_id (job_id),
    INDEX idx_student_id (student_id),
    INDEX idx_status (status),
    UNIQUE KEY unique_application (job_id, student_id),
    CHECK (bid_amount >= 10 AND bid_amount <= 5000)
);

-- Payments table (escrow system)
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    from_user_id INT NOT NULL,
    to_user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    platform_fee DECIMAL(10,2) DEFAULT 0.00,
    type ENUM('escrow', 'payment', 'refund', 'withdrawal') NOT NULL,
    status ENUM('pending', 'completed', 'refunded', 'failed') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id),
    FOREIGN KEY (from_user_id) REFERENCES users(id),
    FOREIGN KEY (to_user_id) REFERENCES users(id),
    INDEX idx_job_id (job_id),
    INDEX idx_from_user_id (from_user_id),
    INDEX idx_to_user_id (to_user_id),
    INDEX idx_status (status),
    INDEX idx_type (type)
);

-- Disputes table
CREATE TABLE disputes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    client_id INT NOT NULL,
    student_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reason VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open', 'resolved', 'closed') DEFAULT 'open',
    resolution ENUM('full_refund', 'partial_refund', 'full_payment', 
                    'partial_payment', 'split', 'other') NULL,
    winner_id INT NULL,
    admin_notes TEXT,
    resolved_at TIMESTAMP NULL,
    resolved_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (resolved_by) REFERENCES users(id),
    INDEX idx_job_id (job_id),
    INDEX idx_status (status),
    INDEX idx_client_id (client_id),
    INDEX idx_student_id (student_id)
);

-- Messages table (with dispute access control)
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    has_dispute BOOLEAN DEFAULT FALSE,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id),
    INDEX idx_job_id (job_id),
    INDEX idx_sender_id (sender_id),
    INDEX idx_receiver_id (receiver_id),
    INDEX idx_has_dispute (has_dispute),
    INDEX idx_created_at (created_at)
);

-- Transactions table (for earnings history) - MODIFIED: user_id can be NULL for platform fees
CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,  -- Changed from NOT NULL to NULL for platform fees
    job_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('earning', 'withdrawal', 'refund', 'platform_fee') NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (job_id) REFERENCES jobs(id),
    INDEX idx_user_id (user_id),
    INDEX idx_job_id (job_id),
    INDEX idx_type (type)
);

-- Logs table (system activity tracking) - Already has NULL user_id
CREATE TABLE logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,  -- Already allows NULL for system logs
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Settings table (platform configuration)
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
    ('platform_fee', '10'),
    ('escrow_days', '7'),
    ('require_id_verification', '1'),
    ('max_job_amount', '5000'),
    ('min_job_amount', '10'),
    ('admin_email', 'admin@unifreelance.local'),
    ('system_email', 'noreply@unifreelance.local'),
    ('max_login_attempts', '5'),
    ('session_timeout', '30');

-- Insert sample admin user (password: admin123)
-- Note: You'll need to generate the hash and update this after setup
INSERT INTO users (username, email, password, role, status) VALUES
    ('admin', 'admin@unifreelance.local', '$2y$10$YourHashHere', 'admin', 'active');

-- Insert sample users (passwords are 'password123' hashed)
INSERT INTO users (username, email, password, role, status) VALUES
    ('john_student', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active'),
    ('jane_student', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active'),
    ('tech_company', 'tech@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'active'),
    ('design_agency', 'design@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'active');

-- Insert sample students
INSERT INTO students (user_id, full_name, university, major, year, skills, bio, id_verified) VALUES
    (2, 'John Doe', 'State University', 'Computer Science', 'Junior', 'PHP,JavaScript,HTML,CSS', 'Experienced web developer', 'verified'),
    (3, 'Jane Smith', 'Tech College', 'Graphic Design', 'Senior', 'Photoshop,Illustrator,UI/UX', 'Creative designer', 'verified');

-- Insert sample clients
INSERT INTO clients (user_id, company_name, contact_person, industry, website, bio, id_verified) VALUES
    (4, 'Tech Solutions Inc', 'Mike Johnson', 'Technology', 'https://techsolutions.com', 'Software development company', 'verified'),
    (5, 'Creative Designs LLC', 'Sarah Wilson', 'Design', 'https://creativedesigns.com', 'Graphic design agency', 'verified');

-- Insert sample jobs
INSERT INTO jobs (client_id, title, description, category, skills_required, budget_type, amount, deadline, status) VALUES
    (4, 'Website Redesign', 'Need to redesign our company website with modern look and feel.', 'web_development', 'HTML,CSS,JavaScript,Responsive Design', 'fixed', 500.00, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'in_progress'),
    (4, 'Mobile App Development', 'Develop a cross-platform mobile app for task management.', 'mobile_development', 'React Native,JavaScript,Firebase', 'fixed', 1500.00, DATE_ADD(CURDATE(), INTERVAL 60 DAY), 'open'),
    (5, 'Logo Design', 'Create a new logo for our startup company.', 'graphic_design', 'Logo Design,Branding,Adobe Illustrator', 'fixed', 200.00, DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'completed'),
    (5, 'Social Media Graphics', 'Design graphics for our social media campaigns.', 'graphic_design', 'Graphic Design,Social Media,Canva', 'hourly', 25.00, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'open');

-- Update job 1 to have a student assigned
UPDATE jobs SET student_id = 2, status = 'in_progress' WHERE id = 1;

-- Update job 3 to be completed
UPDATE jobs SET student_id = 3, status = 'completed' WHERE id = 3;

-- Insert sample applications
INSERT INTO applications (job_id, student_id, proposal, bid_amount, estimated_days, status) VALUES
    (2, 2, 'I have experience with React Native and Firebase. I can deliver a high-quality app within deadline.', 1400.00, 45, 'pending'),
    (2, 3, 'I am a quick learner and can adapt to React Native. My design skills will ensure a beautiful UI.', 1450.00, 50, 'pending'),
    (4, 2, 'I have experience with graphic design and Canva. I can create engaging social media content.', 20.00, 5, 'accepted');

-- Insert sample payments
INSERT INTO payments (job_id, from_user_id, to_user_id, amount, platform_fee, type, status) VALUES
    (1, 4, 2, 500.00, 50.00, 'escrow', 'pending'),
    (3, 5, 3, 200.00, 20.00, 'payment', 'completed');

-- Insert sample dispute
INSERT INTO disputes (job_id, client_id, student_id, amount, reason, description, status) VALUES
    (1, 4, 2, 500.00, 'quality_issue', 'The delivered work does not meet the quality standards we discussed.', 'open');

-- Insert sample messages
INSERT INTO messages (job_id, sender_id, receiver_id, content, has_dispute) VALUES
    (1, 4, 2, 'Hi, when do you think you can start on the website redesign?', FALSE),
    (1, 2, 4, 'I can start tomorrow. Do you have any specific color preferences?', FALSE),
    (1, 4, 2, 'We want a blue theme. Also, the design needs to be mobile-friendly.', TRUE),
    (1, 2, 4, 'I understand. However, mobile optimization was not in the original scope.', TRUE);

-- Insert sample transactions (FIXED: Platform fee uses NULL instead of user_id=0)
INSERT INTO transactions (user_id, job_id, amount, type, status) VALUES
    (3, 3, 180.00, 'earning', 'completed'),
    (NULL, 3, 20.00, 'platform_fee', 'completed');

-- Insert sample logs (some with NULL user_id for system logs)
INSERT INTO logs (user_id, action, details, ip_address, user_agent) VALUES
    (2, 'user_login', 'Student logged in', '192.168.1.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
    (4, 'job_created', 'Client created job: Website Redesign', '192.168.1.2', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'),
    (2, 'job_application', 'Student applied for job: Mobile App Development', '192.168.1.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
    (3, 'payment_received', 'Student received payment for job: Logo Design', '192.168.1.3', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15'),
    (NULL, 'system_startup', 'System initialized and started', '127.0.0.1', 'System/1.0');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_jobs_status_client ON jobs(status, client_id);
CREATE INDEX idx_jobs_status_student ON jobs(status, student_id);
CREATE INDEX idx_applications_job_status ON applications(job_id, status);
CREATE INDEX idx_messages_job_users ON messages(job_id, sender_id, receiver_id);
CREATE INDEX idx_payments_job_status ON payments(job_id, status);
CREATE INDEX idx_disputes_job_status ON disputes(job_id, status);

-- Create views for reporting
CREATE VIEW view_job_summary AS
SELECT 
    j.id,
    j.title,
    j.amount,
    j.status,
    j.deadline,
    c.contact_person as client_name,
    s.full_name as student_name,
    COUNT(a.id) as application_count
FROM jobs j
LEFT JOIN clients c ON j.client_id = c.user_id
LEFT JOIN students s ON j.student_id = s.user_id
LEFT JOIN applications a ON j.id = a.job_id AND a.status = 'pending'
GROUP BY j.id;

CREATE VIEW view_platform_revenue AS
SELECT 
    DATE(created_at) as date,
    COUNT(*) as transaction_count,
    SUM(amount) as total_amount,
    SUM(platform_fee) as platform_revenue
FROM payments 
WHERE status = 'completed'
GROUP BY DATE(created_at);

CREATE VIEW view_user_activity AS
SELECT 
    u.id,
    u.username,
    u.role,
    u.status,
    COUNT(DISTINCT j.id) as job_count,
    COUNT(DISTINCT a.id) as application_count,
    COUNT(DISTINCT m.id) as message_count,
    MAX(l.created_at) as last_activity
FROM users u
LEFT JOIN jobs j ON u.id = j.client_id OR u.id = j.student_id
LEFT JOIN applications a ON u.id = a.student_id
LEFT JOIN messages m ON u.id = m.sender_id OR u.id = m.receiver_id
LEFT JOIN logs l ON u.id = l.user_id
WHERE u.role != 'admin'
GROUP BY u.id;

DELIMITER $$

-- Trigger to update student earnings
CREATE TRIGGER update_student_earnings
AFTER UPDATE ON payments
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND NEW.type = 'payment' AND OLD.status != 'completed' THEN
        UPDATE students 
        SET total_earnings = total_earnings + (NEW.amount - NEW.platform_fee)
        WHERE user_id = NEW.to_user_id;
    END IF;
END$$

-- Trigger to update client spending
CREATE TRIGGER update_client_spending
AFTER UPDATE ON payments
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND NEW.type IN ('payment', 'escrow') AND OLD.status != 'completed' THEN
        UPDATE clients 
        SET total_spent = total_spent + NEW.amount
        WHERE user_id = NEW.from_user_id;
    END IF;
END$$

-- Trigger to mark messages as during dispute
CREATE TRIGGER mark_messages_dispute
AFTER INSERT ON disputes
FOR EACH ROW
BEGIN
    UPDATE messages 
    SET has_dispute = TRUE 
    WHERE job_id = NEW.job_id;
END$$

-- Trigger to log user status changes
CREATE TRIGGER log_user_status_change
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO logs (user_id, action, details)
        VALUES (NEW.id, 'user_status_change', 
                CONCAT('Status changed from ', OLD.status, ' to ', NEW.status));
    END IF;
END$$

DELIMITER ;

-- Create stored procedure for monthly report
DELIMITER $$

CREATE PROCEDURE GenerateMonthlyReport(IN report_month DATE)
BEGIN
    SELECT 
        DATE_FORMAT(report_month, '%Y-%m') as month,
        COUNT(DISTINCT u.id) as total_users,
        SUM(CASE WHEN u.role = 'student' THEN 1 ELSE 0 END) as total_students,
        SUM(CASE WHEN u.role = 'client' THEN 1 ELSE 0 END) as total_clients,
        COUNT(DISTINCT j.id) as total_jobs,
        SUM(CASE WHEN j.status = 'completed' THEN 1 ELSE 0 END) as completed_jobs,
        SUM(CASE WHEN j.status = 'completed' THEN j.amount ELSE 0 END) as total_transaction_value,
        SUM(p.platform_fee) as platform_revenue,
        COUNT(DISTINCT d.id) as total_disputes
    FROM users u
    LEFT JOIN jobs j ON (u.id = j.client_id OR u.id = j.student_id) 
        AND MONTH(j.created_at) = MONTH(report_month) 
        AND YEAR(j.created_at) = YEAR(report_month)
    LEFT JOIN payments p ON j.id = p.job_id 
        AND p.status = 'completed'
        AND MONTH(p.created_at) = MONTH(report_month) 
        AND YEAR(p.created_at) = YEAR(report_month)
    LEFT JOIN disputes d ON j.id = d.job_id
        AND MONTH(d.created_at) = MONTH(report_month) 
        AND YEAR(d.created_at) = YEAR(report_month)
    WHERE u.role != 'admin';
END$$

DELIMITER ;

-- Create event for auto-releasing payments after escrow period
DELIMITER $$

CREATE EVENT IF NOT EXISTS auto_release_payments
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    -- Release payments for jobs completed more than 7 days ago
    UPDATE payments p
    JOIN jobs j ON p.job_id = j.id
    SET p.status = 'completed',
        p.updated_at = NOW(),
        j.status = 'paid',
        j.payment_released_at = NOW()
    WHERE p.status = 'pending'
    AND p.type = 'escrow'
    AND j.status = 'completed'
    AND j.payment_released_at IS NULL
    AND DATEDIFF(NOW(), j.updated_at) >= 7;
END$$

DELIMITER ;

-- Enable event scheduler
SET GLOBAL event_scheduler = ON;

COMMIT;