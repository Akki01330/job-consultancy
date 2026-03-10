<?php
session_start();
include("../includes/db.php");
if(!isset($_SESSION['seeker_id'])) { header("Location: ../seeker_login.php"); exit(); }
$id = intval($_SESSION['seeker_id']);
include("../includes/header.php");
?>
<h2>My Applications</h2>
<table>
  <tr><th>Job Title</th><th>Company</th><th>Applied At</th></tr>
<?php
$sql = "SELECT j.title, j.company, a.applied_at 
        FROM applications a
        JOIN jobs j ON a.job_id=j.id
        WHERE a.seeker_id=$id
        ORDER BY a.applied_at DESC";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>".htmlspecialchars($row['title'])."</td>
            <td>".htmlspecialchars($row['company'])."</td>
            <td>".htmlspecialchars($row['applied_at'])."</td>
          </tr>";
}
?>
</table>
<?php include("../includes/footer.php"); ?>