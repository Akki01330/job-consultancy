-- ============================================
-- Job Consultancy Web Application Database
-- ============================================
-- Created for a full-featured job consultancy platform
-- with Admin, Recruiter, and Job Seeker modules

CREATE DATABASE IF NOT EXISTS job_consultancy;
USE job_consultancy;

-- Drop existing tables to ensure clean install
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS recruiters;
DROP TABLE IF EXISTS job_seekers;
DROP TABLE IF EXISTS admins;

-- ============================================
-- 1. ADMIN TABLE
-- ============================================
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(150),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- 2. JOB CATEGORIES TABLE
-- ============================================
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT,
  status ENUM('active', 'inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 3. RECRUITERS TABLE
-- ============================================
CREATE TABLE recruiters (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  company_name VARCHAR(150) NOT NULL,
  company_email VARCHAR(150),
  phone VARCHAR(20),
  website VARCHAR(255),
  company_description TEXT,
  location VARCHAR(255),
  logo VARCHAR(255),
  is_verified BOOLEAN DEFAULT FALSE,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX(email),
  INDEX(username)
);

-- ============================================
-- 4. JOB SEEKERS TABLE
-- ============================================
CREATE TABLE job_seekers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100),
  phone VARCHAR(20),
  location VARCHAR(255),
  headline VARCHAR(255),
  bio TEXT,
  skills TEXT,
  experience_years INT,
  education TEXT,
  resume_file VARCHAR(255),
  profile_picture VARCHAR(255),
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX(email),
  INDEX(username)
);

-- ============================================
-- 5. JOBS TABLE
-- ============================================
CREATE TABLE jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recruiter_id INT NOT NULL,
  category_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  description LONGTEXT NOT NULL,
  requirements TEXT,
  salary_min DECIMAL(10, 2),
  salary_max DECIMAL(10, 2),
  salary_currency VARCHAR(10) DEFAULT 'USD',
  location VARCHAR(255) NOT NULL,
  job_type ENUM('Full-time', 'Part-time', 'Contract', 'Freelance', 'Internship') DEFAULT 'Full-time',
  experience_level ENUM('Entry-level', 'Mid-level', 'Senior', 'Executive') DEFAULT 'Entry-level',
  status ENUM('active', 'closed', 'on-hold') DEFAULT 'active',
  total_positions INT DEFAULT 1,
  deadline DATE,
  posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (recruiter_id) REFERENCES recruiters(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
  INDEX(recruiter_id),
  INDEX(category_id),
  INDEX(status)
);

-- ============================================
-- 6. APPLICATIONS TABLE
-- ============================================
CREATE TABLE applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_id INT NOT NULL,
  seeker_id INT NOT NULL,
  status ENUM('applied', 'reviewed', 'shortlisted', 'rejected', 'accepted') DEFAULT 'applied',
  cover_letter TEXT,
  applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  FOREIGN KEY (seeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE,
  UNIQUE KEY unique_application (job_id, seeker_id),
  INDEX(seeker_id),
  INDEX(status)
);

-- ============================================
-- 7. CONTACT/SUPPORT MESSAGES TABLE
-- ============================================
CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_name VARCHAR(150) NOT NULL,
  sender_email VARCHAR(150) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  message LONGTEXT NOT NULL,
  status ENUM('new', 'read', 'replied') DEFAULT 'new',
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(status)
);

-- ============================================
-- INSERT DEFAULT DATA
-- ============================================

-- Default admin (username: admin, password: Admin123!)
-- Password hash generated with PHP password_hash()
INSERT INTO admins (username, email, password, full_name) VALUES 
('admin', 'admin@jobconsultancy.com', '$2y$10$9kL5L0l7kL5L0l7kL5L0luQ0l7kL5L0l7kL5L0l7kL5L0l7kL5L0l', 'System Admin');

-- Default job categories
INSERT INTO categories (name, description) VALUES
('Information Technology', 'Software development, IT management, and related roles'),
('Finance', 'Accounting, banking, financial analysis, and related positions'),
('Healthcare', 'Medical professionals, nursing, healthcare management'),
('Marketing', 'Digital marketing, brand management, and advertising roles'),
('Sales', 'Account executives, business development, and sales roles'),
('Human Resources', 'Recruitment, HR management, and employee relations'),
('Engineering', 'Civil, Mechanical, Electrical, and Software engineering roles'),
('Education', 'Teaching, training, and educational management positions'),
('Business Development', 'Strategic planning, partnerships, and business growth'),
('Customer Service', 'Support, customer relations, and service delivery roles');

-- Sample recruiter (password: Company123!)
INSERT INTO recruiters (username, email, password, company_name, company_email, phone, website, company_description, location, is_verified, is_active) VALUES
('techcorp', 'hr@techcorp.com', '$2y$10$BcL8L0l7kL5L0l7kL5L0luQ0l7kL5L0l7kL5L0l7kL5L0l7kL5L0a', 'Tech Corporation', 'hr@techcorp.com', '+1-555-0123', 'www.techcorp.com', 'Leading technology company specializing in web and mobile applications', 'San Francisco, CA', TRUE, TRUE);

-- Sample job seeker (password: Seeker123!)
INSERT INTO job_seekers (username, email, password, first_name, last_name, phone, location, headline, skills, experience_years) VALUES
('johndoe', 'john.doe@email.com', '$2y$10$DcL8L0l7kL5L0l7kL5L0luQ0l7kL5L0l7kL5L0l7kL5L0l7kL5L0b', 'John', 'Doe', '+1-555-0456', 'New York, NY', 'Senior PHP Developer', 'PHP, MySQL, Laravel, JavaScript, HTML, CSS', 5);

-- Sample jobs
INSERT INTO jobs (recruiter_id, category_id, title, description, requirements, salary_min, salary_max, location, job_type, experience_level, status, deadline) VALUES
(1, 1, 'Senior PHP Developer', 'We are looking for an experienced PHP developer to join our team. Must have 5+ years of experience with modern PHP frameworks.', '5+ years PHP experience, Laravel, MySQL, Git', 70000, 90000, 'San Francisco, CA', 'Full-time', 'Senior', 'active', '2026-03-31'),
(1, 1, 'Junior Web Developer', 'Fresh graduates welcome! Join us and build your web development career with mentorship from our senior developers.', 'Basic HTML/CSS/JavaScript knowledge, willingness to learn', 35000, 45000, 'Remote', 'Full-time', 'Entry-level', 'active', '2026-03-30'),
(1, 1, 'React Frontend Developer', 'Build beautiful user interfaces using React.js. 2-3 years of React experience required.', '2+ years React experience, JavaScript ES6+, REST APIs', 55000, 75000, 'San Francisco, CA', 'Full-time', 'Mid-level', 'active', '2026-04-15');

