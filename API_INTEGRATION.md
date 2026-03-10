# ProHire Consultancy - API & Integration Guide

## 📡 API Endpoints & Integration Points

This guide covers how to interact with the ProHire system programmatically or integrate with third-party services.

---

## 🔄 AJAX Endpoints (Internal)

These endpoints are designed for JavaScript fetch requests from the frontend.

### Response Format (Standard)

All AJAX endpoints return JSON in this format:

```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {
    // Optional data payload
  }
}
```

To send this response:
```php
sendJsonResponse(true, "Message", ['key' => 'value']);
```

---

### Endpoint: Toggle Favorite Job

**File:** `Create jobseeker/toggle_favorite.php`

**Method:** POST  
**Auth Required:** Job Seeker  
**Content-Type:** application/json

**Request:**
```json
{
  "job_id": 123
}
```

**Response:**
```json
{
  "success": true,
  "message": "Added to favorites",
  "data": {
    "favorited": true
  }
}
```

**Implementation:**
```php
<?php
require '../config.php';

if (!isLoggedIn() || !hasRole('jobseeker')) {
    sendJsonResponse(false, "Unauthorized");
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$job_id = intval($data['job_id'] ?? 0);
$seeker_id = getCurrentUserId();

// Check if favorited
$stmt = $conn->prepare("SELECT id FROM job_favorites WHERE seeker_id = ? AND job_id = ?");
$stmt->bind_param("ii", $seeker_id, $job_id);
$stmt->execute();
$exists = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($exists) {
    $stmt = $conn->prepare("DELETE FROM job_favorites WHERE seeker_id = ? AND job_id = ?");
    $stmt->bind_param("ii", $seeker_id, $job_id);
    $stmt->execute();
    $stmt->close();
    sendJsonResponse(true, "Removed from favorites", ['favorited' => false]);
} else {
    $stmt = $conn->prepare("INSERT INTO job_favorites (seeker_id, job_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $seeker_id, $job_id);
    $stmt->execute();
    $stmt->close();
    sendJsonResponse(true, "Added to favorites", ['favorited' => true]);
}
```

**JavaScript Usage:**
```javascript
async function toggleFavorite(jobId) {
    const response = await fetch('<?php echo APP_URL; ?>/jobseeker/toggle_favorite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({job_id: jobId})
    });
    const data = await response.json();
    if (data.success) {
        console.log('Favorite toggled:', data.data.favorited);
    }
}
```

---

### Endpoint: Update Application Status

**File:** `Create recruiter/update_application_status.php`

**Method:** POST  
**Auth Required:** Recruiter  
**Content-Type:** application/json

**Request:**
```json
{
  "application_id": 456,
  "status": "shortlisted"
}
```

**Valid Statuses:** "applied", "reviewed", "shortlisted", "accepted", "rejected"

**Response:**
```json
{
  "success": true,
  "message": "Application status updated",
  "data": {
    "application_id": 456,
    "status": "shortlisted"
  }
}
```

**Implementation:**
```php
<?php
require '../config.php';

if (!isLoggedIn() || !hasRole('recruiter')) {
    sendJsonResponse(false, "Unauthorized");
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$app_id = intval($data['application_id'] ?? 0);
$status = sanitize($data['status'] ?? '');

$allowed_statuses = ['applied', 'reviewed', 'shortlisted', 'accepted', 'rejected'];
if (!in_array($status, $allowed_statuses)) {
    sendJsonResponse(false, "Invalid status");
    exit;
}

$recruiter_id = getCurrentUserId();

// Verify recruiter owns this application
$stmt = $conn->prepare("
    SELECT a.id FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE a.id = ? AND j.recruiter_id = ?
");
$stmt->bind_param("ii", $app_id, $recruiter_id);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    sendJsonResponse(false, "Application not found");
    exit;
}
$stmt->close();

// Update status
$stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $app_id);
$stmt->execute();
$stmt->close();

sendJsonResponse(true, "Application status updated", [
    'application_id' => $app_id,
    'status' => $status
]);
```

