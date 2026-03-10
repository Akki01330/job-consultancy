<?php
require_once '../config.php';
$pageTitle = 'Recruiter Registration';

// If already logged in, redirect
if (isLoggedIn() && hasRole('recruiter')) {
    redirect(APP_URL . '/recruiter');
}

$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $formData['username'] = sanitize($_POST['username'] ?? '');
    $formData['email'] = sanitize($_POST['email'] ?? '');
    $formData['company_name'] = sanitize($_POST['company_name'] ?? '');
    $formData['company_email'] = sanitize($_POST['company_email'] ?? '');
    $formData['phone'] = sanitize($_POST['phone'] ?? '');
    $formData['website'] = sanitize($_POST['website'] ?? '');
    $formData['location'] = sanitize($_POST['location'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validation
    if (empty($formData['username']) || strlen($formData['username']) < 3) {
        $errors[] = 'Username must be at least 3 characters long';
    }
    
    if (empty($formData['email']) || !isValidEmail($formData['email'])) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($formData['company_name'])) {
        $errors[] = 'Company name is required';
    }
    
    if (empty($formData['company_email']) || !isValidEmail($formData['company_email'])) {
        $errors[] = 'Valid company email is required';
    }
    
    if (empty($password) || !isValidPassword($password)) {
        $errors[] = 'Password must be at least ' . SECURE_PASSWORD_MIN_LENGTH . ' characters long';
    }
    
    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM recruiters WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $formData['username'], $formData['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = 'Username or email already exists';
        } else {
            // Hash password and insert recruiter
            $passwordHash = hashPassword($password);
            
            $stmt = $conn->prepare("INSERT INTO recruiters (username, email, password, company_name, company_email, phone, website, location, is_verified, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, FALSE, TRUE, NOW())");
            $stmt->bind_param("ssssssss", $formData['username'], $formData['email'], $passwordHash, $formData['company_name'], $formData['company_email'], $formData['phone'], $formData['website'], $formData['location']);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Registration successful! Your account is pending admin verification. You will receive an email once approved.';
                $_SESSION['message_type'] = 'info';
                redirect(APP_URL . '/recruiter/login.php');
            } else {
                $errors[] = 'Registration failed: ' . $conn->error;
            }
        }
        
        $stmt->close();
    }
}

include("../includes/header.php");
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-building"></i> Recruiter Registration</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Username *</label>
                                    <input type="text" class="form-control" name="username" value="<?php echo $formData['username'] ?? ''; ?>" required>
                                    <small class="form-text">Minimum 3 characters</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo $formData['email'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Company Name *</label>
                            <input type="text" class="form-control" name="company_name" value="<?php echo $formData['company_name'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Company Email *</label>
                                    <input type="email" class="form-control" name="company_email" value="<?php echo $formData['company_email'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" name="phone" value="<?php echo $formData['phone'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Website</label>
                                    <input type="url" class="form-control" name="website" value="<?php echo $formData['website'] ?? ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Location</label>
                                    <input type="text" class="form-control" name="location" value="<?php echo $formData['location'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Password *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="passwordStrength"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control" name="password_confirm" required>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Register Company</button>
                    </form>
                    
                    <hr>
                    
                    <p class="text-center mb-2">
                        Already have an account? 
                        <a href="<?php echo APP_URL; ?>/recruiter/login.php">Login here</a>
                    </p>
                    <p class="text-center">
                        <a href="<?php echo APP_URL; ?>">Back to Home</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initPasswordStrengthIndicator('password', 'passwordStrength');
});
</script>

<?php include("../includes/footer.php"); ?>
