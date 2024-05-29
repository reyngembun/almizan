<!-- dashboard.php -->
<?php
include '../db.php';
// Fetch data
$user_mentor_count = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'mentor'")->fetch_assoc()['count'];
$user_siswa_count = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'siswa'")->fetch_assoc()['count'];
$materi_count = $conn->query("SELECT COUNT(*) AS count FROM materials")->fetch_assoc()['count'];

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
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.html'; ?>
    <div class="main-content">
        <h2>Dashboard Admin</h2>
        <div class="stats">
            <div class="stat">
                <h3>User Mentor</h3>
                <p><?php echo $user_mentor_count; ?></p>
            </div>
            <div class="stat">
                <h3>User Siswa</h3>
                <p><?php echo $user_siswa_count; ?></p>
            </div>
            <div class="stat">
                <h3>Materi</h3>
                <p><?php echo $materi_count; ?></p>
            </div>
        </div>
        <div class="charts">
            <div class="chart">
                <h3>Materi Monthly Recap</h3>
                <!-- Add chart code here (e.g., using a chart library like Chart.js) -->
            </div>
            <div class="chart">
                <h3>Siswa Berdasarkan Divisi</h3>
                <?php while ($row = $division_counts->fetch_assoc()): ?>
                    <p><?php echo $row['nama_divisi']; ?>: <?php echo $row['count']; ?> of 1000</p>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>
