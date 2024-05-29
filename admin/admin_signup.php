<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "almizan";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $jenis_kelamin = $_POST['jenis_kelamin'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into users table
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $username, $password);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $user_id = $stmt->insert_id;

        // Insert into admins table
        $sql = "INSERT INTO admins (id, nama, alamat, no_hp, jenis_kelamin) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("issss", $user_id, $nama, $alamat, $no_hp, $jenis_kelamin);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Commit transaction
        $conn->commit();

        echo "Admin registered successfully";
    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
