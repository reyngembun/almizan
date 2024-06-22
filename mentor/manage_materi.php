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

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Materials</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2>Manage Materi</h2>
        <div class="form-container">
            <form action="create_material.php" method="post" enctype="multipart/form-data">
                <h3>Buat Materi Baru</h3>
                <label for="judul">Judul</label>
                <input type="text" id="judul" name="judul" required>
                <label for="deskripsi">Isi</label>
                <textarea id="deskripsi" name="deskripsi" required></textarea>
                <label for="file">Add file</label>
                <input type="file" id="file" name="file">
                <button type="submit">Upload</button>
            </form>
        </div>
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
                    <a href="edit_material.php?id=<?= $row['id'] ?>" class="edit-link">Edit</a>
                    <a href="delete_material.php?id=<?= $row['id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this material?')">Delete</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
