# ProHire Consultancy - Installation & Setup Guide

## 🖥️ System Requirements

### Minimum Requirements:
- **PHP:** 7.4 or higher
- **MySQL:** 5.7 or higher
- **Web Server:** Apache (included with XAMPP)
- **RAM:** 512 MB minimum
- **Disk Space:** 50 MB minimum
- **Browser:** Modern browser with JavaScript enabled

### Recommended:
- **PHP:** 8.0+
- **MySQL:** 8.0+
- **RAM:** 2 GB or more
- **SSD Storage:** 100 MB+

---

## ⚙️ Step-by-Step Installation

### Step 1: Download & Extract Project

#### Option A: Using XAMPP on Windows
```
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP in C:\xampp\
3. Download the project ZIP file
4. Extract to: C:\xampp\htdocs\job-consultancy
```

#### Option B: Using WAMP on Windows
```
1. Download WAMP from http://www.wampserver.com/
2. Install WAMP
3. Extract project to: C:\wamp\www\job-consultancy
```

#### Option C: Using LAMP on Linux/Mac
```
1. Your web root is usually /var/www/html/
2. Extract project: /var/www/html/job-consultancy
```

### Step 2: Start Your Server

#### XAMPP:
```
1. Open XAMPP Control Panel
2. Click "Start" for Apache
3. Click "Start" for MySQL
4. Wait for "Running" status on both
```

#### WAMP:
```
1. Click WAMP icon in system tray
2. All services should automatically start
3. Icon should turn green
```

### Step 3: Create Database

#### Method 1: Using phpMyAdmin (Easiest)
```
1. Open browser: http://localhost/phpmyadmin
   (For WAMP: http://localhost/phpmyadmin/)
2. Login (default: admin / no password)
3. Click "New" in left sidebar
4. Database name: job_consultancy
5. Collation: utf8mb4_unicode_ci
6. Click "Create"
7. Select the newly created database
8. Click "Import" tab
9. Click "Choose File"
10. Select database.sql from project
11. Click "Go"
```

#### Method 2: Using Command Line
```bash
# Windows (PowerShell as Administrator)
cd "C:\xampp\mysql\bin"
mysql -u root -p
(Just press Enter when asked for password)

# Then in MySQL prompt:
CREATE DATABASE job_consultancy;
USE job_consultancy;
SOURCE C:/xampp/htdocs/job-consultancy/database.sql;
EXIT;
```

### Step 4: Configure Application

The application comes pre-configured for XAMPP default settings. If needed, edit `config.php`:

```php
// Database Configuration
define('DB_HOST', 'localhost');    // Your database host
define('DB_USER', 'root');          // Your database user
define('DB_PASS', '');              // Your database password
define('DB_NAME', 'job_consultancy'); // Your database name

// Application Settings
define('APP_URL', 'http://localhost/job-consultancy'); // Your app URL
```

### Step 5: Set File Permissions

#### On Windows:
```
Usually not needed, but if you get permission errors:
1. Right-click uploads folder
2. Properties → Security
3. Edit, add permissions for IIS_IUSRS
```

#### On Linux/Mac:
```bash
chmod 755 uploads/
chmod 755 uploads/resumes/
chmod 755 uploads/logos/
chmod 755 uploads/profiles/
chmod 644 config.php
```

### Step 6: Verify Installation

1. Open browser: `http://localhost/job-consultancy`
2. You should see the homepage
3. Click "Browse Jobs" to see if database is working
4. All pages should load without errors

---

## 🔑 Login Credentials

### Admin Account (Pre-Created)
```
URL: http://localhost/job-consultancy/admin/login.php
Username: admin
Password: Admin123!
```

### Sample Recruiter Account (Pre-Created)
```
URL: http://localhost/job-consultancy/recruiter/login.php
Username: techcorp
Password: Company123!
Note: Account is already verified
```

### Sample Job Seeker Account (Pre-Created)
```
URL: http://localhost/job-consultancy/jobseeker/login.php
Username: johndoe
Password: Seeker123!
```

### Create New Account
```
Job Seeker: /jobseeker/register.php
Recruiter: /recruiter/register.php
(Admin accounts can only be created via database)
```

