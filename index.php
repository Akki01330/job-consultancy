<?php
require_once '../config.php';
$pageTitle = 'Recruiter Dashboard';

// Check if logged in as recruiter
if (!isLoggedIn() || !hasRole('recruiter')) {
    redirect(APP_URL . '/recruiter/login.php');
}

$recruiter_id = $_SESSION['user_id'];

// Get recruiter profile
$stmt = $conn->prepare("SELECT * FROM recruiters WHERE id = ?");
$stmt->bind_param("i", $recruiter_id);
$stmt->execute();
$recruiter = $stmt->get_result()->fetch_assoc();

// Get statistics
$stats = [];

// Total jobs posted
$result = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE recruiter_id = $recruiter_id");
$stats['total_jobs'] = $result->fetch_assoc()['count'];

// Active jobs
$result = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE recruiter_id = $recruiter_id AND status='active'");
$stats['active_jobs'] = $result->fetch_assoc()['count'];

// Total applications
$result = $conn->query("
    SELECT COUNT(*) as count FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE j.recruiter_id = $recruiter_id
");
$stats['total_applications'] = $result->fetch_assoc()['count'];

// Shortlisted applications
$result = $conn->query("
    SELECT COUNT(*) as count FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE j.recruiter_id = $recruiter_id AND a.status = 'shortlisted'
");
$stats['shortlisted'] = $result->fetch_assoc()['count'];

// Get recent jobs
$recent_jobs = $conn->query("
    SELECT id, title, status, posted_at FROM jobs
    WHERE recruiter_id = $recruiter_id
    ORDER BY posted_at DESC
    LIMIT 5
");

include("../includes/header.php");
?>

<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-building"></i> Recruiter Dashboard</h2>
            <p class="text-muted">Welcome, <?php echo $recruiter['company_name']; ?>!</p>
            <?php if (!$recruiter['is_verified']): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Your account is pending admin verification. 
                You can still post jobs, but they won't be visible until verified.
            </div>
            <?php endif; ?>
        </div>
        <div class="col-md-4 text-end">
            <a href="post_job.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Post New Job
            </a>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="number"><?php echo $stats['total_jobs']; ?></div>
                <div class="label">Total Jobs Posted</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="number" style="color: #27ae60;">
                    <?php echo $stats['active_jobs']; ?>
                </div>
                <div class="label">Active Jobs</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="number" style="color: #3498db;">
                    <?php echo $stats['total_applications']; ?>
                </div>
                <div class="label">Total Applications</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="number" style="color: #16a085;">
                    <?php echo $stats['shortlisted']; ?>
                </div>
                <div class="label">Shortlisted</div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="row">
        <div class="col-md-8">
            <!-- Recent Jobs -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-briefcase"></i> Your Jobs</h5>
                </div>
                <div class="card-body">
                    <?php if ($recent_jobs && $recent_jobs->num_rows > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Status</th>
                                <th>Posted Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($job = $recent_jobs->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo sanitize($job['title']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $job['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($job['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($job['posted_at']); ?></td>
                                <td>
                                    <a href="view_applicants.php?job_id=<?php echo $job['id']; ?>" class="btn btn-sm btn-info">View Applicants</a>
                                    <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <a href="manage_jobs.php" class="btn btn-sm btn-primary">View All Jobs</a>
                    <?php else: ?>
                    <p class="text-muted">You haven't posted any jobs yet. <a href="post_job.php">Post a job</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Company Profile -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Company Profile</h5>
                </div>
                <div class="card-body">
                    <h6><?php echo $recruiter['company_name']; ?></h6>
                    <p class="text-muted small"><?php echo substr($recruiter['company_description'], 0, 100); ?>...</p>
                    
                    <?php if ($recruiter['phone']): ?>
                    <p><i class="fas fa-phone"></i> <?php echo $recruiter['phone']; ?></p>
                    <?php endif; ?>
                    
                    <?php if ($recruiter['location']): ?>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo $recruiter['location']; ?></p>
                    <?php endif; ?>
                    
                    <?php if ($recruiter['website']): ?>
                    <p><i class="fas fa-globe"></i> <a href="<?php echo $recruiter['website']; ?>" target="_blank">Website</a></p>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <a href="edit_profile.php" class="btn btn-primary btn-sm w-100 mb-2">Edit Profile</a>
                        <a href="change_password.php" class="btn btn-secondary btn-sm w-100 mb-2">Change Password</a>
                        <a href="<?php echo APP_URL; ?>/logout.php" class="btn btn-danger btn-sm w-100">Logout</a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tasks"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="post_job.php" class="list-group-item list-group-item-action">
                            <p class="mb-0"><i class="fas fa-plus"></i> Post New Job</p>
                        </a>
                        <a href="manage_jobs.php" class="list-group-item list-group-item-action">
                            <p class="mb-0"><i class="fas fa-list"></i> Manage Jobs</p>
                        </a>
                        <a href="view_applicants.php" class="list-group-item list-group-item-action">
                            <p class="mb-0"><i class="fas fa-users"></i> View All Applicants</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.dashboard-card .number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #3498db;
}

.dashboard-card .label {
    color: #6c757d;
    margin-top: 0.5rem;
    font-weight: 500;
}

.list-group-item {
    border: 1px solid #e0e0e0;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    border-color: #3498db;
    transform: translateX(5px);
}

.badge-success { background-color: #27ae60; }
.badge-secondary { background-color: #95a5a6; }
</style>

<?php include("../includes/footer.php"); ?>
