<?php
include '../db.php';

// Ambil informasi mentor yang login
$loggedInUserId = $_SESSION['user_id'];
$mentorQuery = "
    SELECT 
        mentors.nama, 
        divisions.nama_divisi 
    FROM mentors 
    JOIN divisions ON mentors.divisi_id = divisions.id 
    WHERE mentors.id = $loggedInUserId
";
$result = $conn->query($mentorQuery);
$mentor = $result->fetch_assoc();

$conn->close();
?>
<div class="sidebar">
    <img src="../logo.png" alt="Logo" class="logo">
    <h3>Welcome <?= $mentor['nama'] ?> (<?= $mentor['nama_divisi'] ?>)</h3>
    <ul>
        <li><a href="mentor_dashboard.php">Dashboard</a></li>
        <li><a href="manage_siswa.php">Manage Siswa</a></li>
        <li><a href="manage_attendance.php">Manage Presensi</a></li>
        <li><a href="manage_materi.php">Manage Materi</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

