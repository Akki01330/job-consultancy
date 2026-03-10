<?php
session_start();
if(!isset($_SESSION['seeker_id'])) { header("Location: ../seeker_login.php"); exit(); }
include("../includes/header.php");
?>
<div class="dashboard">
  <h2>Welcome, <?php echo htmlspecialchars($_SESSION['seeker_name']); ?>!</h2>
  <nav>
    <a class="btn" href="profile.php">My Profile</a>
    <a class="btn" href="browse_jobs.php">Browse Jobs</a>
    <a class="btn" href="applications.php">My Applications</a>
    <a class="btn btn-danger" href="logout.php">Logout</a>
  </nav>
</div>
<?php include("../includes/footer.php"); ?>