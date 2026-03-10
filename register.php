<?php
include("includes/db.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = md5($_POST['password']); // consistent with existing code
    $skills = $conn->real_escape_string($_POST['skills']);

    $sql = "INSERT INTO job_seekers (name,email,password,skills) VALUES ('$name','$email','$password','$skills')";
    if ($conn->query($sql) === TRUE) {
        // Welcome email
        $to = $email;
        $subject = "Welcome to ProHire Consultancy";
        $message = "Hello $name\n\nThank you for registering at ProHire Consultancy. You can now log in and apply for jobs.\n\nRegards,\nProHire Team";
        $headers = "From: no-reply@jobconsultancy.com";
        @mail($to, $subject, $message, $headers);

        $success = "Registration successful! Please login.";
    } else {
        $error = "Registration failed: " . $conn->error;
    }
}
include("includes/header.php");
?>
<div class="form-container">
  <h2>Job Seeker Registration</h2>
  <?php if(isset($success)) echo '<p style="color:green;">'.$success.'</p>'; ?>
  <?php if(isset($error)) echo '<p style="color:red;">'.$error.'</p>'; ?>
  <form method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <textarea name="skills" placeholder="Skills (comma separated)"></textarea>
    <button type="submit" class="btn">Register</button>
  </form>
</div>
<?php include("includes/footer.php"); ?>