---

### Endpoint: Search Jobs (AJAX)

**File:** `Create jobseeker/search_jobs.php`

**Method:** GET  
**Auth Required:** No  
**Content-Type:** application/json

**Query Parameters:**
```
?q=php&location=new+york&category=2&limit=10
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 25,
    "jobs": [
      {
        "id": 1,
        "title": "PHP Developer",
        "company_name": "Tech Corp",
        "location": "New York",
        "salary_min": 50000,
        "salary_max": 80000,
        "job_type": "full-time"
      }
    ]
  }
}
```

**Implementation:**
```php
<?php
require '../config.php';

$q = sanitize($_GET['q'] ?? '');
$location = sanitize($_GET['location'] ?? '');
$category = intval($_GET['category'] ?? 0);
$limit = intval($_GET['limit'] ?? 10);

$where = ["j.status = 'active'"];
$params = [];
$types = "";

if ($q) {
    $where[] = "j.title LIKE ?";
    $params[] = "%$q%";
    $types .= "s";
}

if ($location) {
    $where[] = "j.location LIKE ?";
    $params[] = "%$location%";
    $types .= "s";
}

if ($category > 0) {
    $where[] = "j.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

$where_clause = implode(" AND ", $where);

// Count total
$count_query = "SELECT COUNT(*) as total FROM jobs j WHERE $where_clause";
$count_stmt = $conn->prepare($count_query);
if ($types) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

// Get results
$params[] = $limit;
$types .= "i";

$query = "
    SELECT j.id, j.title, r.company_name, j.location, 
           j.salary_min, j.salary_max, j.job_type
    FROM jobs j
    JOIN recruiters r ON j.recruiter_id = r.id
    WHERE $where_clause
    LIMIT ?
";

$stmt = $conn->prepare($query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
$stmt->close();

sendJsonResponse(true, "Search completed", [
    'total' => $total,
    'jobs' => $jobs
]);
```

---

## 🔌 Third-Party Integrations

### Email Notifications (Template Structure)

**File:** `Create includes/email_templates.php`

```php
<?php
// Email template functions

function sendNewJobAlert($seeker_email, $job_title, $company_name) {
    $subject = "New Job Opportunity: $job_title";
    $message = "
        <h2>New Job Opportunity!</h2>
        <p>A new job has been posted that matches your profile:</p>
        <h3>$job_title</h3>
        <p>Company: $company_name</p>
        <p><a href='" . APP_URL . "/job_detail.php?id=123'>View Job</a></p>
    ";
    $to = $seeker_email;
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

function sendApplicationConfirmation($seeker_email, $job_title, $company_name) {
    $subject = "Application Received - $job_title at $company_name";
    $message = "
        <h2>Application Submitted</h2>
        <p>Your application for <strong>$job_title</strong> at $company_name has been received.</p>
        <p>The recruiter will review your profile and contact you soon.</p>
    ";
    // ... send email
}

function sendApplicationStatus($seeker_email, $job_title, $status) {
    $subject = "Application Status Update";
    $message = "
        <h2>Application Update</h2>
        <p>Your application for <strong>$job_title</strong> status: <strong>" . strtoupper($status) . "</strong></p>
    ";
    // ... send email
}

function sendVerificationEmail($recruiter_email, $company_name) {
    $subject = "Account Verification - $company_name";
    $message = "
        <h2>Account Verification Pending</h2>
        <p>Your recruiter account for <strong>$company_name</strong> is pending admin verification.</p>
        <p>You will receive an email once approved.</p>
    ";
    // ... send email
}
?>
```

**Usage in recruiter/register.php:**
```php
<?php
// After successful registration
require '../includes/email_templates.php';
sendVerificationEmail($_POST['email'], $_POST['company_name']);
```

---

