<?php
session_start();
include '../db.php';

// Ambil `divisi_id` dari mentor yang login
$loggedInUserId = $_SESSION['user_id'];
$result = $conn->query("SELECT divisi_id FROM mentors WHERE id = $loggedInUserId");
$loggedInUserDivision = $result->fetch_assoc()['divisi_id'];

// Ambil nama divisi dari `divisi_id` mentor yang login
$divisionNameResult = $conn->query("SELECT nama_divisi FROM divisions WHERE id = $loggedInUserDivision");
$divisionName = $divisionNameResult->fetch_assoc()['nama_divisi'];

// Filter berdasarkan `divisi_id` mentor yang login
$divisionFilter = " AND students.divisi_id = $loggedInUserDivision";

// Ambil data siswa
$students = $conn->query("
    SELECT 
        users.id, 
        users.username, 
        users.role, 
        students.nama, 
        students.nim, 
        students.no_telephone, 
        students.alamat, 
        students.jenis_kelamin, 
        students.instansi, 
        divisions.nama_divisi
    FROM users
    JOIN students ON users.id = students.id
    JOIN divisions ON students.divisi_id = divisions.id
    WHERE users.role = 'siswa' $divisionFilter
");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Siswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2>Manage Siswa</h2>
        <h4>Divisi: <?= $divisionName ?></h4>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>NIM</th>
                    <th>No. Telephone</th>
                    <th>Alamat</th>
                    <th>Jenis Kelamin</th>
                    <th>Instansi</th>
                    <th>Divisi</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['nim'] ?></td>
                        <td><?= $row['no_telephone'] ?></td>
                        <td><?= $row['alamat'] ?></td>
                        <td><?= $row['jenis_kelamin'] ?></td>
                        <td><?= $row['instansi'] ?></td>
                        <td><?= $row['nama_divisi'] ?></td>
                        <td>
                            <a href="edit_siswa.php?id=<?= $row['id'] ?>">Edit</a>
                            <a href="delete_siswa.php?id=<?= $row['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="siswa_signup.php" class="create-user">Create Siswa</a>
    </div>
</body>
</html>
