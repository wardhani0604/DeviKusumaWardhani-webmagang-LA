<?php
session_start();
include('config.php');
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'peserta') {
    header("Location: login.php");
    exit();
}

$peserta_id = $_SESSION['peserta_id'];
$bulanNama = "Semua Bulan"; // Default nama bulan jika tidak dipilih

// Ambil data peserta
$queryPeserta = $conn->query("SELECT * FROM peserta WHERE peserta_id = '$peserta_id'");
$dataPeserta = $queryPeserta->fetch_assoc();
$lembaga = $dataPeserta['lembaga_pendidikan'];

$filter = ""; // Filter untuk query SQL berdasarkan bulan (jika dipilih)
if (isset($_GET['bulan']) && !empty($_GET['bulan'])) {
    $bulan = $_GET['bulan']; // format YYYY-MM
    $year = substr($bulan, 0, 4);
    $month = substr($bulan, 5, 2);
    $bulanNama = date('F Y', strtotime("$year-$month-01"));
    $filter = "AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'";
}

$query = $conn->query("SELECT * FROM absensi WHERE peserta_id = '$peserta_id' $filter ORDER BY tanggal ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Absensi Bulanan</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 40px;
            background-color: #fff;
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
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #000;
            text-align: center;
        }

        th {
            background-color: #2e8b57;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .btn-wrapper {
            margin-top: 30px;
            text-align: center;
        }

        .btn-wrapper button {
            background-color: #27ae60;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-back {
            background-color: #f39c12;
        }

        @media print {
            .btn-wrapper, form {
                display: none !important;
            }
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
    </style>
</head>
<body>

<div class="kop-surat">
    <h3>Daftar Ringkasan Absensi Peserta Magang</h3>
    <p><strong><?= htmlspecialchars($lembaga) ?></strong></p>
</div>

<h2>Ringkasan Absensi Bulanan - <?= htmlspecialchars($bulanNama) ?></h2>

<form method="GET" style="text-align: center;">
    <label for="bulan">Pilih Bulan: </label>
    <input type="month" name="bulan" id="bulan" value="<?= isset($_GET['bulan']) ? $_GET['bulan'] : '' ?>">
    <button type="submit">Tampilkan</button>
</form>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Datang</th>
            <th>Pulang</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $jumlahHadir = 0;
        $jumlahIzin = 0;
        $jumlahSakit = 0;

        if ($query->num_rows > 0) {
            $no = 1;
            while ($row = $query->fetch_assoc()) {
                $tanggalFormatted = date('d-m-Y', strtotime($row['tanggal']));
                $datang = $row['datang'] ? date('H:i', strtotime($row['datang'])) : '-';
                $pulang = $row['pulang'] ? date('H:i', strtotime($row['pulang'])) : '-';
                $status = $row['status'] ?? '-';

                // Hitung jumlah status
                if ($status === 'Hadir') $jumlahHadir++;
                elseif ($status === 'Izin') $jumlahIzin++;
                elseif ($status === 'Sakit') $jumlahSakit++;

                echo "<tr>
                        <td>$no</td>
                        <td>$tanggalFormatted</td>
                        <td>$datang</td>
                        <td>$pulang</td>
                        <td>$status</td>
                    </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='5'>Tidak ada data</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Rekap tulisan di bawah tabel -->
<p><strong>Jumlah Hadir:</strong> <?= $jumlahHadir ?></p>
<p><strong>Jumlah Izin:</strong> <?= $jumlahIzin ?></p>
<p><strong>Jumlah Sakit:</strong> <?= $jumlahSakit ?></p>

<div class="ttd">
    <div class="kanan">
        <p>Palembang, <?= date('d F Y') ?></p>
        <p>Kepala Subbagian Umum dan Kepegawaian</p>
        <br><br><br><br><br>
        <p><strong>Lili Rozali, S.Kom, SP</strong></p>
        <p>Penata Tingkat I (III/d)</p>
        <p>NIP : 197806222005011001</p>
    </div>
    <div class="clear"></div>
</div>

<div class="btn-wrapper">
    <button onclick="window.print()">🖨️ Cetak</button>
    <button onclick="window.location.href='peserta_dashboard.php'" class="btn-back">Kembali</button>
</div>

</body>
</html>
