<?php
session_start();
include('config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'peserta') {
    header("Location: index.php");
    exit();
}

$peserta_id = $_SESSION['peserta_id'];

$query = "SELECT * FROM penilaian WHERE peserta_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $peserta_id);
$stmt->execute();
$result = $stmt->get_result();

$nilai = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Penilaian</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        table th {
            background-color: #f0f0f0;
        }
        .back-button {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            color: white;
            background-color: #4caf50;
            padding: 10px 20px;
            border-radius: 6px;
        }
        .section-title {
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Hasil Penilaian Peserta</h2>

    <?php if ($nilai): ?>
    <div class="section-title">Penilaian Teknis</div>
    <table>
        <tr>
            <th>Persiapan Pekerjaan</th>
            <th>Penggunaan Alat</th>
            <th>Penyelesaian Pekerjaan</th>
            <th>Kualitas Pekerjaan</th>
            <th>Rata-rata Teknis</th>
        </tr>
        <tr>
            <td><?= $nilai['persiapan_pekerjaan'] ?></td>
            <td><?= $nilai['penggunaan_alat'] ?></td>
            <td><?= $nilai['penyelesaian_pekerjaan'] ?></td>
            <td><?= $nilai['kualitas_pekerjaan'] ?></td>
            <td><?= $nilai['rata_teknis'] ?></td>
        </tr>
    </table>

    <div class="section-title">Penilaian Non-Teknis</div>
    <table>
        <tr>
            <th>Disiplin Kerja</th>
            <th>Kerja Sama</th>
            <th>Inisiatif</th>
            <th>Tanggung Jawab</th>
            <th>Kebersihan</th>
            <th>Kerapihan</th>
            <th>Rata-rata Non-Teknis</th>
        </tr>
        <tr>
            <td><?= $nilai['disiplin_kerja'] ?></td>
            <td><?= $nilai['kerjasama'] ?></td>
            <td><?= $nilai['inisiatif'] ?></td>
            <td><?= $nilai['tanggung_jawab'] ?></td>
            <td><?= $nilai['kebersihan'] ?></td>
            <td><?= $nilai['kerapihan'] ?></td>
            <td><?= $nilai['rata_non_teknis'] ?></td>
        </tr>
    </table>

    <div class="section-title">Total & Predikat</div>
    <table>
        <tr>
            <th>Rata-rata Akhir</th>
            <th>Predikat</th>
        </tr>
        <tr>
            <td><?= $nilai['rata_rata_akhir'] ?></td>
            <td><?= $nilai['predikat'] ?></td>
        </tr>
    </table>

    <?php else: ?>
        <p style="text-align:center; color: red;">Belum ada data penilaian untuk Anda.</p>
    <?php endif; ?>

    <a class="back-button" href="peserta_dashboard.php">Kembali ke Dashboard</a>
</div>

</body>
</html>
