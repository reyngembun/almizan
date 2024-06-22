<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mentor') {
    header("Location: index.php");
    exit;
}


// Ambil divisi_id dari mentor yang login
$loggedInUserId = $_SESSION['user_id'];
$result = $conn->query("SELECT divisi_id FROM mentors WHERE id = $loggedInUserId");
if (!$result || $result->num_rows === 0) {
    die("Error fetching division for logged-in user.");
}
$loggedInUserDivision = $result->fetch_assoc()['divisi_id'];



$studentId = intval($_GET['id']);

// Delete the student
$conn->query("DELETE FROM students WHERE id = $studentId");

// Also delete the associated user
$conn->query("DELETE FROM users WHERE id = $studentId");

$conn->close();

header("Location: manage_siswa.php");
exit;
?>