### Payment Integration (Stripe Example)

**File:** `Create includes/payment.php`

```php
<?php
require '../config.php';

class PaymentProcessor {
    private $stripe_key;
    
    public function __construct() {
        $this->stripe_key = getenv('STRIPE_SECRET_KEY');
    }
    
    // Charge recruiter for job posting
    public function chargeForJobPosting($recruiter_id, $amount = 9.99) {
        // Get Stripe library (require via Composer)
        // require 'vendor/autoload.php';
        
        // Example charge creation (requires Stripe API)
        // $charge = \Stripe\Charge::create([
        //     'amount' => $amount * 100, // Convert to cents
        //     'currency' => 'usd',
        //     'source' => $token,
        //     'description' => 'Job Posting'
        // ]);
        
        return true; // Success
    }
    
    // Create subscription for premium features
    public function createSubscription($recruiter_id, $plan = 'premium') {
        // Implementation
    }
}
?>
```

---

### Resume Parser Integration

**File:** `Create includes/resume_parser.php`

```php
<?php

class ResumeParser {
    // Parse resume file for extracting skills, experience
    public static function parseResume($file_path) {
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);
        
        if ($ext == 'pdf') {
            return self::parsePDF($file_path);
        } elseif ($ext == 'docx') {
            return self::parseDOCX($file_path);
        }
        
        return null;
    }
    
    private static function parsePDF($file) {
        // Use TCPDF or similar library
        // $pdf = new TCPDF();
        // $text = $pdf->getTextContent();
        
        $skills = self::extractSkills($text);
        $experience = self::extractExperience($text);
        
        return [
            'skills' => $skills,
            'experience' => $experience,
            'raw_text' => $text
        ];
    }
    
    private static function parseDOCX($file) {
        // Use PhpWord library
        // $phpWord = \PhpOffice\PhpWord\IOFactory::load($file);
        
        return [
            'skills' => [],
            'experience' => []
        ];
    }
    
    private static function extractSkills($text) {
        $common_skills = [
            'PHP', 'JavaScript', 'Python', 'Java', 'SQL', 'MySQL',
            'React', 'Vue', 'Angular', 'Node.js', 'HTML', 'CSS'
        ];
        
        $found_skills = [];
        foreach ($common_skills as $skill) {
            if (stripos($text, $skill) !== false) {
                $found_skills[] = $skill;
            }
        }
        
        return $found_skills;
    }
    
    private static function extractExperience($text) {
        // Use regex to find years of experience
        if (preg_match('/(\d+)\s+years?/', $text, $matches)) {
            return intval($matches[1]);
        }
        return null;
    }
}
?>
```

---

### Notification Service Integration

**File:** `Create includes/notifications.php`

```php
<?php

interface NotificationChannelInterface {
    public function send($recipient, $subject, $message);
}

// SMS Notification (Twilio)
class SMSNotification implements NotificationChannelInterface {
    private $account_sid;
    private $auth_token;
    private $from_number;
    
    public function __construct() {
        $this->account_sid = getenv('TWILIO_ACCOUNT_SID');
        $this->auth_token = getenv('TWILIO_AUTH_TOKEN');
        $this->from_number = getenv('TWILIO_PHONE');
    }
    
    public function send($phone, $subject, $message) {
        // Use Twilio SDK
        // $client = new Client($this->account_sid, $this->auth_token);
        // $client->messages->create($phone, ['from' => $this->from_number, 'body' => $message]);
        
        return true;
    }
}

// Push Notification (Firebase)
class PushNotification implements NotificationChannelInterface {
    private $api_key;
    
    public function __construct() {
        $this->api_key = getenv('FCM_SERVER_KEY');
    }
    
    public function send($device_token, $subject, $message) {
        // Use Firebase Cloud Messaging API
        
        return true;
    }
}

// Email Notification
class EmailNotification implements NotificationChannelInterface {
    public function send($email, $subject, $message) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        return mail($email, $subject, $message, $headers);
    }
}

class NotificationService {
    private $channels = [];
    
    public function addChannel(NotificationChannelInterface $channel) {
        $this->channels[] = $channel;
    }
    
    public function notifyAll($recipient, $subject, $message) {
        foreach ($this->channels as $channel) {
            $channel->send($recipient, $subject, $message);
        }
    }
}

// Usage:
// $notifier = new NotificationService();
// $notifier->addChannel(new EmailNotification());
// $notifier->addChannel(new SMSNotification());
// $notifier->notifyAll('user@example.com', 'New Job', 'You have a new job match');
?>
```

