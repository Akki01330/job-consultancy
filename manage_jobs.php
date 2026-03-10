<?php
session_start();
include("../includes/db.php");
if(!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $company = $conn->real_escape_string($_POST['company']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);
    $conn->query("INSERT INTO jobs (title, company, location, description) VALUES ('$title','$company','$location','$description')");
    $msg = "Job added.";
}
include("../includes/header.php");
?>
<h2>Manage Jobs</h2>
<div class="form-container">
  <?php if(isset($msg)) echo '<p style="color:green;">'.$msg.'</p>'; ?>
  <form method="POST">
    <input type="text" name="title" placeholder="Job Title" required>
    <input type="text" name="company" placeholder="Company Name" required>
    <input type="text" name="location" placeholder="Location" required>
    <textarea name="description" placeholder="Job Description" required></textarea>
    <button class="btn" type="submit">Add Job</button>
  </form>
</div>

<h3 style="text-align:center;">Job Listings</h3>
<table>
  <tr><th>ID</th><th>Title</th><th>Company</th><th>Location</th><th>Action</th></tr>
  <?php
  $result = $conn->query("SELECT * FROM jobs ORDER BY posted_at DESC");
  while($row = $result->fetch_assoc()) {
      echo "<tr>
              <td>".intval($row['id'])."</td>
              <td>".htmlspecialchars($row['title'])."</td>
              <td>".htmlspecialchars($row['company'])."</td>
              <td>".htmlspecialchars($row['location'])."</td>
              <td><a class='btn btn-danger' href='delete_job.php?id=".$row['id']."'>Delete</a></td>
            </tr>";
  }
  ?>
</table>
<?php include("../includes/footer.php"); ?>