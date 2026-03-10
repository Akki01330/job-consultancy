# ProHire Consultancy - Development Guide

## 📚 Developer Documentation

This guide is for developers who want to understand the codebase, add features, or maintain the system.

---

## 🎯 Architecture Overview

### Design Pattern: MVC-Adjacent
The application uses a lightweight MVC-like pattern without a framework:
- **Views**: PHP templates mixing HTML and PHP (index.php, jobs.php, etc.)
- **Models**: Database queries using prepared statements
- **Controllers**: Logic mixed in templates (could be refactored to separate files)

### Folder Structure
```
job-consultancy/
├── config.php              # Core configuration & helper functions
├── includes/               # Shared templates
│   ├── header.php         # Navigation bar
│   └── footer.php         # Footer & scripts
├── assets/                # Static files
│   ├── css/style.css      # Styling
│   └── js/script.js       # Client-side utilities
├── admin/                 # Admin role pages
├── recruiter/             # Recruiter role pages
├── jobseeker/             # Job seeker role pages
├── uploads/               # User-uploaded files
└── database.sql           # Database schema
```

### Request Flow
```
User Request
    ↓
Route to .php file (direct access)
    ↓
includes/header.php (required)
    ↓
config.php (session, database, functions)
    ↓
Business logic & database queries
    ↓
Display template (HTML)
    ↓
includes/footer.php (required)
    ↓
Serve to browser
```

---

## 🔑 Key Functions (In config.php)

### Security Functions

#### `sanitize($data)`
Prevents XSS by escaping HTML special characters.
```php
$username = sanitize($_POST['username']); // Safe to display
```

#### `hashPassword($password)`
Creates bcrypt hash of password (cost 10).
```php
$hash = hashPassword($_POST['password']);
// Store $hash in database
```

#### `verifyPassword($password, $hash)`
Verifies password against bcrypt hash.
```php
if (verifyPassword($_POST['password'], $row['password'])) {
    // Password correct
}
```

#### `isValidPassword($password)`
Checks password meets requirements (8+ chars).
```php
if (!isValidPassword($_POST['password'])) {
    $error = "Password must be at least 8 characters";
}
```

#### `isValidEmail($email)`
Validates email format using regex.
```php
if (!isValidEmail($_POST['email'])) {
    $error = "Invalid email format";
}
```

### Access Control Functions

#### `isLoggedIn()`
Checks if user is authenticated.
```php
if (!isLoggedIn()) {
    redirect(APP_URL . '/login.php');
}
```

#### `hasRole($role)`
Checks if logged-in user has specific role.
```php
if (!hasRole('recruiter')) {
    redirect(APP_URL); // Only recruiters allowed
}
```

#### `getCurrentUserId()`
Returns ID of logged-in user.
```php
$user_id = getCurrentUserId(); // Returns $_SESSION['user_id']
```

### Helper Functions

#### `redirect($url)`
Performs header redirect and exits.
```php
redirect(APP_URL . '/admin'); // Exits script after redirect
```

#### `getPaginationData($total, $itemsPerPage)`
Returns pagination info for templates.
```php
$pagination = getPaginationData(100, 10);
// Returns: [
//   'total' => 100,
//   'total_pages' => 10,
//   'current_page' => 1,
//   'offset' => 0
// ]
```

#### `sanitizeFileName($filename)`
Creates safe filename for file uploads.
```php
$safe_name = sanitizeFileName($_FILES['resume']['name']);
// Converts: "My Resume.pdf" to "my_resume_12345.pdf"
```

#### `isValidFileUpload($file, $allowed_types, $max_size)`
Validates uploaded file.
```php
if (!isValidFileUpload($_FILES['resume'], ['pdf', 'doc', 'docx'], 5242880)) {
    $error = "Invalid file";
}
```

#### `sendJsonResponse($success, $message, $data = [])`
Returns JSON response for AJAX.
```php
sendJsonResponse(true, "Job posted successfully", ['job_id' => 123]);
// Response: {"success":true,"message":"...","data":{...}}
```

---

## 💾 Database Access Pattern

### Standard Query Pattern
All queries follow this pattern:

```php
// 1. Prepare statement (SQL with ? placeholders)
$stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");

// 2. Bind parameters (type, value)
$stmt->bind_param("s", $email); // "s" = string, "i" = int, "d" = double, "b" = blob

// 3. Execute
$stmt->execute();

// 4. Get results
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// 5. Close
$stmt->close();
```

