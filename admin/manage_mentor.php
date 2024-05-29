<?php
include '../db.php';

// Handle filtering
$divisionFilter = '';
if (isset($_GET['division_id']) && $_GET['division_id'] != 'all') {
    $divisionFilter = ' AND mentors.divisi_id = ' . $_GET['division_id'];
}

// Fetch divisions for the filter dropdown
$divisions = $conn->query("SELECT id, nama_divisi FROM divisions");

// Fetch mentors data
$mentors = $conn->query("
    SELECT 
        users.id, 
        users.username, 
        users.role, 
        mentors.nama, 
        mentors.alamat, 
        mentors.no_hp, 
        mentors.jenis_kelamin, 
        divisions.nama_divisi
    FROM users
    JOIN mentors ON users.id = mentors.id
    JOIN divisions ON mentors.divisi_id = divisions.id
    WHERE users.role = 'mentor' $divisionFilter
");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Mentor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.html'; ?>
    <div class="main-content">
        <h2>Manage Mentor</h2>
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
                    <th>Alamat</th>
                    <th>No. HP</th>
                    <th>Jenis Kelamin</th>
                    <th>Divisi</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $mentors->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['alamat'] ?></td>
                        <td><?= $row['no_hp'] ?></td>
                        <td><?= $row['jenis_kelamin'] ?></td>
                        <td><?= $row['nama_divisi'] ?></td>
                        <td>
                            <a href="edit_mentor.php?id=<?= $row['id'] ?>">Edit</a>
                            <a href="delete_mentor.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this mentor?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="mentor_signup.php" class="create-user">Create Mentor</a>
    </div>
</body>
</html>
