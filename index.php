<?php
require_once '../config.php';
$pageTitle = 'Admin Dashboard';

// Check if admin is logged in
if (!isLoggedIn() || !hasRole('admin')) {
    redirect(APP_URL . '/admin/login.php');
}

// Get statistics
$stats = [];

// Total jobs
$result = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE status='active'");
$stats['active_jobs'] = $result->fetch_assoc()['count'];

// Total recruiters
$result = $conn->query("SELECT COUNT(*) as count FROM recruiters WHERE is_verified=TRUE");
$stats['verified_recruiters'] = $result->fetch_assoc()['count'];

// Total job seekers
$result = $conn->query("SELECT COUNT(*) as count FROM job_seekers WHERE is_active=TRUE");
$stats['active_seekers'] = $result->fetch_assoc()['count'];

// Total applications
$result = $conn->query("SELECT COUNT(*) as count FROM applications");
$stats['total_applications'] = $result->fetch_assoc()['count'];

// Pending recruiter verifications
$result = $conn->query("SELECT COUNT(*) as count FROM recruiters WHERE is_verified=FALSE");
$stats['pending_verifications'] = $result->fetch_assoc()['count'];

// Pending contact messages
$result = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status='new'");
$stats['pending_messages'] = $result->fetch_assoc()['count'];

include("../includes/header.php");
?>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-chart-line"></i> Admin Dashboard</h2>
            <p class="text-muted">Welcome back, <?php echo $_SESSION['user_name']; ?>!</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="manage_jobs.php" class="btn btn-primary">
                <i class="fas fa-briefcase"></i> Manage Jobs
            </a>
            <a href="manage_categories.php" class="btn btn-secondary">
                <i class="fas fa-list"></i> Categories
            </a>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="dashboard-card">
                <div class="number"><?php echo $stats['active_jobs']; ?></div>
                <div class="label">Active Jobs</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="dashboard-card">
                <div class="number"><?php echo $stats['verified_recruiters']; ?></div>
                <div class="label">Verified Recruiters</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="dashboard-card">
                <div class="number"><?php echo $stats['active_seekers']; ?></div>
                <div class="label">Job Seekers</div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="dashboard-card">
                <div class="number"><?php echo $stats['total_applications']; ?></div>
                <div class="label">Total Applications</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="dashboard-card bg-warning-light">
                <div class="number text-warning"><?php echo $stats['pending_verifications']; ?></div>
                <div class="label">Pending Verifications</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="dashboard-card bg-info-light">
                <div class="number text-info"><?php echo $stats['pending_messages']; ?></div>
                <div class="label">New Messages</div>
            </div>
        </div>
    </div>
    
    <!-- Main Actions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="manage_jobs.php" class="list-group-item list-group-item-action">
                            <h6 class="mb-1">Manage Job Postings</h6>
                            <p class="mb-0 text-muted">Review, approve, and manage job listings</p>
                        </a>
                        <a href="manage_recruiters.php" class="list-group-item list-group-item-action">
                            <h6 class="mb-1">Manage Recruiters</h6>
                            <p class="mb-0 text-muted">Verify and manage recruiter accounts</p>
                        </a>
                        <a href="manage_users.php" class="list-group-item list-group-item-action">
                            <h6 class="mb-1">Manage Users</h6>
                            <p class="mb-0 text-muted">View and manage job seeker accounts</p>
                        </a>
                        <a href="manage_categories.php" class="list-group-item list-group-item-action">
                            <h6 class="mb-1">Job Categories</h6>
                            <p class="mb-0 text-muted">Add, edit, and delete job categories</p>
                        </a>
                        <a href="contact_messages.php" class="list-group-item list-group-item-action">
                            <h6 class="mb-1">Contact Messages</h6>
                            <p class="mb-0 text-muted"><?php echo $stats['pending_messages']; ?> new messages</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-gear"></i> Administration</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="change_password.php" class="list-group-item list-group-item-action">
                            <h6 class="mb-1">Change Password</h6>
                            <p class="mb-0 text-muted">Update your admin password</p>
                        </a>
                        <a href="view_reports.php" class="list-group-item list-group-item-action">
                            <h6 class="mb-1">View Reports</h6>
                            <p class="mb-0 text-muted">Generate system reports and analytics</p>
                        </a>
                        <a href="system_settings.php" class="list-group-item list-group-item-action">
                            <h6 class="mb-1">System Settings</h6>
                            <p class="mb-0 text-muted">Configure application settings</p>
                        </a>
                        <a href="email_templates.php" class="list-group-item list-group-item-action">
                            <h6 class="mb-1">Email Templates</h6>
                            <p class="mb-0 text-muted">Manage email notification templates</p>
                        </a>
                        <a href="../logout.php" class="list-group-item list-group-item-action text-danger">
                            <h6 class="mb-1"><i class="fas fa-sign-out-alt"></i> Logout</h6>
                            <p class="mb-0 text-muted">Sign out from admin panel</p>
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

.bg-warning-light {
    background-color: rgba(243, 156, 18, 0.1);
}

.text-warning {
    color: #f39c12 !important;
}

.bg-info-light {
    background-color: rgba(22, 160, 133, 0.1);
}

.text-info {
    color: #16a085 !important;
}

.list-group-item {
    border: 1px solid #e0e0e0;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 5px;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    border-color: #3498db;
}
</style>

<?php include("../includes/footer.php"); ?>