### INSERT Example
```php
$stmt = $conn->prepare("
    INSERT INTO users (name, email, password) 
    VALUES (?, ?, ?)
");
$stmt->bind_param("sss", $name, $email, $hash);
$stmt->execute();
$new_id = $conn->insert_id; // Get inserted ID
$stmt->close();
```

### UPDATE Example
```php
$stmt = $conn->prepare("
    UPDATE users 
    SET name = ?, updated_at = NOW() 
    WHERE id = ?
");
$stmt->bind_param("si", $name, $id);
$stmt->execute();
$stmt->close();
```

### Common Type Strings
```php
"s" // String
"i" // Integer
"d" // Double/Float
"b" // Blob (binary)

// Multiple parameters:
"ssi"     // string, string, integer
"idds"    // int, double, double, string
```

---

## 🔐 Security Best Practices

### ✅ DO:
```php
// DO: Use prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

// DO: Hash passwords with bcrypt
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

// DO: Sanitize output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// DO: Check user role
if (!hasRole('admin')) { redirect(APP_URL); }

// DO: Use constants for secrets
define('DB_PASS', 'your_password');
$password = DB_PASS;
```

### ❌ DON'T:
```php
// DON'T: Concatenate SQL
$sql = "SELECT * FROM users WHERE id = " . $_GET['id']; // SQL INJECTION!

// DON'T: Use MD5 for passwords
$hash = md5($password); // INSECURE!

// DON'T: Echo user input directly
echo $_POST['name']; // XSS VULNERABILITY!

// DON'T: Store secrets in files without protection
$pass = "secure123"; // In config.php visible to everyone

// DON'T: Use plaintext cookies
setcookie('user_id', '123'); // Can be spoofed
```

---

## 🛠️ Adding New Features

### Example: Add "Favorites" Feature to Job Seeker

#### Step 1: Update Database
```php
// First, run this SQL manually:
ALTER TABLE job_seekers ADD COLUMN favorites 
   TEXT DEFAULT NULL COMMENT 'JSON array of job IDs';

-- Then create a favorites table:
CREATE TABLE job_favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    seeker_id INT NOT NULL,
    job_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(seeker_id, job_id),
    FOREIGN KEY (seeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
);
```

#### Step 2: Create Toggle Endpoint
Create `jobseeker/toggle_favorite.php`:
```php
<?php
require '../config.php';

// Check logged in
if (!isLoggedIn() || !hasRole('jobseeker')) {
    sendJsonResponse(false, "Unauthorized");
    exit;
}

// Get request data
$data = json_decode(file_get_contents('php://input'), true);
$job_id = intval($data['job_id'] ?? 0);

if (!$job_id) {
    sendJsonResponse(false, "Invalid job ID");
}

$seeker_id = getCurrentUserId();

// Check if already favorited
$stmt = $conn->prepare("SELECT id FROM job_favorites WHERE seeker_id = ? AND job_id = ?");
$stmt->bind_param("ii", $seeker_id, $job_id);
$stmt->execute();
$exists = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($exists) {
    // Remove favorite
    $stmt = $conn->prepare("DELETE FROM job_favorites WHERE seeker_id = ? AND job_id = ?");
    $stmt->bind_param("ii", $seeker_id, $job_id);
    $stmt->execute();
    $stmt->close();
    sendJsonResponse(true, "Removed from favorites", ['favorited' => false]);
} else {
    // Add favorite
    $stmt = $conn->prepare("INSERT INTO job_favorites (seeker_id, job_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $seeker_id, $job_id);
    $stmt->execute();
    $stmt->close();
    sendJsonResponse(true, "Added to favorites", ['favorited' => true]);
}
```

#### Step 3: Update Job Detail Template
Add button to `job_detail.php`:
```html
<button id="favoriteBtn" class="btn btn-outline-primary">
    <i class="fas fa-heart"></i> Add to Favorites
</button>

<script>
    document.getElementById('favoriteBtn').addEventListener('click', function() {
        const jobId = <?php echo $job['id']; ?>;
        
        fetch('<?php echo APP_URL; ?>/jobseeker/toggle_favorite.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({job_id: jobId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.classList.toggle('btn-outline-primary');
                this.classList.toggle('btn-danger');
            }
        });
    });
</script>
```

