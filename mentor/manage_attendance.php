<?php
session_start();
include '../db.php';

// Fungsi untuk memeriksa apakah pengguna sudah login
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Jika pengguna belum login, redirect ke halaman login
if (!isUserLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

// Ambil divisi_id dari mentor yang login
$loggedInUserId = $_SESSION['user_id'];
$result = $conn->query("SELECT divisi_id FROM mentors WHERE id = $loggedInUserId");
$loggedInUserDivision = $result->fetch_assoc()['divisi_id'];

// Ambil nama divisi dari divisi_id mentor yang login
$divisionNameResult = $conn->query("SELECT nama_divisi FROM divisions WHERE id = $loggedInUserDivision");
$divisionName = $divisionNameResult->fetch_assoc()['nama_divisi'];

// Set default division ID
$divisionId = $loggedInUserDivision; // Default to mentor's division



// Fetch students based on mentor's division
$studentsQuery = "
    SELECT students.id, students.nama
    FROM students
    WHERE students.divisi_id = $loggedInUserDivision
";
$studentsResult = $conn->query($studentsQuery);
if (!$studentsResult) {
    die("Error fetching students: " . $conn->error);
}

// Fetch materials based on mentor's division
$materialsQuery = "
    SELECT id, judul
    FROM materials
    WHERE divisi_id = $loggedInUserDivision
";
$materialsResult = $conn->query($materialsQuery);
if (!$materialsResult) {
    die("Error fetching materials: " . $conn->error);
}

// Handle update attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attendanceData = $_POST['attendance'];
    
    foreach ($attendanceData as $studentId => $materials) {
        foreach ($materials as $materialId => $status) {
            if ($status == 'Hadir') {
                // Insert or update attendance
                $conn->query("INSERT INTO attendance (student_id, material_id, attendance_date, hadir) VALUES ($studentId, $materialId, NOW(), 1) 
                ON DUPLICATE KEY UPDATE attendance_date = NOW(), hadir = 1");
            } else {
                // Update attendance to not present
                $conn->query("INSERT INTO attendance (student_id, material_id, attendance_date, hadir) VALUES ($studentId, $materialId, NOW(), 0) 
                ON DUPLICATE KEY UPDATE hadir = 0");
            }
        }
    }
    header("Location: manage_attendance.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .scrollable-table {
            max-height: 500px; /* Atur tinggi maksimum */
            overflow: auto; /* Tambahkan scrollbars jika konten melebihi ukuran */
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2>Manage Attendance</h2>
        <div class="filter-form">
        <h4>Divisi: <?= $divisionName ?></h4>
        </div>
        <div class="scrollable-table">
            <form method="post">
                <table border="1">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <?php 
                            $materialsResult->data_seek(0); // Reset the result pointer
                            while ($material = $materialsResult->fetch_assoc()): ?>
                                <th><?= $material['judul'] ?></th>
                            <?php endwhile; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $studentsResult->data_seek(0); // Reset the result pointer
                        while ($student = $studentsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= $student['nama'] ?></td>
                                <?php 
                                $materialsResult->data_seek(0); // Reset the result pointer
                                while ($material = $materialsResult->fetch_assoc()): ?>
                                    <td>
                                        <?php
                                        // Check attendance status
                                        $attendanceCheckQuery = "SELECT hadir FROM attendance WHERE student_id = {$student['id']} AND material_id = {$material['id']}";
                                        $attendanceCheckResult = $conn->query($attendanceCheckQuery);
                                        $isPresent = false;
                                        if ($attendanceCheckResult && $attendanceCheckResult->num_rows > 0) {
                                            $attendanceRow = $attendanceCheckResult->fetch_assoc();
                                            $isPresent = $attendanceRow['hadir'] == 1;
                                        }
                                        ?>
                                        <?php if ($isPresent): ?>
                                            Hadir
                                        <?php else: ?>
                                            Tidak Hadir
                                        <?php endif; ?>
                                        <input type="checkbox" name="attendance[<?= $student['id'] ?>][<?= $material['id'] ?>]" value="Hadir" <?= $isPresent ? 'checked' : '' ?>>
                                    </td>
                                <?php endwhile; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <button type="submit">Update Presensi</button>
            </form>
        </div>
    </div>
</body>
</html>
