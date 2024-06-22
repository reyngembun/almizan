<?php
session_start();

// Include the database configuration file
include '../db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // Lindungi dari SQL Injection
    $input_username = $conn->real_escape_string($input_username);

    // Ambil data user dari database
    $sql = "SELECT id, username, password, role FROM users WHERE username = '$input_username' AND role = 'siswa'";
    $result = $conn->query($sql);

    // Periksa apakah query berhasil
    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verifikasi password
            if (password_verify($input_password, $row['password'])) {
                // Password cocok, buat session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                header("Location: student_dashboard.php"); // ganti dengan halaman dashboard admin
                exit();
            } else {
                // Password salah
                $error = "Username atau password salah.";
            }
        } else {
            // User tidak ditemukan atau bukan admin
            $error = "Username atau password salah.";
        }
    } else {
        // Query gagal
        $error = "Terjadi kesalahan pada query: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add your custom styles here -->
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

        select,
        input {
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
        <h1 class="text-center">Login Siswa</h1>

        <?php if (isset($success_message)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Form Login Admin -->
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary" name="login">Login</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>

</html>