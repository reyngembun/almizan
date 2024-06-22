<?php
include '../db.php';
session_start();

// Periksa apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$division_counts = $conn->query("
    SELECT divisions.nama_divisi, COUNT(students.id) AS count
    FROM divisions
    LEFT JOIN students ON divisions.id = students.divisi_id
    GROUP BY divisions.id
");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.html'; ?>
    <div class="main-content">
        <h2>Dashboard Siswa</h2>
        
    </div>
</body>
</html>