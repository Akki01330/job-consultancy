# ProHire Consultancy - Quick Start Guide

## ⚡ 5-Minute Setup

Get ProHire running in just 5 minutes!

---

## 🚀 Step 1: Extract Project (30 seconds)

```
Extract job-consultancy.zip to:
C:\xampp\htdocs\job-consultancy  (Windows XAMPP)
or
C:\wamp\www\job-consultancy      (Windows WAMP)
```

---

## 🚀 Step 2: Start Server (30 seconds)

**XAMPP:**
1. Open XAMPP Control Panel
2. Click "Start" on Apache
3. Click "Start" on MySQL
4. Wait for "Running" status

**WAMP:**
1. Click WAMP icon in taskbar
2. Wait for green icon
3. Done!

---

## 🚀 Step 3: Create Database (2 minutes)

Open browser: **http://localhost/phpmyadmin**

### Option A: Using phpMyAdmin (Easiest)
1. Click "New"
2. Name: `job_consultancy`
3. Collation: `utf8mb4_unicode_ci`
4. Click "Create"
5. Click "Import"
6. Select `database.sql` from the project
7. Click "Go"

### Option B: Command Line
```bash
mysql -u root
CREATE DATABASE job_consultancy;
USE job_consultancy;
SOURCE C:/xampp/htdocs/job-consultancy/database.sql;
EXIT;
```

---

## 🚀 Step 4: Access Application (1 minute)

Open browser: **http://localhost/job-consultancy**

You should see the homepage! ✓

---

## 🔑 Login Credentials

### Admin Panel
```
Username: admin
Password: Admin123!
URL: http://localhost/job-consultancy/admin/login.php
```

### Sample Recruiter
```
Username: techcorp
Password: Company123!
URL: http://localhost/job-consultancy/recruiter/login.php
```

### Sample Job Seeker
```
Username: johndoe
Password: Seeker123!
URL: http://localhost/job-consultancy/jobseeker/login.php
```

---

## 🧪 Quick Test Checklist

