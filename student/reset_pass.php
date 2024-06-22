<?php
session_start();
include '../db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    if ($role != 'mentor' && $role != 'siswa') {
        $_SESSION['message'] = 'Unauthorized access.';
        $_SESSION['message_type'] = 'error';
        header("Location: reset_pass.php");
        exit();
    }

    // Get the form data
    $password = $_POST['password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Check if the new passwords match
    if ($new_password != $confirm_new_password) {
        $_SESSION['message'] = 'New passwords do not match.';
        $_SESSION['message_type'] = 'error';
        header("Location: reset_pass.php");
        exit();
    }

    // Check the current password
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($current_password);
    $stmt->fetch();

    // Verify the current password
    if (!password_verify($password, $current_password)) {
        $_SESSION['message'] = 'Current password is incorrect.';
        $_SESSION['message_type'] = 'error';
        header("Location: reset_pass.php");
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database
    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashed_password, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Password has been successfully reset.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error resetting password: ' . $conn->error;
        $_SESSION['message_type'] = 'error';
    }

    // Close connections
    $stmt->close();
    $conn->close();

    header("Location: reset_pass.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <h2>Reset Password</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message <?= $_SESSION['message_type'] ?>">
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>
    <form action="reset_pass.php" method="POST" id="main-content">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password" required>

        <label for="new_password">Ketik Password Baru</label>
        <input type="password" id="new_password" name="new_password" placeholder="Masukkan password baru" required>

        <label for="confirm_new_password">Ketik Ulang Password Baru</label>
        <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Masukkan ulang password baru" required>

        <button type="submit">Reset</button>
    </form>
</div>
</body>
</html>
