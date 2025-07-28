<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "pertanian_db");

$where = "1";
$lembagaPendidikan = "";

// Filter berdasarkan nama peserta
if (!empty($_GET['nama'])) {
    $nama = $conn->real_escape_string($_GET['nama']);
    $where .= " AND p.nama LIKE '%$nama%'";

    // Ambil lembaga pendidikan jika hanya satu nama ditemukan
    $res = $conn->query("SELECT lembaga_pendidikan FROM peserta WHERE nama LIKE '%$nama%'");
    if ($res && $res->num_rows === 1) {
        $dataPeserta = $res->fetch_assoc();
        $lembagaPendidikan = $dataPeserta['lembaga_pendidikan'];
    }
}

// Filter berdasarkan bulan
if (!empty($_GET['bulan'])) {
    $bulan = $conn->real_escape_string($_GET['bulan']); // format: YYYY-MM
    $where .= " AND DATE_FORMAT(l.tanggal, '%Y-%m') = '$bulan'";
}

$query = "
    SELECT l.*, p.nama 
    FROM laporan l 
    JOIN peserta p ON l.peserta_id = p.peserta_id 
    WHERE $where 
    ORDER BY l.tanggal DESC
";
$data = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Laporan Kegiatan</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #34495e;
        }
        form {
            margin: 20px auto;
            max-width: 600px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        select, input[type="date"], input[type="text"], input[type="month"] {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            background: #27ae60;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        th {
            background: #16a085;
            color: white;
        }
        .print-btn {
            margin-top: 10px;
            text-align: center;
        }
        .print-btn button {
            background: #2980b9;
        }

        .btn-back {
            display: inline-block;
            padding: 8px 15px;
            background-color: #95a5a6;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            background-color: #7f8c8d;
        }

        .kop {
            text-align: center;
            margin-bottom: 20px;
        }

        .kop h3, .kop p {
            margin: 0;
            line-height: 1.4;
        }

        hr {
            border: 2px solid black;
            margin-top: 10px;
        }

        .ttd {
            display: flex;
            justify-content: flex-end;
            margin-top: 60px;
            padding-right: 50px;
        }

        .ttd .isi-ttd {
            text-align: center;
            width: 300px;
        }

        /* Sembunyikan kolom tertentu saat print */
        @media print {
            form, .print-btn, .btn-back {
                display: none;
            }
            .hide-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<a href="admin_dashboard.php" class="btn-back">Kembali</a>

<!-- Kop Surat -->
<div class="kop">
    <h3>DAFTAR RINGKASAN LAPORAN KEGIATAN PESERTA MAGANG</h3>
    <?php if ($lembagaPendidikan): ?>
        <p><?= htmlspecialchars($lembagaPendidikan) ?></p>
    <?php endif; ?>
</div>

<h2>Rekap Laporan Kegiatan Peserta</h2>

<!-- Form pencarian -->
<form method="GET">
    <input type="text" name="nama" placeholder="Cari Nama Peserta" value="<?= htmlspecialchars($_GET['nama'] ?? '') ?>">
    <input type="month" name="bulan" value="<?= htmlspecialchars($_GET['bulan'] ?? '') ?>">
    <button type="submit">Tampilkan</button>
</form>

<div class="print-btn">
    <button onclick="window.print()">🖨️ Cetak</button>
</div>

<!-- Tabel laporan -->
<table>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Tanggal</th>
        <th>Isi Laporan</th>
        <th class="hide-print">Bukti Kegiatan</th>
        <th class="hide-print">Validasi</th>
        <th class="hide-print">Catatan</th>
    </tr>
    <?php $no = 1; while ($row = $data->fetch_assoc()): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['tanggal']) ?></td>
        <td><?= nl2br(htmlspecialchars($row['aktivitas'])) ?></td>
        <td class="hide-print">
            <?php if (!empty($row['bukti_kegiatan'])): ?>
                <a href="uploads/<?= htmlspecialchars($row['bukti_kegiatan']) ?>" target="_blank">Lihat</a>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
        <td class="hide-print"><?= htmlspecialchars($row['validasi'] ?? '-') ?></td>
        <td class="hide-print"><?= nl2br(htmlspecialchars($row['catatan'] ?? '-')) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- Tanda Tangan -->
<div class="ttd">
    <div class="isi-ttd">
        <p>Palembang, <?= date('d F Y') ?></p>
        <p><b>Kepala Subbagian Umum dan Kepegawaian</b></p>
        <br><br><br><br>
        <p><b>Lili Rozali, S.Kom, SP</b><br>
           Penata Tingkat I (III/d)<br>
           NIP : 197806222005011001</p>
    </div>
</div>

</body>
</html>
