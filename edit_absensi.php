<?php
$conn = new mysqli("localhost", "root", "", "pertanian_db");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     // Ambil data dari form
    $peserta_id = intval($_POST['peserta_id']);
    $tanggal = $_POST['tanggal'];
    $datang = $_POST['datang'];
    $pulang = $_POST['pulang'];

    $stmt = $conn->prepare("UPDATE absensi SET datang=?, pulang=? WHERE peserta_id=? AND tanggal=?");
    $stmt->bind_param("ssis", $datang, $pulang, $peserta_id, $tanggal);
    $stmt->execute();
}

header("Location: absensi_admin.php"); // kembali ke halaman absensi admin
exit();
