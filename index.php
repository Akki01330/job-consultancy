<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }
include("../includes/header.php");
?>
<div class="dashboard">
  <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin']); ?>!</h2>
  <nav>
    <a class="btn" href="manage_jobs.php">Manage Jobs</a>
    <a class="btn" href="manage_users.php">Manage Job Seekers</a>
    <a class="btn" href="applications.php">View Applications</a>
    <a class="btn btn-danger" href="logout.php">Logout</a>
  </nav>
</div>
<?php include("../includes/footer.php"); ?>