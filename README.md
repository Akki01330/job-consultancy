# ProHire Consultancy - Job Portal Application

## 📋 Project Overview

**ProHire Consultancy** is a comprehensive, fully-functional Job Consultancy Web Application built with Core PHP and MySQL. It serves as a bridge between job seekers, recruiters, and an admin panel to manage the entire ecosystem.

### Objective
Create a professional, secure, and scalable job matching platform that:
- Allows job seekers to search, apply, and track job applications
- Enables recruiters to post jobs and manage applicants
- Provides admins with comprehensive management and analytics tools
- Implements security best practices for user data protection

---

## 🛠 Technologies Used

- **Backend:** Core PHP (No Framework)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, Bootstrap 5
- **Additional Libraries:** Font Awesome Icons, Bootstrap Icons
- **Security:** Password Hashing (bcrypt), Prepared Statements, Session Management
- **Version Control:** Git

---

## ✨ Key Features

### 1️⃣ Admin Panel Features
- **Secure Admin Login & Logout** with session management
- **Dashboard with Statistics:**
  - Total Jobs Posted
  - Total Job Seekers
  - Total Recruiters
  - Total Applications
  - Pending Approvals
- **Job Management:** Add, Edit, Delete, Approve job postings
- **Recruiter verification:** Approve/Reject recruiter accounts
- **User Management:** Manage job seekers and recruiters
- **Job Categories:** Create and manage job categories
- **Contact Support:** View and respond to contact messages
- **Password Management:** Change admin password

### 2️⃣ Recruiter Module
- **Recruiter Registration & Login** with email verification
- **Company Profile Management:**
  - Company information
  - Company logo/branding
  - Office locations
  - Company description
- **Job Management:**
  - Post new jobs with detailed descriptions
  - Edit existing job postings
  - Delete job listings
  - Set salary ranges and requirements
  - Manage job deadlines
- **Applicant Management:**
  - View all applicants per job
  - Track application status
  - Download resumes (PDF/DOC)
  - Shortlist candidates
  - Reject applicants
  - Send messages to candidates
- **Password Management:** Change account password
- **Account Status:** Monitor verification status

### 3️⃣ Job Seeker Module
- **Job Seeker Registration & Login** with secure authentication
- **Profile Management:**
  - Personal information (name, contact)
  - Skills and expertise
  - Experience level
  - Education details
  - Headline/Professional summary
- **Resume Management:**
  - Upload resume (PDF/DOC)
  - Download uploaded resume
  - Update resume
- **Job Search & Application:**
  - Advanced search with filters
  - Filter by location, category, salary, job type
  - Apply for jobs
  - View application status
  - Track pending, shortlisted, and accepted applications
- **Application Tracking:**
  - View all applications
  - Monitor status updates
  - Receive notifications
- **Profile Updates:**
  - Edit personal information
  - Update skills
  - Add experience
- **Password Management:** Change account password

### 4️⃣ Frontend Features
- **Attractive Homepage:**
  - Hero section with call-to-action
  - Featured jobs carousel
  - About us section
  - Statistics section
  - Testimonials
- **Job Listing Page:**
  - Advanced search functionality
  - Filter by multiple criteria
  - Pagination for easy browsing
  - Job cards with key information
- **Job Details Page:**
  - Complete job description
  - Company information
  - Application form
  - Share to social media
- **Responsive Design:** Mobile-friendly Bootstrap
- **Navigation:** Intuitive navbar with user roles
- **Footer:** Rich footer with links and information

---

## 📊 Database Schema

### Tables and Relationships

#### **admins**
```sql
id (INT, Primary Key)
username (VARCHAR)
email (VARCHAR)
password (VARCHAR - hashed)
full_name (VARCHAR)
created_at, updated_at (TIMESTAMP)
```

#### **recruiters**
```sql
id (INT, Primary Key)
username (VARCHAR)
email (VARCHAR)
password (VARCHAR - hashed)
company_name (VARCHAR)
company_email (VARCHAR)
phone (VARCHAR)
website (VARCHAR)
company_description (TEXT)
location (VARCHAR)
logo (VARCHAR)
is_verified (BOOLEAN)
is_active (BOOLEAN)
created_at, updated_at (TIMESTAMP)
```

#### **job_seekers**
```sql
id (INT, Primary Key)
username (VARCHAR)
email (VARCHAR)
password (VARCHAR - hashed)
first_name (VARCHAR)
last_name (VARCHAR)
phone (VARCHAR)
location (VARCHAR)
headline (VARCHAR)
bio (TEXT)
skills (TEXT)
experience_years (INT)
education (TEXT)
resume_file (VARCHAR)
profile_picture (VARCHAR)
is_active (BOOLEAN)
created_at, updated_at (TIMESTAMP)
```

#### **categories**
```sql
id (INT, Primary Key)
name (VARCHAR)
description (TEXT)
status (ENUM: active, inactive)
created_at (TIMESTAMP)
```

