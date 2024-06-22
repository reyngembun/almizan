<?php
include '../db.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM materials WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: manage_materi.php");
?>
