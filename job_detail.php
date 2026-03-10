<?php
require_once 'config.php';
$pageTitle = 'Job Details';

// Get job ID from URL
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($job_id <= 0) {
    redirect(APP_URL . '/jobs.php');
}

// Get job details
$stmt = $conn->prepare("
    SELECT j.id, j.title, j.description, j.requirements, j.location, j.job_type, 
           j.salary_min, j.salary_max, j.salary_currency, j.experience_level, 
           j.total_positions, j.deadline, j.posted_at, j.status,
           r.company_name, r.company_description, r.location as company_location,
           r.website, r.phone, r.company_email, c.name as category_name
    FROM jobs j
    JOIN recruiters r ON j.recruiter_id = r.id
    JOIN categories c ON j.category_id = c.id
    WHERE j.id = ? AND j.status = 'active'
");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = 'Job not found or is no longer available';
    $_SESSION['message_type'] = 'warning';
    redirect(APP_URL . '/jobs.php');
}

$job = $result->fetch_assoc();

// Check if user has already applied
$has_applied = false;
if (isLoggedIn() && hasRole('jobseeker')) {
    $check_stmt = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND seeker_id = ?");
    $check_stmt->bind_param("ii", $job_id, $_SESSION['user_id']);
    $check_stmt->execute();
    $has_applied = $check_stmt->get_result()->num_rows > 0;
    $check_stmt->close();
}

// Handle application submission
if (isLoggedIn() && hasRole('jobseeker') && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply'])) {
    if (!$has_applied) {
        $cover_letter = sanitize($_POST['cover_letter'] ?? '');
        
        $apply_stmt = $conn->prepare("
            INSERT INTO applications (job_id, seeker_id, cover_letter, status, applied_at)
            VALUES (?, ?, ?, 'applied', NOW())
        ");
        $apply_stmt->bind_param("iis", $job_id, $_SESSION['user_id'], $cover_letter);
        
        if ($apply_stmt->execute()) {
            $_SESSION['message'] = 'Application submitted successfully!';
            $_SESSION['message_type'] = 'success';
            $has_applied = true;
        } else {
            $_SESSION['message'] = 'Failed to submit application';
            $_SESSION['message_type'] = 'danger';
        }
        $apply_stmt->close();
    }
}

include("includes/header.php");
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <!-- Job Details -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge badge-primary"><?php echo $job['category_name']; ?></span>
                        <span class="badge badge-info"><?php echo $job['job_type']; ?></span>
                        <span class="badge badge-secondary"><?php echo $job['experience_level']; ?></span>
                    </div>
                    
                    <h1><?php echo sanitize($job['title']); ?></h1>
                    <h5 class="text-primary mb-3"><?php echo sanitize($job['company_name']); ?></h5>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-map-marker-alt"></i> Location:</strong><br>
                            <?php echo sanitize($job['location']); ?></p>
                            
                            <p><strong><i class="fas fa-briefcase"></i> Job Type:</strong><br>
                            <?php echo $job['job_type']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <?php if ($job['salary_min'] && $job['salary_max']): ?>
                            <p><strong><i class="fas fa-dollar-sign"></i> Salary:</strong><br>
                            <?php echo number_format($job['salary_min']); ?> - <?php echo number_format($job['salary_max']); ?> <?php echo $job['salary_currency']; ?></p>
                            <?php endif; ?>
                            
                            <p><strong><i class="fas fa-user"></i> Positions:</strong><br>
                            <?php echo $job['total_positions']; ?> position(s) available</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h4>Job Description</h4>
                    <p><?php echo nl2br(sanitize($job['description'])); ?></p>
                    
                    <h4>Requirements</h4>
                    <p><?php echo nl2br(sanitize($job['requirements'])); ?></p>
                    
                    <hr>
                    
                    <p class="text-muted">
                        <strong>Posted:</strong> <?php echo formatDate($job['posted_at']); ?><br>
                        <strong>Deadline:</strong> <?php echo $job['deadline'] ? formatDate($job['deadline']) : 'Not specified'; ?>
                    </p>
                </div>
            </div>
            
            <!-- Application Form -->
            <?php if (isLoggedIn() && hasRole('jobseeker')): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-paper-plane"></i> 
                    <?php echo $has_applied ? 'You have applied for this job' : 'Apply for this Job'; ?></h5>
                </div>
                <div class="card-body">
                    <?php if ($has_applied): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> You have already applied for this position.
                        Check your applications status in your dashboard.
                    </div>
                    <?php else: ?>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Cover Letter (Optional)</label>
                            <textarea class="form-control" name="cover_letter" rows="5" 
                                      placeholder="Tell the recruiter about yourself and why you're interested in this position..."></textarea>
                        </div>
                        <button type="submit" name="apply" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Application
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <p><?php if (isLoggedIn()) echo 'Please log in as a job seeker to apply for this job.'; 
                         else echo 'Please <a href="' . APP_URL . '/jobseeker/login.php">log in</a> or <a href="' . APP_URL . '/jobseeker/register.php">register</a> to apply for this job.'; ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar - Company Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Company Information</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-primary"><?php echo sanitize($job['company_name']); ?></h6>
                    
                    <p class="text-muted"><?php echo nl2br(sanitize($job['company_description'])); ?></p>
                    
                    <hr>
                    
                    <?php if ($job['company_location']): ?>
                    <p><strong><i class="fas fa-map-marker-alt"></i></strong> 
                    <?php echo sanitize($job['company_location']); ?></p>
                    <?php endif; ?>
                    
                    <?php if ($job['phone']): ?>
                    <p><strong><i class="fas fa-phone"></i></strong> 
                    <?php echo sanitize($job['phone']); ?></p>
                    <?php endif; ?>
                    
                    <?php if ($job['company_email']): ?>
                    <p><strong><i class="fas fa-envelope"></i></strong> 
                    <?php echo sanitize($job['company_email']); ?></p>
                    <?php endif; ?>
                    
                    <?php if ($job['website']): ?>
                    <p><strong><i class="fas fa-globe"></i></strong> 
                    <a href="<?php echo $job['website']; ?>" target="_blank">Visit Website</a></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Share -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Share This Job</h5>
                </div>
                <div class="card-body">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(APP_URL . '/job_detail.php?id=' . $job_id); ?>" 
                       class="btn btn-sm btn-primary w-100 mb-2" target="_blank">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(APP_URL . '/job_detail.php?id=' . $job_id); ?>&text=<?php echo urlencode($job['title']); ?>" 
                       class="btn btn-sm btn-info w-100 mb-2" target="_blank">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(APP_URL . '/job_detail.php?id=' . $job_id); ?>" 
                       class="btn btn-sm btn-primary w-100" target="_blank">
                        <i class="fab fa-linkedin"></i> LinkedIn
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
