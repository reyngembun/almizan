<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mentor') {
    header("Location: index.php");
    exit;
}


// Ambil divisi_id dari mentor yang login
$loggedInUserId = $_SESSION['user_id'];
$result = $conn->query("SELECT divisi_id FROM mentors WHERE id = $loggedInUserId");
if (!$result || $result->num_rows === 0) {
    die("Error fetching division for logged-in user.");
}
$loggedInUserDivision = $result->fetch_assoc()['divisi_id'];


$studentId = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $nim = $conn->real_escape_string($_POST['nim']);
    $no_telephone = $conn->real_escape_string($_POST['no_telephone']);
    $alamat = $conn->real_escape_string($_POST['alamat']);
    $jenis_kelamin = $conn->real_escape_string($_POST['jenis_kelamin']);
    $instansi = $conn->real_escape_string($_POST['instansi']);
    $divisi_id = intval($_POST['divisi_id']);

    $conn->query("UPDATE students SET nama='$nama', nim='$nim', no_telephone='$no_telephone', alamat='$alamat', jenis_kelamin='$jenis_kelamin', instansi='$instansi', divisi_id='$divisi_id' WHERE id=$studentId");
    header("Location: manage_siswa.php");
    exit;
}

// Fetch student data
$student = $conn->query("SELECT * FROM students WHERE id = $studentId")->fetch_assoc();
if (!$student) {
    die("Student not found.");
}

// Fetch divisions for the dropdown
$divisions = $conn->query("SELECT id, nama_divisi FROM divisions");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Siswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2>Edit Siswa</h2>
        <form method="post">
            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" value="<?= $student['nama'] ?>" required>

            <label for="nim">NIM:</label>
            <input type="text" id="nim" name="nim" value="<?= $student['nim'] ?>" required>

            <label for="no_telephone">No. Telephone:</label>
            <input type="text" id="no_telephone" name="no_telephone" value="<?= $student['no_telephone'] ?>" required>

            <label for="alamat">Alamat:</label>
            <textarea id="alamat" name="alamat" required><?= $student['alamat'] ?></textarea>

            <label for="jenis_kelamin">Jenis Kelamin:</label>
            <select id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="Laki-laki" <?= $student['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= $student['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>

            <label for="instansi">Instansi:</label>
            <input type="text" id="instansi" name="instansi" value="<?= $student['instansi'] ?>" required>

            <label for="divisi_id">Divisi:</label>
            <select id="divisi_id" name="divisi_id" required>
                <?php while ($division = $divisions->fetch_assoc()): ?>
                    <option value="<?= $division['id'] ?>" <?= $division['id'] == $student['divisi_id'] ? 'selected' : '' ?>>
                        <?= $division['nama_divisi'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Update Siswa</button>
        </form>
    </div>
</body>
</html>
