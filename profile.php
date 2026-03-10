<?php
require_once '../config.php';
$pageTitle = 'Edit Profile';

// Check if logged in as job seeker
if (!isLoggedIn() || !hasRole('jobseeker')) {
    redirect(APP_URL . '/jobseeker/login.php');
}

$seeker_id = $_SESSION['user_id'];
$errors = [];
$success = false;

// Get current profile
$stmt = $conn->prepare("SELECT * FROM job_seekers WHERE id = ?");
$stmt->bind_param("i", $seeker_id);
$stmt->execute();
$seeker = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $location = sanitize($_POST['location'] ?? '');
    $headline = sanitize($_POST['headline'] ?? '');
    $bio = sanitize($_POST['bio'] ?? '');
    $skills = sanitize($_POST['skills'] ?? '');
    $experience_years = intval($_POST['experience_years'] ?? 0);
    $education = sanitize($_POST['education'] ?? '');
    
    // Validation
    if (empty($first_name)) {
        $errors[] = 'First name is required';
    }
    
    if (empty($errors)) {
        // Update profile
        $stmt = $conn->prepare("
            UPDATE job_seekers 
            SET first_name=?, last_name=?, phone=?, location=?, headline=?, bio=?, 
                skills=?, experience_years=?, education=?, updated_at=NOW()
            WHERE id=?
        ");
        
        $stmt->bind_param("sssssssii", $first_name, $last_name, $phone, $location, 
                         $headline, $bio, $skills, $experience_years, $education, $seeker_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Profile updated successfully!';
            $_SESSION['message_type'] = 'success';
            $_SESSION['user_name'] = $first_name;
            
            // Refresh seeker data
            $stmt = $conn->prepare("SELECT * FROM job_seekers WHERE id = ?");
            $stmt->bind_param("i", $seeker_id);
            $stmt->execute();
            $seeker = $stmt->get_result()->fetch_assoc();
            
            $success = true;
        } else {
            $errors[] = 'Failed to update profile: ' . $conn->error;
        }
        
        $stmt->close();
    }
}

include("../includes/header.php");
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-user-edit"></i> Edit Profile</h4>
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
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Profile updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation">
                        <!-- Name -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" class="form-control" name="first_name" 
                                           value="<?php echo $seeker['first_name']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" 
                                           value="<?php echo $seeker['last_name']; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Info -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?php echo $seeker['phone']; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" class="form-control" name="location" 
                                           placeholder="City, State/Country"
                                           value="<?php echo $seeker['location']; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Professional Info -->
                        <div class="form-group mb-3">
                            <label class="form-label">Headline</label>
                            <input type="text" class="form-control" name="headline" 
                                   placeholder="e.g., Senior PHP Developer"
                                   value="<?php echo $seeker['headline']; ?>">
                            <small class="form-text">Your professional title or headline</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Bio / About You</label>
                            <textarea class="form-control" name="bio" rows="4" 
                                      placeholder="Tell employers about yourself..."><?php echo $seeker['bio']; ?></textarea>
                        </div>
                        
                        <!-- Experience -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">years of Experience</label>
                                    <input type="number" class="form-control" name="experience_years" 
                                           min="0" max="60" value="<?php echo $seeker['experience_years']; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Skills -->
                        <div class="form-group mb-3">
                            <label class="form-label">Skills</label>
                            <textarea class="form-control" name="skills" rows="3" 
                                      placeholder="Enter skills separated by commas (e.g., PHP, MySQL, JavaScript)"><?php echo $seeker['skills']; ?></textarea>
                        </div>
                        
                        <!-- Education -->
                        <div class="form-group mb-3">
                            <label class="form-label">Education</label>
                            <textarea class="form-control" name="education" rows="3" 
                                      placeholder="e.g., Bachelor of Science in Computer Science"><?php echo $seeker['education']; ?></textarea>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="../jobseeker/" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <!-- Additional Actions -->
                    <div class="mt-4">
                        <h6>Other Account Options:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="change_password.php" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-key"></i> Change Password
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="upload_resume.php" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-file-upload"></i> Upload Resume
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
