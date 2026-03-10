<?php
session_start();
include("../includes/db.php");
if(!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }
$id = intval($_GET['id']);
$conn->query("DELETE FROM jobs WHERE id=$id");
header("Location: manage_jobs.php");
exit();
?>