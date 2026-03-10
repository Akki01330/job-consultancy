<?php
session_start();
include("../includes/db.php");
if(!isset($_SESSION['seeker_id'])) { header("Location: ../seeker_login.php"); exit(); }
$id = intval($_SESSION['seeker_id']);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $skills = $conn->real_escape_string($_POST['skills']);
    $resume_name = '';
    if (!empty($_FILES['resume']['name'])) {
        $resume_name = time().'_'.basename($_FILES['resume']['name']);
        move_uploaded_file($_FILES['resume']['tmp_name'], "../uploads/".$resume_name);
        $conn->query("UPDATE job_seekers SET skills='$skills', resume='$resume_name' WHERE id=$id");
    } else {
        $conn->query("UPDATE job_seekers SET skills='$skills' WHERE id=$id");
    }
    $msg = "Profile updated.";
}
$user = $conn->query("SELECT * FROM job_seekers WHERE id=$id")->fetch_assoc();
include("../includes/header.php");
?>
<h2>My Profile</h2>
<div class="form-container">
  <?php if(isset($msg)) echo '<p style="color:green;">'.$msg.'</p>'; ?>
  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
    <textarea name="skills" placeholder="Your Skills"><?php echo htmlspecialchars($user['skills']); ?></textarea>
    <p>Current Resume: <?php echo $user['resume'] ? "<a href='../uploads/".htmlspecialchars($user['resume'])."' target='_blank'>View</a>" : "—"; ?></p>
    <input type="file" name="resume">
    <button class="btn" type="submit">Update</button>
  </form>
</div>
<?php include("../includes/footer.php"); ?>