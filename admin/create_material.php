<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $divisi_id = $_POST['divisi_id'];
    $uploaded_by = 1; // Assume the uploader ID is 1 for this example

    // Handle file upload
    $file_path = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_path = 'uploads/' . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
    }

    $stmt = $conn->prepare("INSERT INTO materials (judul, deskripsi, file_path, divisi_id, uploaded_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $judul, $deskripsi, $file_path, $divisi_id, $uploaded_by);
    $stmt->execute();

    header("Location: manage_materi.php");
}

$conn->close();
?>
