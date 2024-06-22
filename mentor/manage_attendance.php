<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Ambil divisi_id dari mentor yang login
$loggedInUserId = $_SESSION['user_id'];

// Use prepared statements to avoid SQL injection
$stmt = $conn->prepare("SELECT divisi_id FROM mentors WHERE id = ?");
$stmt->bind_param("i", $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();
$loggedInUserDivision = $result->fetch_assoc()['divisi_id'];
$stmt->close();

// Fetch students and materials based on mentor's division
$stmt = $conn->prepare("
    SELECT students.id AS student_id, students.nama AS student_name, materials.id AS material_id, materials.judul AS material_title, 
    attendance.hadir AS is_present
    FROM students
    LEFT JOIN materials ON materials.divisi_id = students.divisi_id
    LEFT JOIN attendance ON attendance.student_id = students.id AND attendance.material_id = materials.id
    WHERE students.divisi_id = ?
");
$stmt->bind_param("i", $loggedInUserDivision);
$stmt->execute();
$attendanceData = $stmt->get_result();
$stmt->close();

$students = [];
$materials = [];
while ($row = $attendanceData->fetch_assoc()) {
    $students[$row['student_id']] = $row['student_name'];
    $materials[$row['material_id']] = $row['material_title'];
    $attendance[$row['student_id']][$row['material_id']] = $row['is_present'];
}

// Handle update attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attendanceData = $_POST['attendance'];
    
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, material_id, attendance_date, hadir) VALUES (?, ?, NOW(), ?) 
        ON DUPLICATE KEY UPDATE attendance_date = NOW(), hadir = VALUES(hadir)");
    foreach ($attendanceData as $studentId => $materials) {
        foreach ($materials as $materialId => $status) {
            $hadir = $status == 'Hadir' ? 1 : 0;
            $stmt->bind_param("iii", $studentId, $materialId, $hadir);
            $stmt->execute();
        }
    }
    $stmt->close();
    header("Location: manage_attendance.php");
    exit();
}

$divisionNameResult = $conn->query("SELECT nama_divisi FROM divisions WHERE id = $loggedInUserDivision");
$divisionName = $divisionNameResult->fetch_assoc()['nama_divisi'];

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
                            <?php foreach ($materials as $materialId => $materialTitle): ?>
                                <th><?= htmlspecialchars($materialTitle) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $studentId => $studentName): ?>
                            <tr>
                                <td><?= htmlspecialchars($studentName) ?></td>
                                <?php foreach ($materials as $materialId => $materialTitle): ?>
                                    <td>
                                        <?php $isPresent = $attendance[$studentId][$materialId] ?? 0; ?>
                                        <input type="checkbox" name="attendance[<?= $studentId ?>][<?= $materialId ?>]" value="Hadir" <?= $isPresent ? 'checked' : '' ?>>
                                        <?= $isPresent ? 'Hadir' : 'Tidak Hadir' ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit">Update Presensi</button>
            </form>
        </div>
    </div>
</body>
</html>
