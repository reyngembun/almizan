<?php
include '../db.php';

// Handle filtering
$divisionFilter = '';
if (isset($_GET['division_id']) && $_GET['division_id'] != 'all') {
    $divisionFilter = ' AND students.divisi_id = ' . $_GET['division_id'];
}

// Fetch divisions for the filter dropdown
$divisions = $conn->query("SELECT id, nama_divisi FROM divisions");

// Fetch students data
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
    <?php include 'sidebar.html'; ?>
    <div class="main-content">
        <h2>Manage Siswa</h2>
        <form method="get" class="filter-form">
            <label for="division_id">Filter Divisi:</label>
            <select name="division_id" id="division_id">
                <option value="all">All</option>
                <?php while ($division = $divisions->fetch_assoc()): ?>
                    <option value="<?= $division['id'] ?>" <?= isset($_GET['division_id']) && $_GET['division_id'] == $division['id'] ? 'selected' : '' ?>>
                        <?= $division['nama_divisi'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Filter</button>
        </form>
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
                            <a href="delete_siswa.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="siswa_signup.php" class="create-user">Create Siswa</a>
    </div>
</body>
</html>
