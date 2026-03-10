<?php
require_once 'config.php';
$pageTitle = 'Job Seeker Login';

// If already logged in, redirect
if (isLoggedIn() && hasRole('jobseeker')) {
    redirect(APP_URL . '/jobseeker');
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    if (empty($errors)) {
        // Query database with prepared statement
        $stmt = $conn->prepare("SELECT id, first_name, password, is_active FROM job_seekers WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if ($user['is_active']) {
                if (verifyPassword($password, $user['password'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = 'jobseeker';
                    $_SESSION['user_name'] = $user['first_name'];
                    
                    $_SESSION['message'] = 'Welcome back, ' . $user['first_name'] . '!';
                    $_SESSION['message_type'] = 'success';
                    
                    redirect(APP_URL . '/jobseeker');
                } else {
                    $errors[] = 'Invalid password';
                }
            } else {
                $errors[] = 'Your account has been disabled';
            }
        } else {
            $errors[] = 'Username not found';
        }
        
        $stmt->close();
    }
}

include("includes/header.php");
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-user-tie"></i> Job Seeker Login</h4>
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
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" value="<?php echo $username; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block w-100 mb-3">Login</button>
                    </form>
                    
                    <hr>
                    
                    <p class="text-center mb-2">
                        Don't have an account? 
                        <a href="<?php echo APP_URL; ?>/jobseeker/register.php">Register here</a>
                    </p>
                    <p class="text-center">
                        <a href="<?php echo APP_URL; ?>">Back to Home</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>