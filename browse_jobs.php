<?php
session_start();
include("../includes/db.php");
if(!isset($_SESSION['seeker_id'])) { header("Location: ../seeker_login.php"); exit(); }
$seeker_id = intval($_SESSION['seeker_id']);
if (isset($_GET['apply'])) {
    $job_id = intval($_GET['apply']);
    $conn->query("INSERT INTO applications (job_id, seeker_id) VALUES ($job_id, $seeker_id)");

    // notify admin
    $job = $conn->query("SELECT * FROM jobs WHERE id=$job_id")->fetch_assoc();
    $admin_email = 'admin@example.com';
    $subject = 'New Job Application - ' . $job['title'];
    $message = "Hello Admin\n\nA new application has been submitted.\nJob: ".$job['title']."\nCompany: ".$job['company']."\nApplicant: ".$_SESSION['seeker_name']."\n\nRegards,\nProHire";
    $headers = "From: no-reply@jobconsultancy.com";
    @mail($admin_email, $subject, $message, $headers);

    $msg = "Application submitted! Admin notified.";
}
include("../includes/header.php");
?>
<h2>Browse Jobs</h2>
<?php if(isset($msg)) echo '<p style="color:green; text-align:center;">'.$msg.'</p>'; ?>
<div class="job-list">
<?php
$sql = "SELECT * FROM jobs ORDER BY posted_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='job-card'>
                <h3>".htmlspecialchars($row['title'])."</h3>
                <p><b>Company:</b> ".htmlspecialchars($row['company'])."</p>
                <p><b>Location:</b> ".htmlspecialchars($row['location'])."</p>
                <p>".nl2br(htmlspecialchars($row['description']))."</p>
                <a class='btn' href='browse_jobs.php?apply=".$row['id']."'>Apply</a>
              </div>";
    }
} else {
    echo "<p style='text-align:center;'>No jobs available at the moment.</p>";
}
?>
</div>
<?php include("../includes/footer.php"); ?>