#### Step 4: Create Favorites List Page
Create `jobseeker/favorites.php`:
```php
<?php
require '../config.php';
require '../includes/header.php';

if (!isLoggedIn() || !hasRole('jobseeker')) {
    redirect(APP_URL);
}

$seeker_id = getCurrentUserId();

// Get paginated favorites
$page = intval($_GET['page'] ?? 1);
$pagination = getPaginationData(
    $conn->query("SELECT COUNT(*) as count FROM job_favorites 
                   WHERE seeker_id = $seeker_id")->fetch_assoc()['count'],
    ITEMS_PER_PAGE
);

$stmt = $conn->prepare("
    SELECT j.*, r.company_name 
    FROM jobs j
    JOIN recruiters r ON j.recruiter_id = r.id
    JOIN job_favorites f ON j.id = f.job_id
    WHERE f.seeker_id = ?
    ORDER BY f.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $seeker_id, $pagination['limit'], $pagination['offset']);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container py-5">
    <h1>My Favorite Jobs</h1>
    
    <div class="row">
        <?php while ($job = $result->fetch_assoc()): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5><?php echo sanitize($job['title']); ?></h5>
                        <p><?php echo sanitize($job['company_name']); ?></p>
                        <p class="text-muted"><?php echo sanitize($job['location']); ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
```

---

## 🔄 Session Management

### Session Variables
```php
$_SESSION['user_id']    // Database ID of logged-in user
$_SESSION['user_type']  // Role: 'admin', 'recruiter', or 'jobseeker'
$_SESSION['user_name']  // Display name of user

// Plus any custom variables you add
$_SESSION['message']        // Flash messages for display
$_SESSION['message_type']   // 'success', 'danger', 'warning', etc
```

### Setting Flash Message
```php
// In login.php after successful login:
$_SESSION['message'] = "Welcome back!";
$_SESSION['message_type'] = 'success';
redirect(APP_URL);

// Then in header.php it displays automatically:
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
        <?php echo $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>
```

### Session Timeout
Session expires after 30 minutes of inactivity (set in config.php):
```php
define('SESSION_TIMEOUT', 1800); // seconds

// Check in config.php during session_start()
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_destroy();
        header('Location: ' . APP_URL);
        exit;
    }
}
$_SESSION['last_activity'] = time();
```

---

## 📋 Code Examples

### Example 1: Add a New Admin Page

Create `admin/manage_jobs_detailed.php`:

```php
<?php
require '../config.php';
require '../includes/header.php';

// Check authorization
if (!isLoggedIn() || !hasRole('admin')) {
    redirect(APP_URL);
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = sanitize($_POST['action'] ?? '');
    
    if ($action == 'close') {
        $job_id = intval($_POST['job_id'] ?? 0);
        $stmt = $conn->prepare("UPDATE jobs SET status = 'closed' WHERE id = ?");
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['message'] = "Job closed successfully";
        $_SESSION['message_type'] = 'success';
    }
}

// Get pagination
$page = intval($_GET['page'] ?? 1);
$total = $conn->query("SELECT COUNT(*) as count FROM jobs")->fetch_assoc()['count'];
$pagination = getPaginationData($total, ITEMS_PER_PAGE);

// Get jobs
$stmt = $conn->prepare("
    SELECT j.*, r.company_name, COUNT(a.id) as applications
    FROM jobs j
    LEFT JOIN recruiters r ON j.recruiter_id = r.id
    LEFT JOIN applications a ON j.id = a.job_id
    GROUP BY j.id
    ORDER BY j.posted_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ii", $pagination['items_per_page'], $pagination['offset']);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container py-5">
    <h1>All Jobs</h1>
    
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Company</th>
                <th>Applications</th>
                <th>Status</th>
                <th>Posted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($job = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo sanitize($job['title']); ?></td>
                <td><?php echo sanitize($job['company_name']); ?></td>
                <td><?php echo $job['applications']; ?></td>
                <td><span class="badge bg-<?php echo $job['status'] == 'active' ? 'success' : 'danger'; ?>">
                    <?php echo ucfirst($job['status']); ?>
                </span></td>
                <td><?php echo date('M d, Y', strtotime($job['posted_at'])); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="close">
                        <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                        <button class="btn btn-sm btn-danger">Close</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<?php require '../includes/footer.php'; ?>
```

### Example 2: Create a Modal Confirmation

