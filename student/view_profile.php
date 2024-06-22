<?php
session_start();
include '../db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil ID pengguna yang login
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Periksa apakah pengguna adalah siswa
if ($role != 'siswa') {
    die('Unauthorized access.');
}

// Ambil data siswa dari database
$sql = "SELECT s.nama, s.nim, s.no_telephone, s.alamat, s.instansi, d.nama_divisi FROM students s
        JOIN divisions d ON s.divisi_id = d.id
        WHERE s.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nama, $nim, $no_telephone, $alamat, $instansi, $nama_divisi);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2>Profile Siswa</h2>
        <div class="profile-container">
            <h3>Informasi Pribadi</h3>
            <p><strong>Nama:</strong> <?= htmlspecialchars($nama) ?></p>
            <p><strong>NIM:</strong> <?= htmlspecialchars($nim) ?></p>
            <p><strong>No Telepon:</strong> <?= htmlspecialchars($no_telephone) ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($alamat) ?></p>
            <p><strong>Instansi:</strong> <?= htmlspecialchars($instansi) ?></p>
            <p><strong>Divisi:</strong> <?= htmlspecialchars($nama_divisi) ?></p>
        </div>
    </div>
</body>
</html>