#### **jobs**
```sql
id (INT, Primary Key)
recruiter_id (INT, Foreign Key)
category_id (INT, Foreign Key)
title (VARCHAR)
description (LONGTEXT)
requirements (TEXT)
salary_min, salary_max (DECIMAL)
salary_currency (VARCHAR)
location (VARCHAR)
job_type (ENUM: Full-time, Part-time, Contract, Freelance, Internship)
experience_level (ENUM: Entry-level, Mid-level, Senior, Executive)
status (ENUM: active, closed, on-hold)
total_positions (INT)
deadline (DATE)
posted_at, updated_at (TIMESTAMP)
```

#### **applications**
```sql
id (INT, Primary Key)
job_id (INT, Foreign Key)
seeker_id (INT, Foreign Key)
status (ENUM: applied, reviewed, shortlisted, rejected, accepted)
cover_letter (TEXT)
applied_at, updated_at (TIMESTAMP)
UNIQUE(job_id, seeker_id)
```

#### **contact_messages**
```sql
id (INT, Primary Key)
sender_name (VARCHAR)
sender_email (VARCHAR)
subject (VARCHAR)
message (LONGTEXT)
status (ENUM: new, read, replied)
sent_at (TIMESTAMP)
```

---

## 🔐 Security Features

1. **Password Security:**
   - Bcrypt hashing with cost factor 10
   - Password strength validation
   - Minimum 8-character requirement

2. **SQL Injection Prevention:**
   - Prepared statements for all database queries
   - Parameter binding with type checking

3. **XSS Protection:**
   - HTML escaping with htmlspecialchars()
   - Sanitization with custom sanitize() function

4. **Session Management:**
   - Secure session handling
   - User role-based access control
   - Session timeout mechanism

5. **Input Validation:**
   - Email format validation
   - File type and size validation
   - Data type validation

6. **Data Protection:**
   - Unique email and username constraints
   - Foreign key relationships
   - Timestamp tracking for audits

---

## 📁 Project Structure

```
job-consultancy/
│
├── admin/
│   ├── index.php                 (Dashboard)
│   ├── login.php                 (Admin Login)
│   ├── manage_jobs.php          (Manage All Jobs)
│   ├── manage_users.php         (Manage Job Seekers)
│   ├── manage_recruiters.php    (Manage Recruiters)
│   ├── manage_categories.php    (Manage Categories)
│   ├── contact_messages.php     (View Messages)
│   ├── change_password.php      (Admin Password)
│   ├── view_reports.php         (Analytics)
│   └── system_settings.php      (App Settings)
│
├── recruiter/
│   ├── index.php                (Dashboard)
│   ├── login.php                (Recruiter Login)
│   ├── register.php             (Recruiter Register)
│   ├── post_job.php             (Post New Job)
│   ├── manage_jobs.php          (Manage Jobs)
│   ├── edit_job.php             (Edit Job)
│   ├── view_applicants.php      (View Applicants)
│   ├── edit_profile.php         (Edit Company Profile)
│   └── change_password.php      (Change Password)
│
├── jobseeker/
│   ├── index.php                (Dashboard)
│   ├── login.php                (Seeker Login)
│   ├── register.php             (Seeker Register)
│   ├── profile.php              (Manage Profile)
│   ├── applications.php         (View Applications)
│   ├── upload_resume.php        (Upload Resume)
│   ├── browse_jobs.php          (Browse Jobs)
│   └── change_password.php      (Change Password)
│
├── includes/
│   ├── header.php               (Navigation & Bootstrap)
│   ├── footer.php               (Footer & Scripts)
│   └── db.php                   (Database Connection)
│
├── assets/
│   ├── css/
│   │   └── style.css            (Custom Styles)
│   ├── js/
│   │   └── script.js            (Custom JavaScript)
│   └── images/
│       └── (placeholder folder)
│
├── uploads/
│   ├── resumes/                 (User Resume Files)
│   ├── logos/                   (Company Logos)
│   └── profiles/                (Profile Pictures)
│
├── config.php                   (Configuration & Helpers)
├── index.php                    (Homepage)
├── jobs.php                     (Job Listings)
├── job_detail.php               (Job Details)
├── login.php                    (Admin Login - Root)
├── logout.php                   (Logout Handle)
├── contact.php                  (Contact Form)
├── database.sql                 (Database Schema)
└── README.md                    (This File)
```

---

## 🚀 Installation Steps

### Prerequisites
- XAMPP/WAMP Server installed
- PHP 7.4+
- MySQL Database
- Modern Web Browser

### Step 1: Extract Files
```bash
Extract the project to: C:\xampp\htdocs\job-consultancy
(or your server's document root)
```