---

## 📊 Data Export/Import

### Export Job Listings to CSV

**File:** `Create admin/export_jobs.php`

```php
<?php
require '../config.php';

if (!isLoggedIn() || !hasRole('admin')) {
    redirect(APP_URL);
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="jobs_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// Header row
fputcsv($output, ['ID', 'Title', 'Company', 'Location', 'Salary Min', 'Salary Max', 'Status', 'Posted']);

// Data rows
$result = $conn->query("
    SELECT j.id, j.title, r.company_name, j.location, 
           j.salary_min, j.salary_max, j.status, j.posted_at
    FROM jobs j
    JOIN recruiters r ON j.recruiter_id = r.id
    ORDER BY j.posted_at DESC
");

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
```

### Import Jobs from CSV

**File:** `Create admin/import_jobs.php`

```php
<?php
require '../config.php';

if (!isLoggedIn() || !hasRole('admin')) {
    redirect(APP_URL);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    
    if (($handle = fopen($file, 'r')) !== false) {
        $header = fgetcsv($handle); // Skip header
        $imported = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            list($title, $recruiter_id, $category_id, $salary_min, $salary_max) = $row;
            
            $stmt = $conn->prepare("
                INSERT INTO jobs (recruiter_id, category_id, title, salary_min, salary_max, status)
                VALUES (?, ?, ?, ?, ?, 'active')
            ");
            $stmt->bind_param("iisii", $recruiter_id, $category_id, $title, $salary_min, $salary_max);
            
            if ($stmt->execute()) {
                $imported++;
            }
            $stmt->close();
        }
        
        fclose($handle);
        $_SESSION['message'] = "Imported $imported jobs";
        $_SESSION['message_type'] = 'success';
    }
}

// Show import form
require '../includes/header.php';
?>

<div class="container py-5">
    <h1>Import Jobs</h1>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>CSV File:</label>
            <input type="file" name="csv_file" accept=".csv" required>
            <small>Format: title, recruiter_id, category_id, salary_min, salary_max</small>
        </div>
        <button type="submit" class="btn btn-primary">Import</button>
    </form>
</div>

<?php require '../includes/footer.php'; ?>
```

---

## 🔌 Webhook Integration

### Recruiter Webhook Events

**File:** `Create recruiter/webhooks.php`

Register webhooks for your application events:

```php
<?php
require '../config.php';

class WebhookDispatcher {
    private $webhooks = [];
    
    public function register($event, $url) {
        // Save to database
        $stmt = $conn->prepare("
            INSERT INTO webhooks (event, url, recruiter_id, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("ssi", $event, $url, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    }
    
    public function trigger($event, $data) {
        // Get all webhooks for this event
        $stmt = $conn->prepare("
            SELECT url FROM webhooks 
            WHERE event = ? AND recruiter_id = ? AND is_active = TRUE
        ");
        $stmt->bind_param("si", $event, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        while ($row = $result->fetch_assoc()) {
            // Send webhook request
            $this->sendWebhook($row['url'], $event, $data);
        }
    }
    
    private function sendWebhook($url, $event, $data) {
        $payload = json_encode([
            'event' => $event,
            'timestamp' => date('c'),
            'data' => $data
        ]);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);
        curl_exec($ch);
        curl_close($ch);
    }
}

// Example: Trigger webhook when application received
// $dispatcher = new WebhookDispatcher();
// $dispatcher->trigger('application.received', ['job_id' => 123, 'seeker_id' => 456]);
?>
```

