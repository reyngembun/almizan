<!-- sidebar.html -->
<?php
include '../db.php';

// Ambil informasi siswa yang login
$loggedInUserId = $_SESSION['user_id'];
$siswaQuery = "
    SELECT 
        students.nama, 
        divisions.nama_divisi 
    FROM students 
    JOIN divisions ON students.divisi_id = divisions.id 
    WHERE students.id = $loggedInUserId
";
$result = $conn->query($siswaQuery);
$siswa = $result->fetch_assoc();

$conn->close();
?>

<div class="sidebar">
    <img src="../logo.png" alt="Logo" class="logo">
    <h3>Welcome <?= $siswa['nama'] ?> (<?= $siswa['nama_divisi'] ?>)</h3>
    <ul>
        <li><a href="student_dashboard.php">Dashboard</a></li>
        <li><a href="view_profile.php">Profile</a></li>
        <li><a href="reset_pass.php">Reset Password</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>
