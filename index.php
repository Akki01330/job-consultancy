<?php
require_once '../config.php';
$pageTitle = 'Job Seeker Dashboard';

// Check if logged in as job seeker
if (!isLoggedIn() || !hasRole('jobseeker')) {
    redirect(APP_URL . '/jobseeker/login.php');
}

$seeker_id = $_SESSION['user_id'];

// Get seeker profile
$stmt = $conn->prepare("SELECT * FROM job_seekers WHERE id = ?");
$stmt->bind_param("i", $seeker_id);
$stmt->execute();
$seeker = $stmt->get_result()->fetch_assoc();

// Get statistics
$stats = [];

// Total applications
$result = $conn->query("SELECT COUNT(*) as count FROM applications WHERE seeker_id = $seeker_id");
$stats['total_applications'] = $result->fetch_assoc()['count'];

// Applied status breakdown
$result = $conn->query("SELECT status, COUNT(*) as count FROM applications WHERE seeker_id = $seeker_id GROUP BY status");
$stats['by_status'] = [];
while ($row = $result->fetch_assoc()) {
    $stats['by_status'][$row['status']] = $row['count'];
}

// Get recent applications
$recent_apps = $conn->query("
    SELECT a.id, a.status, a.applied_at, j.title, j.location, r.company_name
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN recruiters r ON j.recruiter_id = r.id
    WHERE a.seeker_id = $seeker_id
    ORDER BY a.applied_at DESC
    LIMIT 5
");

include("../includes/header.php");
?>

<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-user"></i> Job Seeker Dashboard</h2>
            <p class="text-muted">Welcome, <?php echo $seeker['first_name']; ?>!</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?php echo APP_URL; ?>/jobs.php" class="btn btn-primary">
                <i class="fas fa-search"></i> Find Jobs
            </a>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="number"><?php echo $stats['total_applications']; ?></div>
                <div class="label">Total Applications</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="number" style="color: #f39c12;">
                    <?php echo $stats['by_status']['applied'] ?? 0; ?>
                </div>
                <div class="label">Applied</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="number" style="color: #16a085;">
                    <?php echo $stats['by_status']['shortlisted'] ?? 0; ?>
                </div>
                <div class="label">Shortlisted</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="number" style="color: #27ae60;">
                    <?php echo $stats['by_status']['accepted'] ?? 0; ?>
                </div>
                <div class="label">Accepted</div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="row">
        <div class="col-md-8">
            <!-- Recent Applications -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Recent Applications</h5>
                </div>
                <div class="card-body">
                    <?php if ($recent_apps && $recent_apps->num_rows > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th>Applied Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($app = $recent_apps->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo sanitize($app['title']); ?></td>
                                <td><?php echo sanitize($app['company_name']); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $app['status'] == 'accepted' ? 'success' : 
                                             ($app['status'] == 'shortlisted' ? 'info' : 
                                              ($app['status'] == 'rejected' ? 'danger' : 'warning'));
                                    ?>">
                                        <?php echo ucfirst($app['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($app['applied_at']); ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <a href="applications.php" class="btn btn-sm btn-primary">View All Applications</a>
                    <?php else: ?>
                    <p class="text-muted">You haven't applied to any jobs yet. <a href="<?php echo APP_URL; ?>/jobs.php">Browse jobs</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-id-card"></i> Profile Summary</h5>
                </div>
                <div class="card-body">
                    <p><strong><?php echo $seeker['first_name'] . ' ' . $seeker['last_name']; ?></strong></p>
                    <p class="text-muted"><?php echo $seeker['headline'] ?? 'No headline'; ?></p>
                    
                    <?php if ($seeker['phone']): ?>
                    <p><i class="fas fa-phone"></i> <?php echo $seeker['phone']; ?></p>
                    <?php endif; ?>
                    
                    <?php if ($seeker['location']): ?>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo $seeker['location']; ?></p>
                    <?php endif; ?>
                    
                    <?php if ($seeker['experience_years']): ?>
                    <p><i class="fas fa-briefcase"></i> <?php echo $seeker['experience_years']; ?> years experience</p>
                    <?php endif; ?>
                    
                    <!-- Action Buttons -->
                    <div class="mt-3">
                        <a href="profile.php" class="btn btn-primary btn-sm w-100 mb-2">Edit Profile</a>
                        <a href="change_password.php" class="btn btn-secondary btn-sm w-100 mb-2">Change Password</a>
                        <a href="<?php echo APP_URL; ?>/logout.php" class="btn btn-danger btn-sm w-100">Logout</a>
                    </div>
                </div>
            </div>
            
            <!-- Resume Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file"></i> Resume</h5>
                </div>
                <div class="card-body">
                    <?php if ($seeker['resume_file']): ?>
                    <p class="text-success mb-2"><i class="fas fa-check-circle"></i> Resume uploaded</p>
                    <a href="<?php echo APP_URL; ?>/<?php echo $seeker['resume_file']; ?>" class="btn btn-sm btn-info" download>
                        <i class="fas fa-download"></i> Download
                    </a>
                    <?php else: ?>
                    <p class="text-muted">No resume uploaded yet</p>
                    <?php endif; ?>
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

.badge {
    padding: 0.4rem 0.8rem;
    font-weight: 600;
}

.badge-success { background-color: #27ae60; }
.badge-info { background-color: #16a085; }
.badge-warning { background-color: #f39c12; }
.badge-danger { background-color: #e74c3c; }
</style>

<?php include("../includes/footer.php"); ?>