---

## 📚 Example Integrations

### LinkedIn Job Integration

```php
<?php
// Post job to LinkedIn (requires LinkedIn API credentials)
class LinkedInJobPoster {
    private $access_token;
    
    public function postJob($job_data) {
        $job = [
            'title' => $job_data['title'],
            'description' => $job_data['description'],
            'location' => $job_data['location'],
            'seniority_level' => 'MID_LEVEL',
            'employment_type' => 'FULL_TIME',
            'company_id' => getenv('LINKEDIN_COMPANY_ID')
        ];
        
        $ch = curl_init('https://api.linkedin.com/rest/jobs');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->access_token,
                'Content-Type: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($job)
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}
?>
```

### Indeed API Integration

```php
<?php
// Sync with Indeed job board
class IndeedJobSync {
    public function syncJob($job_id) {
        $job = getJobData($job_id);
        
        // Post to Indeed API
        // Implementation depends on Indeed API documentation
        
        // Save sync status
        $stmt = $conn->prepare("
            UPDATE jobs 
            SET synced_to_indeed = TRUE 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>
```

---

## 🔐 API Security Best Practices

### Rate Limiting

```php
<?php
function rateLimit($identifier, $limit = 100, $window = 3600) {
    $key = "rate_limit_" . md5($identifier);
    
    $current = getMemcacheValue($key) ?? 0;
    
    if ($current >= $limit) {
        sendJsonResponse(false, "Rate limit exceeded");
        exit;
    }
    
    setMemcacheValue($key, $current + 1, $window);
}

// Usage:
rateLimit($_SERVER['REMOTE_ADDR'], 100, 3600); // 100 requests per hour
?>
```

### API Key Authentication

```php
<?php
define('API_KEYS', [
    'recruiter_portal' => 'sk_live_...',
    'mobile_app' => 'sk_live_...'
]);

function validateAPIKey($key) {
    return in_array($key, API_KEYS);
}

// Usage:
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? '';
if (!validateAPIKey($api_key)) {
    sendJsonResponse(false, "Invalid API key");
    exit;
}
?>
```

### CORS Headers

```php
<?php
function enableCORS($allowed_origins = ['https://example.com']) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    if (in_array($origin, $allowed_origins)) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }
}

// Usage:
enableCORS(['https://app.example.com', 'https://mobile.example.com']);
?>
```

---

## 📝 Database Schema for Extensions

```sql
-- Webhooks table
CREATE TABLE webhooks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recruiter_id INT NOT NULL,
    event VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recruiter_id) REFERENCES recruiters(id) ON DELETE CASCADE
);

-- Job favorites table
CREATE TABLE job_favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    seeker_id INT NOT NULL,
    job_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(seeker_id, job_id),
    FOREIGN KEY (seeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
);

-- API tokens table
CREATE TABLE api_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recruiter_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recruiter_id) REFERENCES recruiters(id) ON DELETE CASCADE
);
```

---

## 🚀 Deployment Checklist for APIs

- [ ] API keys stored in environment variables
- [ ] Rate limiting implemented
- [ ] CORS configured correctly
- [ ] Error handling returns proper status codes
- [ ] API documentation generated
- [ ] Input validation on all endpoints
- [ ] Authentication checks on all protected endpoints
- [ ] Logging enabled for all API calls
- [ ] Database backups before API deployment
- [ ] Load testing completed
- [ ] Security audit performed

---

**For additional help with API integration, refer to:**
- DEVELOPMENT.md - Code organization and patterns
- INSTALLATION.md - Database setup
- README.md - Feature overview

---

**Last Updated:** February 17, 2026  
**ProHire Consultancy v1.0.0**
