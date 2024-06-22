<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'sidebar.html'; ?>
<div class="main-content">
    <h2>Reset Password</h2>
    <form action="" id="main-content">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password">

        <label for="new_password">Ketik Password Baru</label>
        <input type="password" id="new_password" name="new_password" placeholder="Masukkan password baru">

        <label for="confirm_new_password">Ketik Ulang Password Baru</label>
        <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Masukkan ulang password baru">
    </form>
    <button>Reset</button>
</div>
</body>
</html>