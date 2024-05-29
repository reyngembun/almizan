<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$mentorId = intval($_GET['id']);

// Delete the mentor
$conn->query("DELETE FROM mentors WHERE id = $mentorId");

// Also delete the associated user
$conn->query("DELETE FROM users WHERE id = $mentorId");

$conn->close();

header("Location: manage_mentor.php");
exit;
?>
