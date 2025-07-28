<?php
session_start();
include('config.php');

// Pastikan hanya peserta yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'peserta') {
    header("Location: index.php");
    exit();
}

// Ambil ID peserta yang sedang login
$peserta_id = $_SESSION['peserta_id'];

// Ambil nama lembaga dari tabel peserta
$sql_lembaga = "SELECT lembaga_pendidikan FROM peserta WHERE peserta_id = '$peserta_id'";  
$result_lembaga = $conn->query($sql_lembaga);

// Cek apakah query berhasil dan ambil nama lembaga
$lembaga = '';
if ($result_lembaga->num_rows > 0) {
    $row_lembaga = $result_lembaga->fetch_assoc();
    $lembaga = $row_lembaga['lembaga_pendidikan'];
}

// Ambil data laporan berdasarkan peserta_id
$sql = "SELECT * FROM laporan WHERE peserta_id = '$peserta_id' ORDER BY tanggal DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Ringkasan Laporan</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .kop-surat {
            text-align: center;
            margin-bottom: 30px;
        }

        .kop-surat h3 {
            margin: 0;
            font-size: 20px;
        }

        .kop-surat p {
            margin: 4px 0;
            font-size: 16px;
        }

        h2 {
            text-align: center;
            color: #2e8b57;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #2E8B57;
            color: white;
        }

        .button-container {
            margin-top: 20px;
            text-align: center;
        }

        .button-container button {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button-container button:hover {
            background-color: #2e8b57;
        }

        .ttd {
            width: 100%;
            margin-top: 60px;
        }

        .ttd .kanan {
            float: right;
            text-align: center;
            width: 300px;
        }

        .ttd .kanan p {
            margin: 4px 0;
        }

        .clear {
            clear: both;
        }

        @media print {
            .button-container {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- Kop Surat -->
<div class="kop-surat">
    <h3>Daftar Ringkasan Laporan Kegiatan Peserta Magang</h3>
    <p><?= htmlspecialchars($lembaga) ?></p> <!-- Nama lembaga diambil dari database -->
</div>

<h2>Ringkasan Laporan Harian - <?= date('F Y') ?></h2>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Kegiatan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['tanggal']}</td>
                        <td>{$row['aktivitas']}</td>
                      </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='3'>Tidak ada data laporan</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Tanda Tangan -->
<div class="ttd">
    <div class="kanan">
        <p>Palembang, <?= date('d F Y') ?></p> <!-- Tanggal akan otomatis disesuaikan dengan tanggal cetak -->
        <p>Kepala Subbagian Umum dan Kepegawaian</p>
        <br><br><br><br><br>
        <p><strong>Lili Rozali, S.Kom, SP</strong></p>
        <p>Penata Tingkat I (III/d)</p>
        <p>NIP : 197806222005011001</p>
    </div>
    <div class="clear"></div>
</div>

<!-- Button untuk mencetak dan kembali -->
<div class="button-container">
    <button onclick="window.print()">🖨️ Cetak</button>
    <button onclick="window.location.href='peserta_dashboard.php'">Kembali</button>
</div>

</body>
</html>
