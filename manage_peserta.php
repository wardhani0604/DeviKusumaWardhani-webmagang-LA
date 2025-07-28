<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "pertanian_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$keyword = isset($_GET['cari']) ? $conn->real_escape_string($_GET['cari']) : "";
$peserta = !empty($keyword) ?
    $conn->query("SELECT * FROM peserta WHERE nama LIKE '%$keyword%' ORDER BY nama ASC") :
    $conn->query("SELECT * FROM peserta ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Kelola Peserta Magang</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f0f4f8;
        margin: 0;
        padding: 0;
        color: #2c3e50;
    }

    .header {
        background: linear-gradient(90deg, #2ecc71, #27ae60);
        color: white;
        padding: 25px 0;
        text-align: center;
        font-weight: 700;
        font-size: 28px;
    }

    .container {
        max-width: 1200px;
        margin: 30px auto;
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(44, 62, 80, 0.1);
    }

    .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .actions .btn-group a, .search-form button {
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        color: white;
        border: none;
        cursor: pointer;
    }

    .tambah { background: #3498db; }
    .tambah:hover { background: #2980b9; }
    .kembali { background: #95a5a6; }
    .kembali:hover { background: #7f8c8d; }

    .search-form {
        display: flex;
        gap: 10px;
        flex-grow: 1;
    }

    .search-form input[type="text"] {
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #ccc;
        flex: 1;
    }

    .search-form button {
        background-color: #27ae60;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 14px;
    }

    thead {
        background: #2ecc71;
        color: white;
    }

    th, td {
        padding: 10px;
        text-align: center;
        border: 1px solid #ddd;
    }

    .aksi-btn-group {
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    @media (max-width: 768px) {
        .aksi-btn-group {
            flex-direction: column;
            align-items: stretch;
        }
    }

    .btn {
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        color: white;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        min-width: 60px;
        transition: background-color 0.3s ease;
    }

    .edit { background: #f39c12; }
    .edit:hover { background: #d68910; }

    .hapus { background: #e74c3c; }
    .hapus:hover { background: #c0392b; }

    @media (max-width: 768px) {
        .actions {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-form {
            width: 100%;
        }

        .container {
            padding: 20px;
        }
    }

    @media (max-width: 600px) {
        table, thead, tbody, th, td, tr {
            display: block;
        }

        thead tr { display: none; }

        tr {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
            background: #f9fcf9;
        }

        td {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border: none;
            border-bottom: 1px solid #eee;
        }

        td:before {
            content: attr(data-label);
            font-weight: bold;
            color: #27ae60;
        }

        td:last-child {
            border-bottom: none;
        }

        .aksi-btn-group {
            flex-direction: column;
            gap: 8px;
            align-items: stretch;
        }

        .btn {
            width: 100%;
            text-align: center;
        }
    }
</style>
</head>
<body>
<div class="header">Kelola Peserta Magang</div>
<div class="container">
    <div class="actions">
        <div class="btn-group">
            <a href="admin_dashboard.php" class="kembali">← Kembali</a>
            <a href="tambah_peserta.php" class="tambah">+ Tambah Peserta</a>
        </div>
        <form method="GET" class="search-form">
            <input type="text" name="cari" placeholder="Cari nama peserta..." value="<?= htmlspecialchars($keyword) ?>" />
            <button type="submit">🔍 Cari</button>
        </form>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th><th>ID Peserta</th><th>Nama</th><th>Lembaga</th><th>Jurusan</th><th>Username</th><th>Password</th><th>No HP</th><th>Tgl Masuk</th><th>Tgl Keluar</th><th>Durasi</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($peserta->num_rows > 0): $no = 1; while ($row = $peserta->fetch_assoc()): ?>
                <tr>
        <td data-label="No"><?= $no++ ?></td>
        <td data-label="ID Peserta"><?= htmlspecialchars($row['peserta_id']) ?></td>
        <td data-label="Nama"><?= htmlspecialchars($row['nama']) ?></td>
        <td data-label="Lembaga Pendidikan"><?= htmlspecialchars($row['lembaga_pendidikan']) ?></td>
        <td data-label="Jurusan"><?= htmlspecialchars($row['jurusan']) ?></td>
        <td data-label="Username"><?= htmlspecialchars($row['username']) ?></td>
        <td data-label="Password"><?= substr(htmlspecialchars($row['password']), 0, 10) ?>...</td>
        <td data-label="No HP"><?= htmlspecialchars($row['no_hp']) ?></td>
        <td data-label="Tanggal Masuk"><?= htmlspecialchars($row['tanggal_masuk']) ?></td>
        <td data-label="Tanggal Keluar"><?= htmlspecialchars($row['tanggal_keluar']) ?></td>
        <td data-label="Durasi (bulan)"><?= htmlspecialchars($row['durasi_magang']) ?></td>
        <td data-label="Status">
            <?php
                $status = $row['status_validasi'];
                if ($status == 'diterima') {
                    echo "<span style='color:green;font-weight:bold;'>Diterima</span>";
                } elseif ($status == 'ditolak') {
                    echo "<span style='color:red;font-weight:bold;'>Ditolak</span>";
                } else {
                    echo "<span style='color:gray;'>Belum Divalidasi</span>";
                }
            ?>
        </td>
        <td data-label="Aksi">
            <div class="aksi-btn-group">
                <a href="edit_peserta.php?id=<?= urlencode($row['peserta_id']) ?>" class="btn edit">Edit</a>
                <a href="hapus_peserta.php?id=<?= urlencode($row['peserta_id']) ?>" class="btn hapus" onclick="return confirm('Yakin ingin hapus peserta ini?')">Hapus</a>
            </div>
        </td>
    </tr>
<?php endwhile; else: ?>
    <tr><td colspan="13">Tidak ada peserta ditemukan.</td></tr>
<?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
