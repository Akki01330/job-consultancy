<?php
require_once '../config.php';
$pageTitle = 'Job Seeker Registration';

// If already logged in, redirect
if (isLoggedIn() && hasRole('jobseeker')) {
    redirect(APP_URL . '/jobseeker');
}

$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $formData['username'] = sanitize($_POST['username'] ?? '');
    $formData['email'] = sanitize($_POST['email'] ?? '');
    $formData['first_name'] = sanitize($_POST['first_name'] ?? '');
    $formData['last_name'] = sanitize($_POST['last_name'] ?? '');
    $formData['phone'] = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $formData['skills'] = sanitize($_POST['skills'] ?? '');
    
    // Validation
    if (empty($formData['username']) || strlen($formData['username']) < 3) {
        $errors[] = 'Username must be at least 3 characters long';
    }
    
    if (empty($formData['email']) || !isValidEmail($formData['email'])) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($formData['first_name'])) {
        $errors[] = 'First name is required';
    }
    
    if (empty($password) || !isValidPassword($password)) {
        $errors[] = 'Password must be at least ' . SECURE_PASSWORD_MIN_LENGTH . ' characters long';
    }
    
    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM job_seekers WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $formData['username'], $formData['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = 'Username or email already exists';
        } else {
            // Hash password and insert user
            $passwordHash = hashPassword($password);
            
            $stmt = $conn->prepare("INSERT INTO job_seekers (username, email, password, first_name, last_name, phone, skills, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssssss", $formData['username'], $formData['email'], $passwordHash, $formData['first_name'], $formData['last_name'], $formData['phone'], $formData['skills']);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Registration successful! Please log in with your credentials.';
                $_SESSION['message_type'] = 'success';
                redirect(APP_URL . '/jobseeker/login.php');
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
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-user-plus"></i> Job Seeker Registration</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" name="first_name" value="<?php echo $formData['first_name'] ?? ''; ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="<?php echo $formData['last_name'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Username *</label>
                            <input type="text" class="form-control" name="username" value="<?php echo $formData['username'] ?? ''; ?>" required>
                            <small class="form-text">Minimum 3 characters, alphanumeric and underscore only</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" value="<?php echo $formData['email'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" value="<?php echo $formData['phone'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Skills</label>
                            <textarea class="form-control" name="skills" rows="3" placeholder="Enter your skills (comma-separated)"><?php echo $formData['skills'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="form-label">Password *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="passwordStrength"></div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" name="password_confirm" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>
                    </form>
                    
                    <hr>
                    
                    <p class="text-center mb-2">
                        Already have an account? 
                        <a href="<?php echo APP_URL; ?>/jobseeker/login.php">Login here</a>
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
