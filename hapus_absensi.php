<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "pertanian_db");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $peserta_id = $_POST['peserta_id'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';

    if ($peserta_id && $tanggal) {
        // Lakukan penghapusan berdasarkan peserta_id dan tanggal
        $stmt = $conn->prepare("DELETE FROM absensi WHERE peserta_id = ? AND tanggal = ?");
        $stmt->bind_param("ss", $peserta_id, $tanggal);
// Redirect jika berhasil
        if ($stmt->execute()) {
            header("Location: absensi_admin.php?msg=hapus_berhasil");
            exit();
        } else {
            echo "Gagal menghapus data.";
        }
    } else {
        echo "Data tidak lengkap.";
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>
