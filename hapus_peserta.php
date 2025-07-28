<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID peserta tidak valid.";
    exit();
}

$peserta_id = $_GET['id']; 

$conn = new mysqli("localhost", "root", "", "pertanian_db");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$stmt = $conn->prepare("DELETE FROM peserta WHERE peserta_id = ?");
$stmt->bind_param("s", $peserta_id); 
if ($stmt->execute()) {
    header("Location: manage_peserta.php");
    exit(); // Jika berhasil, kembali ke halaman daftar peserta
} else {
    echo "Gagal menghapus data.";
} // Jika gagal

$stmt->close();
$conn->close();
?>
