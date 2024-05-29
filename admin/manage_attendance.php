<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Set default division ID
$divisionId = 'all';
if (isset($_GET['division_id']) && $_GET['division_id'] != 'all') {
    $divisionId = intval($_GET['division_id']);
}

// Fetch divisions for the filter dropdown
$divisionsResult = $conn->query("SELECT id, nama_divisi FROM divisions");
if (!$divisionsResult) {
    die("Error fetching divisions: " . $conn->error);
}

// Validate division ID
$validDivision = ($divisionId == 'all');
while ($division = $divisionsResult->fetch_assoc()) {
    if ($division['id'] == $divisionId) {
        $validDivision = true;
        break;
    }
}
if (!$validDivision) {
    die("Invalid Division ID.");
}

// Fetch students based on selected division
$studentsQuery = "
    SELECT students.id, students.nama
    FROM students
    " . ($divisionId != 'all' ? "WHERE students.divisi_id = $divisionId" : "") . "
";
$studentsResult = $conn->query($studentsQuery);
if (!$studentsResult) {
    die("Error fetching students: " . $conn->error);
}

// Fetch materials based on selected division
$materialsQuery = "
    SELECT id, judul
    FROM materials
    " . ($divisionId != 'all' ? "WHERE divisi_id = $divisionId" : "") . "
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
    header("Location: manage_attendance.php?division_id=$divisionId");
    exit;
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
    <?php include 'sidebar.html'; ?>
    <div class="main-content">
        <h2>Manage Attendance</h2>
        <div class="filter-form">
            <form method="get">
                <label for="division_id">Filter Divisi:</label>
                <select name="division_id" id="division_id" onchange="this.form.submit()">
                    <option value="all" <?= $divisionId == 'all' ? 'selected' : '' ?>>All</option>
                    <?php 
                    $divisionsResult->data_seek(0); // Reset the result pointer
                    while ($division = $divisionsResult->fetch_assoc()): ?>
                        <option value="<?= $division['id'] ?>" <?= $divisionId == $division['id'] ? 'selected' : '' ?>>
                            <?= $division['nama_divisi'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
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
                        $materialsResult->data_seek(0); // Reset the result pointer
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
                                <?php 
                                endwhile; ?>
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