Add to `assets/js/script.js`:
```javascript
function showConfirmModal(title, message, onConfirm) {
    const modalHtml = `
        <div class="modal fade" id="confirmModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                    </div>
                    <div class="modal-body">${message}</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    
    document.getElementById('confirmBtn').onclick = function() {
        onConfirm();
        modal.hide();
    };
    
    modal.show();
}

// Usage:
function deleteJob(jobId) {
    showConfirmModal(
        'Delete Job',
        'Are you sure you want to delete this job?',
        function() {
            // Submit form or AJAX request
            document.getElementById('deleteForm').submit();
        }
    );
}
```

---

## 🧪 Testing Checklist

### Authentication Testing
- [ ] Admin login works
- [ ] Recruiter login works
- [ ] Job seeker login works
- [ ] Password hashing verified (bcrypt)
- [ ] Wrong password rejected
- [ ] Invalid role cannot access admin pages
- [ ] Session expires after 30 minutes
- [ ] Logout clears session

### Database Testing
- [ ] All 7 tables created
- [ ] Foreign keys working (cascading deletes)
- [ ] Unique constraints enforced (no duplicate emails)
- [ ] Sample data populated
- [ ] Prepared statements prevent SQL injection
- [ ] Timestamps auto-update

### UI/UX Testing
- [ ] Responsive design on mobile/tablet
- [ ] All forms validate input
- [ ] Error messages display clearly
- [ ] Success messages appear
- [ ] Navigation works from all pages
- [ ] Images/icons load
- [ ] Links work (no 404s)
- [ ] Forms submit and redirect properly

---

## 📦 Code Organization Tips

### File Naming Conventions
```
admin/*.php           // Admin-only pages
recruiter/*.php       // Recruiter-only pages
jobseeker/*.php       // Job seeker-only pages
includes/*.php        // Shared templates
assets/css/*.css      // Stylesheets
assets/js/*.js        // JavaScript
config.php            // Core configuration
database.sql          // Database schema
logout.php            // Cross-role functionality
index.php             // Homepage
jobs.php              // Public job listing
job_detail.php        // Public job details
```

### Function Organization
Keep `config.php` organized in sections:
```php
// 1. Configuration constants
define('APP_NAME', 'ProHire');
// ... other constants

// 2. Database connection
$conn = new mysqli(...);

// 3. Session management
session_start();

// 4. Security functions
function sanitize() {}
function hashPassword() {}

// 5. Access control
function isLoggedIn() {}
function hasRole() {}

// 6. Database helpers
function getPaginationData() {}

// 7. Utility functions
function redirect() {}
```

---

## 🚀 Performance Tips

### 1. Database Optimization
```php
// ❌ Bad: Multiple queries in loop
foreach ($ids as $id) {
    $result = $conn->query("SELECT * FROM jobs WHERE id = $id");
}

// ✅ Good: Single query with WHERE IN
$ids_str = implode(',', $ids);
$result = $conn->query("SELECT * FROM jobs WHERE id IN ($ids_str)");
```

### 2. Caching Results
```php
// Cache job counts
if (!isset($_SESSION['job_counts'])) {
    $count = $conn->query("SELECT COUNT(*) FROM jobs")->fetch_assoc()['count'];
    $_SESSION['job_counts'] = $count;
}
```

### 3. Pagination (never load all records)
```php
// ❌ Bad: Load all jobs into memory
$result = $conn->query("SELECT * FROM jobs");
while ($row = $result->fetch_assoc()) { }

// ✅ Good: Load only current page
$stmt = $conn->prepare("SELECT * FROM jobs LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
```

---

## 🐛 Debugging Tips

### Enable Error Display (development only)
Add to config.php:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errors.log');
```

### Debug Database Queries
```php
// Print prepared statement
echo $stmt->error; // Shows SQL error

// Check affected rows
if ($stmt->execute()) {
    echo "Rows affected: " . $stmt->affected_rows;
}

// See actual query before execution (mysqli limitation)
// Use PDO if you need true prepared statement debugging
```

### Debug Sessions
```php
// Print all session data
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
```

### Debug AJAX Requests
In browser developer console:
```javascript
// Add before fetch
fetch(url, options)
    .then(r => r.json())
    .then(data => {
        console.log('Response:', data); // See what server sent
    });
```

---

**Happy Coding!** 🚀

For questions, refer to README.md or INSTALLATION.md

---

**Last Updated:** February 17, 2026  
**ProHire Consultancy v1.0.0**
