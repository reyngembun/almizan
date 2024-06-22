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

// Ambil divisi_id dari mentor yang login
$loggedInUserId = $_SESSION['user_id'];
$result = $conn->query("SELECT divisi_id FROM mentors WHERE id = $loggedInUserId");
if (!$result || $result->num_rows === 0) {
    die("Error fetching division for logged-in user.");
}
$loggedInUserDivision = $result->fetch_assoc()['divisi_id'];

// Ambil data dari form jika dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $divisi_id = $loggedInUserDivision;
    $uploaded_by = $_SESSION['user_id'];
    $file_path = '';

    // Cek apakah ada file yang diunggah
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = basename($_FILES['file']['name']);
        $target_dir = "../uploads/";
        $target_file = $target_dir . $file_name;

        // Pastikan direktori tujuan ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Pindahkan file yang diunggah ke direktori tujuan
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file_path = $target_file;
        } else {
            die("Error uploading file.");
        }
    }

    // Simpan data materi ke database
    $query = "INSERT INTO materials (judul, deskripsi, divisi_id, uploaded_by, file_path, created_at) 
              VALUES ('$judul', '$deskripsi', $divisi_id, $uploaded_by, '$file_path', NOW())";

    if ($conn->query($query)) {
        header("Location: manage_materi.php");
        exit();
    } else {
        die("Error saving material: " . $conn->error);
    }
}

$conn->close();
?>