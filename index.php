<?php
require_once 'config.php';
$pageTitle = 'Home';

include("includes/header.php");
?>

<!-- Hero Section -->
<div class="hero">
    <h1>Find Your Dream Job Today</h1>
    <p>Connect with top employers and unlock your career potential</p>
    <div class="mt-4">
        <a href="<?php echo APP_URL; ?>/jobs.php" class="btn btn-primary btn-lg me-2">
            <i class="fas fa-search"></i> Browse Jobs
        </a>
        <?php if (!isLoggedIn()): ?>
        <a href="<?php echo APP_URL; ?>/jobseeker/register.php" class="btn btn-secondary btn-lg">
            <i class="fas fa-user-plus"></i> Register Now
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Featured Section -->
<div class="container mt-5">
    <h2 class="text-center mb-4">Featured Jobs</h2>
    
    <div class="row">
        <?php
        // Get featured/latest jobs
        $result = $conn->query("
            SELECT j.id, j.title, j.description, j.location, j.job_type, j.salary_min, j.salary_max, 
                   r.company_name, r.logo 
            FROM jobs j 
            JOIN recruiters r ON j.recruiter_id = r.id 
            WHERE j.status='active' 
            ORDER BY j.posted_at DESC 
            LIMIT 6
        ");
        
        if ($result && $result->num_rows > 0):
            while ($job = $result->fetch_assoc()):
        ?>
        <div class="col-md-4 mb-4">
            <div class="card job-card h-100">
                <div class="card-body">
                    <h5 class="card-title job-title"><?php echo sanitize($job['title']); ?></h5>
                    <p class="company-name mb-2"><?php echo sanitize($job['company_name']); ?></p>
                    <p class="job-meta">
                        <i class="fas fa-map-marker-alt"></i> <?php echo sanitize($job['location']); ?>
                        <span class="badge badge-info ms-2"><?php echo $job['job_type']; ?></span>
                    </p>
                    <?php if ($job['salary_min'] && $job['salary_max']): ?>
                    <p class="salary-range">
                        <i class="fas fa-dollar-sign"></i> 
                        <?php echo number_format($job['salary_min']); ?> - <?php echo number_format($job['salary_max']); ?>
                    </p>
                    <?php endif; ?>
                    <p class="card-text"><?php echo substr(sanitize($job['description']), 0, 100) . '...'; ?></p>
                </div>
                <div class="card-footer">
                    <a href="<?php echo APP_URL; ?>/job_detail.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">
                        View Details
                    </a>
                </div>
            </div>
        </div>
        <?php
            endwhile;
        else:
        ?>
        <div class="col-12 text-center">
            <p class="text-muted">No featured jobs available at the moment.</p>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="text-center mt-4">
        <a href="<?php echo APP_URL; ?>/jobs.php" class="btn btn-primary btn-lg">
            View All Jobs
        </a>
    </div>
</div>

<!-- About Section -->
<div class="container mt-5 mb-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2>About ProHire Consultancy</h2>
            <p>ProHire Consultancy is a leading job portal connecting talented professionals with top employers worldwide. We bridge the gap between job seekers and recruiters through advanced matching and networking features.</p>
            <ul class="list-unstyled">
                <li class="mb-2"><i class="fas fa-check-circle text-success"></i> <strong>Trusted by Thousands</strong> - Thousands of successful job placements annually</li>
                <li class="mb-2"><i class="fas fa-check-circle text-success"></i> <strong>Top Employers</strong> - Partner with industry-leading companies</li>
                <li class="mb-2"><i class="fas fa-check-circle text-success"></i> <strong>24/7 Support</strong> - Dedicated team to help you succeed</li>
                <li class="mb-2"><i class="fas fa-check-circle text-success"></i> <strong>Skill Development</strong> - Resources to enhance your career</li>
            </ul>
        </div>
        <div class="col-md-6">
            <img src="<?php echo APP_URL; ?>/assets/images/about.jpg" alt="About ProHire" class="img-fluid rounded" onerror="this.src='https://via.placeholder.com/400x300?text=ProHire+Consultancy'">
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="container-fluid bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">Why Choose ProHire?</h2>
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="dashboard-card">
                    <div class="number text-primary">
                        <?php 
                        $result = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE status='active'");
                        echo $result->fetch_assoc()['count'];
                        ?>
                    </div>
                    <div class="label">Active Jobs</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="dashboard-card">
                    <div class="number text-success">
                        <?php 
                        $result = $conn->query("SELECT COUNT(*) as count FROM recruiters WHERE is_verified=TRUE");
                        echo $result->fetch_assoc()['count'];
                        ?>
                    </div>
                    <div class="label">Top Companies</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="dashboard-card">
                    <div class="number text-info">
                        <?php 
                        $result = $conn->query("SELECT COUNT(*) as count FROM job_seekers WHERE is_active=TRUE");
                        echo $result->fetch_assoc()['count'];
                        ?>
                    </div>
                    <div class="label">Active Job Seekers</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="dashboard-card">
                    <div class="number text-warning">
                        <?php 
                        $result = $conn->query("SELECT COUNT(*) as count FROM applications");
                        echo $result->fetch_assoc()['count'];
                        ?>
                    </div>
                    <div class="label">Total Applications</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-briefcase fa-3x text-primary mb-3"></i>
                    <h4>For Job Seekers</h4>
                    <p>Find your perfect job, build your profile, and connect with top employers.</p>
                    <?php if (!isLoggedIn() || !hasRole('jobseeker')): ?>
                    <a href="<?php echo APP_URL; ?>/jobseeker/register.php" class="btn btn-primary">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-building fa-3x text-secondary mb-3"></i>
                    <h4>For Recruiters</h4>
                    <p>Post jobs, find qualified candidates, and grow your team efficiently.</p>
                    <?php if (!isLoggedIn() || !hasRole('recruiter')): ?>
                    <a href="<?php echo APP_URL; ?>/recruiter/register.php" class="btn btn-secondary">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