### Step 2: Create Database
1. Open phpmyadmin: `http://localhost/phpmyadmin`
2. Click **New** to create a new database
3. Name: `job_consultancy`
4. Click **Create**

### Step 3: Import Database Schema
1. Select the `job_consultancy` database
2. Click **Import** tab
3. Choose `database.sql` from project folder
4. Click **Go**

Alternatively, run SQL commands:
```sql
-- Copy entire content of database.sql and paste in SQL tab
```

### Step 4: Configure Application
The application is already configured in `config.php` for default XAMPP setup:
- Host: `localhost`
- User: `root`
- Password: (empty)
- Database: `job_consultancy`

If your setup is different, edit `config.php`:
```php
define('DB_HOST', 'your_host');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_password');
define('DB_NAME', 'job_consultancy');
```

### Step 5: Access the Application
1. Start XAMPP (Apache & MySQL)
2. Navigate to: `http://localhost/job-consultancy`
3. You should see the homepage

### Step 6: Default Credentials

**Admin Login:**
- URL: `http://localhost/job-consultancy/admin/login.php`
- Username: `admin`
- Password: `Admin123!`

**Sample Recruiter:**
- Username: `techcorp`
- Password: `Company123!`

**Sample Job Seeker:**
- Username: `johndoe`
- Password: `Seeker123!`

---

## 💡 Usage Guide

### For Job Seekers
1. **Register** at `/jobseeker/register.php`
2. **Complete Profile** with personal details
3. **Upload Resume** in PDF or DOC format
4. **Browse Jobs** using advanced filters
5. **Apply for Jobs** with optional cover letter
6. **Track Applications** in dashboard

### For Recruiters
1. **Register** at `/recruiter/register.php`
2. **Wait for Admin Approval** (check email)
3. **Setup Company Profile** with details
4. **Post Jobs** with detailed descriptions
5. **Manage Applicants** and track status
6. **Shortlist/Reject** candidates

### For Admins
1. **Login** at `/admin/login.php`
2. **Verify Recruiter Accounts** in dashboard
3. **Manage Job Categories** for organization
4. **Monitor Jobs & Applications**
5. **Manage User Accounts** if needed
6. **View System Analytics** and reports

---

## 🔧 Advanced Features

### Email Notifications (Future Enhancement)
Configure in `config.php`:
```php
define('MAIL_HOST', 'smtp.mailtrap.io');
define('MAIL_USERNAME', 'your_username');
define('MAIL_PASSWORD', 'your_password');
```

### Custom Email Templates
Edit in `admin/email_templates.php`:
- Job application confirmation
- Status update notifications
- Recruiter verification emails
- Password reset emails

### File Upload Validation
Supported formats:
- **Resumes:** PDF, DOC, DOCX
- **Images:** JPG, JPEG, PNG, GIF
- **Max Size:** 5 MB

---

## 📈 Future Enhancements

1. **Advanced Features:**
   - AI-powered job recommendations
   - Email notification system
   - SMS alerts
   - Video interview integration
   - Analytics dashboard with charts

2. **Improvements:**
   - Dark mode toggle
   - Multi-language support
   - API development (REST)
   - Mobile app
   - Payment gateway integration

3. **Performance:**
   - Caching mechanism
   - Database query optimization
   - CDN integration
   - Load balancing

4. **Security:**
   - Two-factor authentication
   - OAuth integration
   - CAPTCHA on forms
   - Rate limiting

---

## 🐛 Troubleshooting

### Database Connection Error
**Issue:** "Database connection failed"
**Solution:**
1. Check XAMPP MySQL is running
2. Verify credentials in `config.php`
3. Database `job_consultancy` exists

### File Upload Error
**Issue:** "File upload failed"
**Solution:**
1. Check `uploads/` folder exists and writable
2. Verify file format (PDF/DOC for resume)
3. File size under 5 MB
4. Folder permissions set to 755

### Login Issues
**Issue:** "Invalid password" but password is correct
**Solution:**
1. Clear browser cookies
2. First time? Check if account is active
3. For recruiters: Account must be verified by admin

### 404 Page Not Found
**Issue:** Page shows error
**Solution:**
1. Check file name spelling
2. Verify file exists in correct folder
3. Check `config.php` APP_URL setting

---

## 📞 Support & Contact

For issues or questions:
- **Email:** support@jobconsultancy.com
- **Phone:** +1-555-0123
- **Website:** www.jobconsultancy.com

---

## 📄 License

This project is licensed under the MIT License. See LICENSE file for details.

---

## 👥 Contributors

- **Development Team:** ProHire Development Group
- **Initial Version:** 2026.02

---

## 🙏 Acknowledgments

- Bootstrap framework for responsive design
- FontAwesome for icons
- PHP community for security best practices

---

**Last Updated:** February 17, 2026  
**Version:** 1.0.0  
**Status:** Fully Functional

Thank you for using ProHire Consultancy! Happy job hunting! 🎯