---

## 🔧 Configuration Options

### Edit Email Configuration
Edit `config.php` to enable email notifications:

```php
define('MAIL_FROM', 'noreply@jobconsultancy.com');
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', '587');
define('MAIL_USERNAME', 'your_email@gmail.com');
define('MAIL_PASSWORD', 'your_app_password');
```

### Change Application Name
Edit `config.php`:
```php
define('APP_NAME', 'Your Company Name');
```

### Change Items Per Page
Edit `config.php`:
```php
define('ITEMS_PER_PAGE', 10); // Change 10 to desired number
```

### Change Session Timeout
Edit `config.php`:
```php
define('SESSION_TIMEOUT', 1800); // In seconds (1800 = 30 minutes)
```

---

## 🚀 Quick Start Checklist

- [ ] XAMPP/WAMP started and running
- [ ] Database created: `job_consultancy`
- [ ] database.sql imported successfully
- [ ] Website accessible at localhost/job-consultancy
- [ ] Can login with admin credentials
- [ ] Can view sample jobs
- [ ] Can view admin dashboard
- [ ] File uploads folder writable

---

## 🐛 Troubleshooting

### 1. "Could not connect to database" Error
**Cause:** MySQL not running or wrong credentials
**Solution:**
```
1. Start MySQL in XAMPP Control Panel
2. Check DB credentials in config.php
3. Verify database job_consultancy exists
4. Try connecting in phpMyAdmin first
```

### 2. "404 Not Found" for any page
**Cause:** File not in correct location
**Solution:**
```
1. Check file exists in the folder
2. Verify URL path is correct
3. Check path in config.php APP_URL
4. Restart Apache
```

### 3. "Permission Denied" when uploading files
**Cause:** Insufficient folder permissions
**Solution:**
```
# Windows: Run as Administrator, or
# Edit uploads folder security permissions

# Linux/Mac:
sudo chmod 755 uploads/
sudo chown -R www-data:www-data uploads/
```

### 4. "Session not working" / Cannot stay logged in
**Cause:** PHP session configuration
**Solution:**
```
1. Check tmp folder exists and is writable
2. Verify session.save_path in php.ini
3. Clear browser cookies
4. Try in private/incognito browser window
```

### 5. Password hashing issues / "Invalid password"
**Cause:** Database using old MD5 passwords
**Solution:**
```
# Delete old admin and re-insert:
DELETE FROM admins;
INSERT INTO admins (username, email, password, full_name) 
VALUES ('admin', 'admin@jobconsultancy.com', 
        '$2y$10$...', 'System Admin');

# Use a bcrypt password generator online
```

### 6. "CORS" or JavaScript errors
**Cause:** Bootstrap/JavaScript not loading
**Solution:**
```
1. Check internet connection
2. Check developer console (F12) for errors
3. Clear browser cache
4. Disable browser extensions
```

### 7. White screen / PHP errors
**Cause:** PHP error reporting disabled
**Solution:**
```
# Edit config.php to see errors:
error_reporting(E_ALL);
ini_set('display_errors', 1);

# Or check web server error logs:
# XAMPP: C:\xampp\apache\logs\error.log
# WAMP: C:\wamp\logs\apache_error.log
```

---

## 📊 Database Verification

After import, verify all tables exist:

```sql
SHOW TABLES; -- Should show 7 tables

-- Or check specific table:
SELECT COUNT(*) FROM admins;          -- Should be 1
SELECT COUNT(*) FROM categories;      -- Should be 10
SELECT COUNT(*) FROM recruiters;      -- Should be 1
SELECT COUNT(*) FROM job_seekers;     -- Should be 1
SELECT COUNT(*) FROM jobs;            -- Should be 3
```

---

## 🔐 Initial Security Steps

After fresh installation:

1. **Change Admin Password**
   - Login to admin panel
   - Go to Change Password
   - Set a strong password

2. **Update Sample Data**
   - Delete sample recruiter and job seeker
   - Create new test accounts yourself

3. **Configure Email** (if using)
   - Update email credentials in config.php
   - Send test email to verify

