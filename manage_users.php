<?php
session_start();
include("../includes/db.php");
if(!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }
include("../includes/header.php");
?>
<h2>Registered Job Seekers</h2>
<table>
  <tr><th>ID</th><th>Name</th><th>Email</th><th>Skills</th><th>Resume</th></tr>
<?php
$result = $conn->query("SELECT * FROM job_seekers ORDER BY created_at DESC");
while($row = $result->fetch_assoc()) {
    $resume_link = $row['resume'] ? "<a href='../uploads/".htmlspecialchars($row['resume'])."' target='_blank'>View</a>" : "—";
    echo "<tr>
            <td>".intval($row['id'])."</td>
            <td>".htmlspecialchars($row['name'])."</td>
            <td>".htmlspecialchars($row['email'])."</td>
            <td>".htmlspecialchars($row['skills'])."</td>
            <td>".$resume_link."</td>
          </tr>";
}
?>
</table>
<?php include("../includes/footer.php"); ?>