<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$filter = '';
if (isset($_GET['nama']) && $_GET['nama'] !== '') {
    $nama = $conn->real_escape_string($_GET['nama']);
    $filter = "WHERE ps.nama LIKE '%$nama%'";
}

$query = $conn->query("
    SELECT ps.nama, p.persiapan_pekerjaan, p.penggunaan_alat, p.penyelesaian_pekerjaan, 
        p.kualitas_pekerjaan, p.rata_teknis, p.disiplin_kerja, p.kerjasama, p.inisiatif, 
        p.tanggung_jawab, p.kebersihan, p.kerapihan, p.rata_non_teknis, 
        p.rata_rata_akhir, p.predikat 
    FROM penilaian p
    JOIN peserta ps ON p.peserta_id = ps.peserta_id
    $filter
    ORDER BY ps.nama ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nilai Lengkap Peserta</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f9f9f9;
        padding: 20px;
    }

    h2 {
        color: #2c3e50;
        margin-bottom: 20px;
        text-align: center;
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
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        font-size: 13px;
    }

    th, td {
        padding: 10px;
        text-align: center;
        border: 1px solid #ccc;
    }

    th {
        background-color: #2980b9;
        color: white;
        text-transform: uppercase;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #eef9ff;
        transition: background-color 0.2s ease-in-out;
    }

    .actions {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 10px 18px;
        font-size: 14px;
        text-decoration: none;
        border-radius: 6px;
        color: white;
        display: inline-block;
    }

    .btn-kembali {
        background-color: #2980b9;
    }

    .btn-pdf {
        background-color: #e74c3c;
    }

    .btn-pdf:hover {
        background-color: #c0392b;
    }

    @media print {
        form, .actions {
            display: none;
        }
        table {
            margin-top: 20px;
        }
    }
    </style>
</head>
<body>

<h2>Daftar Nilai Lengkap Peserta Magang</h2>

<form method="GET">
    <input type="text" name="nama" placeholder="Cari nama peserta..." value="<?= isset($_GET['nama']) ? htmlspecialchars($_GET['nama']) : '' ?>">
    <button type="submit">🔍 Cari</button>
    <button type="button" onclick="window.print()">🖨 Cetak</button>
</form>

<table>
    <thead>
        <tr>
            <th>No</th>
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
    </thead>
    <tbody>
        <?php
        $no = 1;
        if ($query->num_rows > 0):
            while ($row = $query->fetch_assoc()):
        ?>
        <tr>
            <td><?= $no++ ?></td>
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
        <?php endwhile; else: ?>
        <tr>
            <td colspan="16">Tidak ada data ditemukan.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="actions">
    <a class="btn btn-kembali" href="admin_dashboard.php">⬅ Kembali ke Dashboard</a>
</div>

</body>
</html>
