<?php
include '../db.php';
session_start();

// Fungsi untuk memeriksa apakah pengguna sudah login
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Jika pengguna belum login, redirect ke halaman login
if (!isUserLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

// Ambil divisi_id dari siswa yang login
$loggedInUserId = $_SESSION['user_id'];
$result = $conn->query("SELECT divisi_id FROM students WHERE id = $loggedInUserId");
if (!$result || $result->num_rows === 0) {
    die("Error fetching division for logged-in user.");
}
$loggedInUserDivision = $result->fetch_assoc()['divisi_id'];

// Fetch materials data with sorting by newest first
$materialsQuery = "
    SELECT 
        materials.id, 
        materials.judul, 
        materials.deskripsi, 
        users.username as uploaded_by, 
        materials.created_at,
        materials.file_path,
        divisions.nama_divisi
    FROM materials
    JOIN users ON materials.uploaded_by = users.id
    JOIN divisions ON materials.divisi_id = divisions.id
    WHERE materials.divisi_id = $loggedInUserDivision
    ORDER BY materials.created_at DESC
";
$materialsResult = $conn->query($materialsQuery);
if (!$materialsResult) {
    die("Error fetching materials: " . $conn->error);
}

// Fetch attendance data for the logged-in student
$attendanceQuery = "
    SELECT material_id, hadir 
    FROM attendance 
    WHERE student_id = $loggedInUserId
";
$attendanceResult = $conn->query($attendanceQuery);
$attendance = [];
while ($row = $attendanceResult->fetch_assoc()) {
    $attendance[$row['material_id']] = $row['hadir'];
}

// Handle update attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $materialId = $_POST['material_id'];
    $hadir = isset($_POST['attendance']) && $_POST['attendance'] == 'Hadir' ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, material_id, attendance_date, hadir) VALUES (?, ?, NOW(), ?) 
        ON DUPLICATE KEY UPDATE attendance_date = NOW(), hadir = VALUES(hadir)");
    $stmt->bind_param("iii", $loggedInUserId, $materialId, $hadir);
    $stmt->execute();
    $stmt->close();
    header("Location: student_dashboard.php");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Materi <?= $loggedInUserDivision ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2>Materi Divisi <?= $loggedInUserDivision ?></h2>
        <div class="material-list" id="material-list">
            <?php while ($row = $materialsResult->fetch_assoc()): ?>
                <div class="material-item" data-division="<?= $row['nama_divisi'] ?>" data-created-at="<?= $row['created_at'] ?>">
                    <h3><?= $row['judul'] ?></h3>
                    <p><?= $row['deskripsi'] ?></p>
                    <p>Divisi: <?= $row['nama_divisi'] ?></p>
                    <p>Uploaded by: <?= $row['uploaded_by'] ?> on <span class="created-at"><?= $row['created_at'] ?></span></p>
                    <?php if ($row['file_path']): ?>
                        <a href="<?= $row['file_path'] ?>" class="download-link" download>Download File</a>
                    <?php endif; ?>
                    <p>Presensi:</p>
                    <form method="post">
                        <input type="hidden" name="material_id" value="<?= $row['id'] ?>">
                        <input type="checkbox" name="attendance" value="Hadir" <?= isset($attendance[$row['id']]) && $attendance[$row['id']] ? 'checked' : '' ?>>
                        <?= isset($attendance[$row['id']]) && $attendance[$row['id']] ? 'Hadir' : 'Tidak Hadir' ?>
                        <button type="submit">Hadir</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
