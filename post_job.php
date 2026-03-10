<?php
require_once '../config.php';
$pageTitle = 'Post New Job';

// Check if logged in as recruiter
if (!isLoggedIn() || !hasRole('recruiter')) {
    redirect(APP_URL . '/recruiter/login.php');
}

$recruiter_id = $_SESSION['user_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = sanitize($_POST['title'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $description = $_POST['description'] ?? '';
    $requirements = $_POST['requirements'] ?? '';
    $location = sanitize($_POST['location'] ?? '');
    $job_type = sanitize($_POST['job_type'] ?? 'Full-time');
    $experience_level = sanitize($_POST['experience_level'] ?? 'Entry-level');
    $salary_min = intval($_POST['salary_min'] ?? 0);
    $salary_max = intval($_POST['salary_max'] ?? 0);
    $total_positions = intval($_POST['total_positions'] ?? 1);
    $deadline = $_POST['deadline'] ?? '';
    
    // Validation
    if (empty($title)) {
        $errors[] = 'Job title is required';
    }
    
    if (empty($category_id)) {
        $errors[] = 'Job category is required';
    }
    
    if (empty($description)) {
        $errors[] = 'Job description is required';
    }
    
    if (empty($location)) {
        $errors[] = 'Location is required';
    }
    
    if ($salary_min < 0 || $salary_max < 0) {
        $errors[] = 'Salary values must be positive';
    }
    
    if ($salary_min > $salary_max && $salary_max > 0) {
        $errors[] = 'Minimum salary cannot be greater than maximum salary';
    }
    
    if (empty($errors)) {
        // Insert job
        $stmt = $conn->prepare("
            INSERT INTO jobs (recruiter_id, category_id, title, description, requirements, 
                            location, job_type, experience_level, salary_min, salary_max, 
                            total_positions, deadline, status, posted_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())
        ");
        
        $stmt->bind_param("iissssssiis", $recruiter_id, $category_id, $title, $description, 
                         $requirements, $location, $job_type, $experience_level, $salary_min, 
                         $salary_max, $total_positions, $deadline);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Job posted successfully!';
            $_SESSION['message_type'] = 'success';
            redirect(APP_URL . '/recruiter/manage_jobs.php');
        } else {
            $errors[] = 'Failed to post job: ' . $conn->error;
        }
        
        $stmt->close();
    }
}

// Get categories
$categories = $conn->query("SELECT id, name FROM categories WHERE status='active' ORDER BY name");

include("../includes/header.php");
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-plus"></i> Post New Job</h4>
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
                        <!-- Job Title -->
                        <div class="form-group mb-3">
                            <label class="form-label">Job Title *</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        
                        <!-- Category & Job Type -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Job Category *</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">-- Select Category --</option>
                                        <?php while ($cat = $categories->fetch_assoc()): ?>
                                        <option value="<?php echo $cat['id']; ?>">
                                            <?php echo $cat['name']; ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Job Type *</label>
                                    <select class="form-select" name="job_type" required>
                                        <option value="Full-time">Full-time</option>
                                        <option value="Part-time">Part-time</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Freelance">Freelance</option>
                                        <option value="Internship">Internship</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Experience Level & Positions -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Experience Level *</label>
                                    <select class="form-select" name="experience_level" required>
                                        <option value="Entry-level">Entry-level</option>
                                        <option value="Mid-level">Mid-level</option>
                                        <option value="Senior">Senior</option>
                                        <option value="Executive">Executive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Number of Positions *</label>
                                    <input type="number" class="form-control" name="total_positions" value="1" min="1" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Location -->
                        <div class="form-group mb-3">
                            <label class="form-label">Job Location *</label>
                            <input type="text" class="form-control" name="location" placeholder="e.g., New York, NY" required>
                        </div>
                        
                        <!-- Salary -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Minimum Salary (Optional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="salary_min" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Maximum Salary (Optional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="salary_max" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="form-group mb-3">
                            <label class="form-label">Job Description *</label>
                            <textarea class="form-control" name="description" rows="6" 
                                      placeholder="Describe the job, responsibilities, and environment..." required></textarea>
                            <small class="form-text">At least 50 characters required</small>
                        </div>
                        
                        <!-- Requirements -->
                        <div class="form-group mb-3">
                            <label class="form-label">Requirements & Skills *</label>
                            <textarea class="form-control" name="requirements" rows="4" 
                                      placeholder="List required skills, experience, and qualifications..." required></textarea>
                        </div>
                        
                        <!-- Deadline -->
                        <div class="form-group mb-3">
                            <label class="form-label">Application Deadline (Optional)</label>
                            <input type="date" class="form-control" name="deadline">
                        </div>
                        
                        <!-- Buttons -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Post Job
                            </button>
                            <a href="manage_jobs.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
