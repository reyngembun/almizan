<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$mentorId = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $alamat = $conn->real_escape_string($_POST['alamat']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);
    $jenis_kelamin = $conn->real_escape_string($_POST['jenis_kelamin']);
    $divisi_id = intval($_POST['divisi_id']);

    $conn->query("UPDATE mentors SET nama='$nama', alamat='$alamat', no_hp='$no_hp', jenis_kelamin='$jenis_kelamin', divisi_id='$divisi_id' WHERE id=$mentorId");
    header("Location: manage_mentor.php");
    exit;
}

// Fetch mentor data
$mentor = $conn->query("SELECT * FROM mentors WHERE id = $mentorId")->fetch_assoc();
if (!$mentor) {
    die("Mentor not found.");
}

// Fetch divisions for the dropdown
$divisions = $conn->query("SELECT id, nama_divisi FROM divisions");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Mentor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.html'; ?>
    <div class="main-content">
        <h2>Edit Mentor</h2>
        <form method="post">
            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" value="<?= $mentor['nama'] ?>" required>

            <label for="alamat">Alamat:</label>
            <textarea id="alamat" name="alamat" required><?= $mentor['alamat'] ?></textarea>

            <label for="no_hp">No. HP:</label>
            <input type="text" id="no_hp" name="no_hp" value="<?= $mentor['no_hp'] ?>" required>

            <label for="jenis_kelamin">Jenis Kelamin:</label>
            <select id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="Laki-laki" <?= $mentor['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= $mentor['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>

            <label for="divisi_id">Divisi:</label>
            <select id="divisi_id" name="divisi_id" required>
                <?php while ($division = $divisions->fetch_assoc()): ?>
                    <option value="<?= $division['id'] ?>" <?= $division['id'] == $mentor['divisi_id'] ? 'selected' : '' ?>>
                        <?= $division['nama_divisi'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Update Mentor</button>
        </form>
    </div>
</body>
</html>
