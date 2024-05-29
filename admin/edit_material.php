<?php
include '../db.php';

$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $divisi_id = $_POST['divisi_id'];

    $stmt = $conn->prepare("UPDATE materials SET judul = ?, deskripsi = ?, divisi_id = ? WHERE id = ?");
    $stmt->bind_param("ssii", $judul, $deskripsi, $divisi_id, $id);
    $stmt->execute();

    header("Location: manage_materi.php");
}

$material = $conn->query("SELECT * FROM materials WHERE id = $id")->fetch_assoc();
$divisions = $conn->query("SELECT id, nama_divisi FROM divisions");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Material</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.html'; ?>
    <div class="main-content">
        <h2>Edit Material</h2>
        <form method="post">
            <label for="judul">Judul:</label>
            <input type="text" id="judul" name="judul" value="<?= $material['judul'] ?>" required>
            <label for="deskripsi">Deskripsi:</label>
            <textarea id="deskripsi" name="deskripsi" required><?= $material['deskripsi'] ?></textarea>
            <label for="divisi_id">Divisi:</label>
            <select id="divisi_id" name="divisi_id">
                <?php while ($division = $divisions->fetch_assoc()): ?>
                    <option value="<?= $division['id'] ?>" <?= $division['id'] == $material['divisi_id'] ? 'selected' : '' ?>>
                        <?= $division['nama_divisi'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
