<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$conn = new mysqli("localhost", "root", "", "pertanian_db");

// Proses filter
$where = "1";
if (!empty($_GET['nama'])) {
    $nama = $conn->real_escape_string($_GET['nama']);
    $where .= " AND p.nama LIKE '%$nama%'";
}
if (!empty($_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'];
    $where .= " AND a.tanggal = '$tanggal'";
}

// Ambil data absensi
$query = "
    SELECT a.*, p.nama 
    FROM absensi a 
    JOIN peserta p ON a.peserta_id = p.peserta_id 
    WHERE $where 
    ORDER BY a.tanggal DESC
";
$data = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi Peserta</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        form.filter {
            margin: 20px auto;
            max-width: 600px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        input[type="text"], input[type="date"] {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            flex: 1 1 150px;
        }
        button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            background: #2ecc71;
            color: white;
            cursor: pointer;
        }
        .btn-back, .btn-rekap-bulanan {
            display: inline-block;
            padding: 8px 15px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .btn-back {
            background-color: #95a5a6;
        }
        .btn-back:hover {
            background-color: #7f8c8d;
        }
        .btn-rekap-bulanan {
            background-color: #2980b9;
            margin-left: 10px;
        }
        .btn-rekap-bulanan:hover {
            background-color: #3498db;
        }
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            min-width: 700px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #27ae60;
            color: white;
        }
        input[type="time"] {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        @media print {
            form.filter, .btn-back, .btn-rekap-bulanan, .aksi-col {
                display: none;
            }
            table {
                margin-top: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<a href="admin_dashboard.php" class="btn-back">Kembali</a>
<a href="rekap_bulanan.php" class="btn-rekap-bulanan">Rekap Bulanan</a>

<h2>Rekap Data Absensi Peserta</h2>

<form method="GET" class="filter">
    <input type="text" name="nama" placeholder="Cari nama peserta" value="<?= $_GET['nama'] ?? '' ?>">
    <input type="date" name="tanggal" value="<?= $_GET['tanggal'] ?? '' ?>">
    <button type="submit">Tampilkan</button>
</form>

<div class="table-responsive">
    <table>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Tanggal</th>
            <th>Datang</th>
            <th>Pulang</th>
            <th class="aksi-col">Aksi</th>
        </tr>
        <?php $no = 1; while ($row = $data->fetch_assoc()): ?>
        <tr>
            <form method="POST" action="edit_absensi.php">
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td>
                    <?= date('d-m-Y', strtotime($row['tanggal'])) ?>
                    <input type="hidden" name="tanggal" value="<?= $row['tanggal'] ?>">
                    <input type="hidden" name="peserta_id" value="<?= $row['peserta_id'] ?>">
                </td>
                <td><input type="time" name="datang" value="<?= $row['datang'] ?>"></td>
                <td><input type="time" name="pulang" value="<?= $row['pulang'] ?>"></td>
                <td class="aksi-col">
                    <button type="submit">Simpan</button>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
