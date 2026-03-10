<?php
require_once 'config.php';
$pageTitle = 'Browse Jobs';

include("includes/header.php");

// Get search parameters
$search = sanitize($_GET['search'] ?? '');
$location = sanitize($_GET['location'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$job_type = sanitize($_GET['job_type'] ?? '');
$salary_min = sanitize($_GET['salary_min'] ?? '');
$salary_max = sanitize($_GET['salary_max'] ?? '');

// Build query
$where_clause = "WHERE j.status='active'";
$params = [];
$types = '';

if (!empty($search)) {
    $where_clause .= " AND (j.title LIKE ? OR j.description LIKE ? OR r.company_name LIKE ?)";
    $search_param = '%' . $search . '%';
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
    $types .= 'sss';
}

if (!empty($location)) {
    $where_clause .= " AND j.location LIKE ?";
    $location_param = '%' . $location . '%';
    $params[] = $location_param;
    $types .= 's';
}

if (!empty($category)) {
    $where_clause .= " AND j.category_id = ?";
    $params[] = intval($category);
    $types .= 'i';
}

if (!empty($job_type)) {
    $where_clause .= " AND j.job_type = ?";
    $params[] = $job_type;
    $types .= 's';
}

if (!empty($salary_min)) {
    $where_clause .= " AND j.salary_min >= ?";
    $params[] = intval($salary_min);
    $types .= 'i';
}

if (!empty($salary_max)) {
    $where_clause .= " AND j.salary_max <= ?";
    $params[] = intval($salary_max);
    $types .= 'i';
}

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM jobs j JOIN recruiters r ON j.recruiter_id = r.id " . $where_clause;
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_jobs = $count_result->fetch_assoc()['total'];

// Pagination
$pagination = getPaginationData($total_jobs, ITEMS_PER_PAGE);
$offset = $pagination['offset'];

// Get jobs
$sql = "SELECT j.id, j.title, j.description, j.location, j.job_type, j.salary_min, j.salary_max, 
                j.experience_level, j.posted_at, r.company_name, r.logo, c.name as category_name
         FROM jobs j 
         JOIN recruiters r ON j.recruiter_id = r.id
         JOIN categories c ON j.category_id = c.id
         " . $where_clause . "
         ORDER BY j.posted_at DESC 
         LIMIT ?, ?";

$params[] = $offset;
$params[] = ITEMS_PER_PAGE;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get categories for filter
$categories_result = $conn->query("SELECT id, name FROM categories WHERE status='active' ORDER BY name");
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-search"></i> Browse Jobs</h2>
    
    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Job title, company..." value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="location" placeholder="Location" value="<?php echo $location; ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">All Categories</option>
                        <?php while ($cat = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo $cat['name']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="job_type">
                        <option value="">All Types</option>
                        <option value="Full-time" <?php echo $job_type == 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
                        <option value="Part-time" <?php echo $job_type == 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
                        <option value="Contract" <?php echo $job_type == 'Contract' ? 'selected' : ''; ?>>Contract</option>
                        <option value="Freelance" <?php echo $job_type == 'Freelance' ? 'selected' : ''; ?>>Freelance</option>
                        <option value="Internship" <?php echo $job_type == 'Internship' ? 'selected' : ''; ?>>Internship</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="salary_min" placeholder="Min Salary" value="<?php echo $salary_min; ?>">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="salary_max" placeholder="Max Salary" value="<?php echo $salary_max; ?>">
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Search
                    </button>
                </div>
                <div class="col-md-6">
                    <a href="jobs.php" class="btn btn-secondary w-100">
                        <i class="fas fa-redo"></i> Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Results Info -->
    <div class="mb-3">
        <p class="text-muted">Found <strong><?php echo $total_jobs; ?></strong> job(s)</p>
    </div>
    
    <!-- Job Listings -->
    <?php if ($result && $result->num_rows > 0): ?>
    <div class="job-list">
        <?php while ($job = $result->fetch_assoc()): ?>
        <div class="card job-card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="job-title">
                            <a href="<?php echo APP_URL; ?>/job_detail.php?id=<?php echo $job['id']; ?>">
                                <?php echo sanitize($job['title']); ?>
                            </a>
                        </h5>
                        <p class="company-name mb-2">
                            <i class="fas fa-building"></i> 
                            <?php echo sanitize($job['company_name']); ?>
                        </p>
                        <p class="job-meta">
                            <i class="fas fa-map-marker-alt"></i> <?php echo sanitize($job['location']); ?>
                            <span class="badge badge-info ms-2"><?php echo $job['job_type']; ?></span>
                            <span class="badge badge-secondary ms-2"><?php echo $job['experience_level']; ?></span>
                            <span class="badge badge-success ms-2"><?php echo $job['category_name']; ?></span>
                        </p>
                        <p class="mb-0 text-truncate">
                            <?php echo substr(sanitize($job['description']), 0, 150); ?>...
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <?php if ($job['salary_min'] && $job['salary_max']): ?>
                        <p class="salary-range mb-2">
                            <i class="fas fa-dollar-sign"></i>
                            <?php echo number_format($job['salary_min']); ?> - <?php echo number_format($job['salary_max']); ?>
                        </p>
                        <?php endif; ?>
                        <small class="text-muted d-block mb-2">
                            Posted: <?php echo formatDate($job['posted_at']); ?>
                        </small>
                        <?php if (isLoggedIn() && hasRole('jobseeker')): ?>
                        <a href="<?php echo APP_URL; ?>/job_detail.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">
                            Apply Now
                        </a>
                        <?php else: ?>
                        <a href="<?php echo APP_URL; ?>/jobseeker/login.php" class="btn btn-sm btn-primary">
                            Login to Apply
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
    <nav aria-label="Page navigation" class="mt-5">
        <ul class="pagination justify-content-center">
            <?php if ($pagination['current_page'] > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">First</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] - 1])); ?>">Previous</a>
            </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>
            
            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
            <li class="page-item">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] + 1])); ?>">Next</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['total_pages']])); ?>">Last</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="alert alert-info">
        <h5>No jobs found</h5>
        <p>Try adjusting your search criteria or browse all available jobs.</p>
        <a href="jobs.php" class="btn btn-primary">Clear Filters</a>
    </div>
    <?php endif; ?>
</div>

<?php include("includes/footer.php"); ?>