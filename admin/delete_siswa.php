<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$studentId = intval($_GET['id']);

// Delete the student
$conn->query("DELETE FROM students WHERE id = $studentId");

// Also delete the associated user
$conn->query("DELETE FROM users WHERE id = $studentId");

$conn->close();

header("Location: manage_siswa.php");
exit;
?>
