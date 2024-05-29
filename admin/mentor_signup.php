<?php
session_start();

// Fungsi untuk memeriksa apakah pengguna sudah login
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Jika pengguna belum login, redirect ke halaman login
if (!isUserLoggedIn()) {
    header("Location: ../login.html");
    exit();
}
include("../db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $divisi_id = $_POST['divisi_id'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

    // Insert into users table
    $stmt_user = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'mentor')");
    $stmt_user->bind_param("ss", $username, $password);

    if ($stmt_user->execute()) {
        $user_id = $stmt_user->insert_id;

        // Insert into mentors table
        $stmt_mentor = $conn->prepare("INSERT INTO mentors (id, nama, alamat, no_hp, divisi_id, jenis_kelamin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_mentor->bind_param("isssis", $user_id, $nama, $alamat, $no_hp, $divisi_id, $jenis_kelamin);

        if ($stmt_mentor->execute()) {
            $success_message = "Mentor registered successfully!";
        } else {
            $error_message = "Error: " . $stmt_mentor->error;
        }
        
        // Tutup statement
        $stmt_mentor->close();
    } else {
        $error_message = "Error: " . $stmt_user->error;
    }

    // Tutup statement
    $stmt_user->close();
}



$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Mentor</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        h1 {
            color: #007bff;
        }

        form {
            max-width: 400px;
            margin: auto;
        }

        label {
            margin-top: 10px;
        }

        select, input {
            margin-bottom: 15px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center">Create Mentor</h1>

    <?php if (isset($success_message)) : ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)) : ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <!-- Form Sign Up mentor -->
    <form method="post" action="">
        <div class="form-group">
            <label for="nama">Nama:</label>
            <input type="text" class="form-control" name="nama" required>
        </div>

        <div class="form-group">
            <label for="nim">Alamat:</label>
            <input type="text" class="form-control" name="alamat" required>
        </div>

        <div class="form-group">
            <label for="no_telephone">No Telephone:</label>
            <input type="text" class="form-control" name="no_hp" required>
        </div>
        <div class="form-group">
            <label for="divisi_id">Divisi:</label>
            <select class="form-control" name="divisi_id" required>
                <option value="1">Tilawah</option>
                <option value="2">Tahfizh</option>
                <option value="3">Tafsir</option>
                <option value="4">Kaligrafi</option>
                <option value="5">Sholawat</option>
            </select>
        </div>

        <div class="form-group">
            <label for="jenis_kelamin">Jenis Kelamin:</label>
            <select class="form-control" name="jenis_kelamin" required>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <p class="text-center"><a href="manage_mentor.php">Back</a></p>
        <button type="submit" class="btn btn-primary" name="signup">Sign Up</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>
</html>
