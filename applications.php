<?php
session_start();
include("../includes/db.php");
if(!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }
include("../includes/header.php");
?>
<h2>Job Applications</h2>
<table>
  <tr><th>Application ID</th><th>Job Title</th><th>Applicant</th><th>Applied At</th></tr>
<?php
$sql = "SELECT a.id, j.title, s.name, a.applied_at 
        FROM applications a
        JOIN jobs j ON a.job_id=j.id
        JOIN job_seekers s ON a.seeker_id=s.id
        ORDER BY a.applied_at DESC";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>".intval($row['id'])."</td>
            <td>".htmlspecialchars($row['title'])."</td>
            <td>".htmlspecialchars($row['name'])."</td>
            <td>".htmlspecialchars($row['applied_at'])."</td>
          </tr>";
}
?>
</table>
<?php include("../includes/footer.php"); ?>