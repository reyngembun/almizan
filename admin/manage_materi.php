<?php
include '../db.php';

// Handle filtering
$divisionFilter = '';
if (isset($_GET['division_id']) && $_GET['division_id'] != 'all') {
    $divisionFilter = ' WHERE materials.divisi_id = ' . $_GET['division_id'];
}

// Fetch divisions for the filter dropdown
$divisionsResult = $conn->query("SELECT id, nama_divisi FROM divisions");
if (!$divisionsResult) {
    die("Error fetching divisions: " . $conn->error);
}

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
    $divisionFilter
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
    <?php include 'sidebar.html'; ?>
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
                <label for="divisi">Divisi:</label>
                <select name="divisi_id" id="divisi" required>
                    <?php while ($division = $divisionsResult->fetch_assoc()): ?>
                        <option value="<?= $division['id'] ?>"><?= $division['nama_divisi'] ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Upload</button>
            </form>
        </div>
        <div class="filter-form">
            <form method="get">
                <label for="division_id">Filter Divisi:</label>
                <select name="division_id" id="division_id">
                    <option value="all">All</option>
                    <?php 
                    // Reset the result pointer and fetch divisions again
                    $divisionsResult->data_seek(0);
                    while ($division = $divisionsResult->fetch_assoc()): ?>
                        <option value="<?= $division['id'] ?>" <?= isset($_GET['division_id']) && $_GET['division_id'] == $division['id'] ? 'selected' : '' ?>>
                            <?= $division['nama_divisi'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Filter</button>
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

    <script>
        function applyFilter() {
            const division = document.getElementById('filter-division').value;
            const sort = document.getElementById('filter-sort').value;
            const materialItems = document.querySelectorAll('.material-item');

            // Filter by division
            materialItems.forEach(item => {
                const itemDivision = item.getAttribute('data-division');
                if (division !== 'all' && itemDivision !== division) {
                    item.style.display = 'none';
                } else {
                    item.style.display = 'block';
                }
            });

            // Sort by created_at
            const sortedItems = Array.from(materialItems).sort((a, b) => {
                const timeA = new Date(a.getAttribute('data-created-at'));
                const timeB = new Date(b.getAttribute('data-created-at'));
                return sort === 'newest' ? timeB - timeA : timeA - timeB;
            });

            const materialList = document.getElementById('material-list');
            materialList.innerHTML = '';
            sortedItems.forEach(item => materialList.appendChild(item));
        }
    </script>
</body>
</html>
