<?php
session_start();
session_destroy();
header("Location: ../seeker_login.php");
exit();
?>