- [ ] Homepage loads (http://localhost/job-consultancy)
- [ ] Can see featured jobs
- [ ] Can click "Browse Jobs"
- [ ] Can see job list with filters
- [ ] Can admin login with admin/Admin123!
- [ ] Can see admin dashboard
- [ ] Can recruiter login with techcorp/Company123!
- [ ] Can see recruiter dashboard

**All checks passed?** You're ready! 🎉

---

## 📱 Next Steps

### For Job Seeker
1. Go to: http://localhost/job-consultancy/jobseeker/register.php
2. Create account
3. Login and view jobs
4. Apply for jobs

### For Recruiter
1. Go to: http://localhost/job-consultancy/recruiter/register.php
2. Create account (pending admin approval)
3. Admin approves at admin panel
4. Login and post jobs

### For Admin
1. Login at admin panel
2. View statistics
3. Manage jobs, users, categories

---

## 🆘 Common Issues

### "Cannot connect to database"
```
Check: MySQL is running in XAMPP/WAMP
Check: Database "job_consultancy" exists
Check: config.php has correct credentials
```

### "404 Not Found"
```
Check: Project folder is in correct location
Check: Apache is running
Check: URL is correct
```

### "White page / No errors shown"
```
In config.php, find this line:
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

Uncomment by removing //:
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

## 📁 Project Structure

```
job-consultancy/
├── admin/              # Admin pages
├── recruiter/          # Recruiter pages
├── jobseeker/          # Job seeker pages
├── includes/           # Navigation, footer, database
├── assets/
│   ├── css/           # Styling
│   └── js/            # JavaScript
├── uploads/           # User files (resumes, logos)
├── config.php         # Configuration & functions
├── database.sql       # Database schema
├── index.php          # Homepage
├── jobs.php           # Job listing
└── job_detail.php     # Job details
```

---

## ⚙️ Configuration (Optional)

Edit `config.php` to customize:

```php
// Change app name
define('APP_NAME', 'Your Company Name');

// Change app URL (if not localhost)
define('APP_URL', 'http://localhost/job-consultancy');

// Change items per page
define('ITEMS_PER_PAGE', 10);

// Change session timeout (in seconds)
define('SESSION_TIMEOUT', 1800); // 30 minutes
```

---

## 🎓 Learning Path

1. **Explore as Guest**
   - Visit homepage
   - Browse jobs
   - View job details

2. **Test as Job Seeker**
   - Create account
   - Browse jobs
   - Apply for jobs
   - Edit profile

3. **Test as Recruiter**
   - Create account
   - Wait for admin approval (you need to approve in admin panel)
   - Post jobs
   - View applications

4. **Admin Panel**
   - Login
   - View statistics
   - Approve recruiters
   - Manage categories

---

## 📚 Full Documentation

For detailed information, read:

- **README.md** - Features & overview
- **INSTALLATION.md** - Complete installation guide
- **DEVELOPMENT.md** - For developers extending the system
- **API_INTEGRATION.md** - Custom integrations
- **CHANGELOG.md** - What's included

---

## 💡 Pro Tips

### Tip 1: Use All Sample Accounts First
Try all 3 accounts to understand the system before creating custom accounts.

### Tip 2: Post Test Jobs
As recruiter, post some jobs to see how job seeker interface works.

### Tip 3: Copy Base URL
Save `http://localhost/job-consultancy/` and use it for all links.

### Tip 4: Clear Browser Cache
If CSS/JS doesn't update, clear browser cache (Ctrl+Shift+Delete).

### Tip 5: Check phpMyAdmin
View database at http://localhost/phpmyadmin to understand data structure.

---

## 🔐 Security Notes

**For Local Development Only:**
- Default credentials are not secure
- SQL queries are logged (for debugging)
- Errors display on screen (for debugging)
- File permissions are open

**Before Production:**
- Change all passwords
- Disable error display
- Enable HTTPS
- Set proper file permissions
- Review INSTALLATION.md security section

---

## 🤔 Frequently Asked Questions

**Q: Where are uploaded files stored?**  
A: In the `uploads/` folder

**Q: How do I reset the database?**  
A: Delete database and re-import `database.sql`

**Q: How do I change the theme colors?**  
A: Edit `assets/css/style.css` and change CSS variables at the top

**Q: How do I allow another person to access locally?**  
A: Use your computer's IP address (run `ipconfig` in command prompt)  
   Example: `http://192.168.1.100/job-consultancy`

**Q: Can I run this on a live server?**  
A: Yes! See INSTALLATION.md production section

---

## 🆘 Getting Help

1. Check README.md for features
2. Check INSTALLATION.md for setup issues
3. Check DEVELOPMENT.md for code questions
4. Review error logs (phpMyAdmin → database)
5. Check browser console (F12 → Console)

---

## 🎉 You're All Set!

Your ProHire application is ready to explore.

**Next Step:** Login to admin panel and explore the system!

```
http://localhost/job-consultancy/admin/login.php
Username: admin
Password: Admin123!
```

---

## Additional Resources

| Resource | Link |
|----------|------|
| Home | http://localhost/job-consultancy |
| Browse Jobs | http://localhost/job-consultancy/jobs.php |
| Admin Login | http://localhost/job-consultancy/admin/login.php |
| Recruiter Register | http://localhost/job-consultancy/recruiter/register.php |
| Seeker Register | http://localhost/job-consultancy/jobseeker/register.php |
| Database Admin | http://localhost/phpmyadmin |

---

**Version:** 1.0.0  
**Last Updated:** February 17, 2026  

Happy coding! 🚀

---

## Still Need Help?

### Windows XAMPP Setup
1. Download: https://www.apachefriends.org/
2. Install to C:\xampp\
3. All files go in: C:\xampp\htdocs\job-consultancy\

### Windows WAMP Setup
1. Download: http://www.wampserver.com/
2. Install normally
3. All files go in: C:\wamp\www\job-consultancy\

### Mac/Linux Setup
1. Use XAMPP for Mac/Linux
2. Or native LAMP stack
3. Files go in: /var/www/html/job-consultancy/

---

**Stuck?** Re-read Step 1-4 carefully. 99% of issues are:
- MySQL not running
- Files in wrong folder
- Wrong URL in browser
- Browser cache not cleared

**Still stuck?** Check INSTALLATION.md Troubleshooting section!