4. **Update Company Info**
   - Edit APP_NAME in config.php
   - Update logo paths
   - Customize email templates

5. **Enable HTTPS** (if live server)

6. **Database Backups**
   - Schedule regular database backups
   - Export to safe location

---

## 📁 Project Structure After Installation

```
job-consultancy/
├── admin/                   ✓ Created
├── recruiter/              ✓ Created
├── jobseeker/              ✓ Created
├── includes/               ✓ Created
│   ├── header.php         ✓ Updated
│   ├── footer.php         ✓ Updated
│   └── db.php             ✓ Updated
├── assets/                ✓ Created
│   ├── css/style.css      ✓ Updated
│   └── js/script.js       ✓ Created
├── uploads/               ✓ (Needs to be writable)
├── config.php             ✓ Updated
├── database.sql           ✓ Imported
├── index.php              ✓ Updated
├── jobs.php               ✓ Updated
├── job_detail.php         ✓ Created
├── login.php              ✓ Updated
├── logout.php             ✓ Created
└── README.md              ✓ Created
```

---

## ✅ Testing Each Module

### Test Admin Panel
```
1. Go to: http://localhost/job-consultancy/admin/login.php
2. Login with: admin / Admin123!
3. Should see dashboard with statistics
4. Test: Manage Jobs, Users, Categories
```

### Test Recruiter Module
```
1. Go to: http://localhost/job-consultancy/recruiter/login.php
2. Login with: techcorp / Company123!
3. Should see recruiter dashboard
4. Test: Post Job, View Applicants
```

### Test Job Seeker Module
```
1. Go to: http://localhost/job-consultancy/jobseeker/login.php
2. Login with: johndoe / Seeker123!
3. Should see seeker dashboard
4. Test: Browse Jobs, Apply
```

### Test Public Functions
```
1. Visit: http://localhost/job-consultancy/
2. Click: Browse Jobs
3. See job list and can apply (after login)
```

---

## 🌐 Accessing from Other Devices

### Local Network Access
```
Find your IP: ipconfig (Windows) or ifconfig (Linux)
Access from other device: http://YOUR.IP.ADDRESS/job-consultancy
Example: http://192.168.1.100/job-consultancy
```

### Using Virtual Host (Advanced)
Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/job-consultancy"
    ServerName jobconsultancy.local
</VirtualHost>
```

Then edit `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 jobconsultancy.local
```

Access at: `http://jobconsultancy.local`

---

## 📦 Deployment to Production

When ready to deploy:

1. **Get a Hosting Account** with:
   - PHP 7.4+
   - MySQL database
   - FTP/SSH access

2. **Upload Files:**
   - Use FileZilla or cPanel File Manager
   - Upload all files except uploads folder

3. **Create Database:**
   - Use cPanel or phpMyAdmin
   - Import database.sql

4. **Update config.php:**
   - Change DB credentials
   - Change APP_URL to your domain
   - Update email settings

5. **Set Permissions:**
   - uploads/ folder: 755
   - config.php: 644

6. **Enable HTTPS:**
   - Install SSL certificate
   - Update APP_URL to https://

---

## 🆘 Getting Help

If you encounter issues:

1. Check the README.md for feature documentation
2. Review error messages carefully
3. Check browser developer console (F12)
4. Look in error logs (php_error_log, apache_error.log)
5. Create a test page to verify PHP works:

```php
<?php
phpinfo();
?>
```

---

## ✨ Next Steps After Installation

1. **Customize the Application:**
   - Edit company name and logo
   - Customize email templates
   - Update color scheme

2. **Add More Test Data:**
   - Create test accounts
   - Post sample jobs
   - Test complete workflow

3. **Enable Additional Features:**
   - Configure email notifications
   - Set up payment integration
   - Add analytics

4. **Plan Deployment:**
   - Choose hosting provider
   - Plan security measures
   - Set up backups

---

**Installation Complete!** 🎉

Your ProHire Consultancy application is now ready to use. Start by logging in to the admin panel and explore all features.

For detailed usage guide, see [README.md](README.md)

---

**Last Updated:** February 17, 2026  
**ProHire Consultancy v1.0.0**
