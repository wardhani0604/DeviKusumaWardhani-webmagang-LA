<?php
session_start();
include 'config.php';

// Hanya HRD
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hrd') {
    header("Location: index.php");
    exit();
}

$filter = '';
if (isset($_GET['nama']) && $_GET['nama'] !== '') {
    $nama = $conn->real_escape_string($_GET['nama']);
    $filter = "WHERE ps.nama LIKE '%$nama%'";
}

$query = "SELECT ps.nama, p.persiapan_pekerjaan, p.penggunaan_alat, p.penyelesaian_pekerjaan, 
    p.kualitas_pekerjaan, p.rata_teknis, p.disiplin_kerja, p.kerjasama, p.inisiatif, 
    p.tanggung_jawab, p.kebersihan, p.kerapihan, p.rata_non_teknis, 
    p.rata_rata_akhir, p.predikat 
    FROM penilaian p
    JOIN peserta ps ON p.peserta_id = ps.peserta_id $filter";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Penilaian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: white;
            color: #000;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 6px 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 250px;
        }

        button {
            padding: 7px 14px;
            margin-left: 5px;
            font-size: 14px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            overflow: hidden;
            font-size: 13px;
        }

        th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px;
            border: 1px solid #ccc;
        }

        td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #eef9ff;
            transition: background-color 0.2s ease-in-out;
        }

        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
        }

        @media print {
            button, .back-btn, form {
                display: none;
            }
        }

        .back-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>
<body>

<h2>Rekapitulasi Penilaian Peserta Magang</h2>

<form method="GET">
    <input type="text" name="nama" placeholder="Cari nama peserta..." value="<?= isset($_GET['nama']) ? htmlspecialchars($_GET['nama']) : '' ?>">
    <button type="submit">🔍 Cari</button>
    <button type="button" onclick="window.print()">🖨 Cetak</button>
</form>

<table>
    <tr>
        <th>Nama</th>
        <th>Persiapan</th>
        <th>Alat</th>
        <th>Penyelesaian</th>
        <th>Kualitas</th>
        <th>Rata Teknis</th>
        <th>Disiplin</th>
        <th>Kerjasama</th>
        <th>Inisiatif</th>
        <th>Tanggung Jawab</th>
        <th>Kebersihan</th>
        <th>Kerapihan</th>
        <th>Rata Non-Teknis</th>
        <th>Rata Akhir</th>
        <th>Predikat</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= $row['persiapan_pekerjaan'] ?></td>
            <td><?= $row['penggunaan_alat'] ?></td>
            <td><?= $row['penyelesaian_pekerjaan'] ?></td>
            <td><?= $row['kualitas_pekerjaan'] ?></td>
            <td><?= $row['rata_teknis'] ?></td>
            <td><?= $row['disiplin_kerja'] ?></td>
            <td><?= $row['kerjasama'] ?></td>
            <td><?= $row['inisiatif'] ?></td>
            <td><?= $row['tanggung_jawab'] ?></td>
            <td><?= $row['kebersihan'] ?></td>
            <td><?= $row['kerapihan'] ?></td>
            <td><?= $row['rata_non_teknis'] ?></td>
            <td><?= $row['rata_rata_akhir'] ?></td>
            <td><?= $row['predikat'] ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="15" class="no-data">Tidak ada data ditemukan.</td></tr>
    <?php endif; ?>
</table>

<div style="text-align: center;">
    <a href="penilaian.php" class="back-btn">⬅ Kembali</a>
</div>

</body>
</html>
