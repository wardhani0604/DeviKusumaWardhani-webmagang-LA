<?php
session_start();
include 'config.php';

// Cek apakah user adalah HRD
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hrd') {
    header("Location: index.php");
    exit();
}

// Proses simpan validasi jika ada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laporan_id = $_POST['laporan_id'];
    $validasi = $_POST['validasi'];
    $catatan = $_POST['catatan'];

    // Siapkan query untuk update validasi laporan
    $stmt = $conn->prepare("UPDATE laporan SET validasi = ?, catatan = ? WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssi", $validasi, $catatan, $laporan_id);
    $stmt->execute();
    $stmt->close();

    // Kembali ke halaman setelah update
    header("Location: validasi_laporan.php");
    exit();
}

// Ambil data laporan dan nama peserta
$result = $conn->query("
    SELECT l.*, p.nama 
    FROM laporan l 
    JOIN peserta p ON l.peserta_id = p.peserta_id 
    ORDER BY l.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Laporan Peserta</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f3;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #2ecc71;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #2ecc71;
            color: white;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        textarea, select, button {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            width: 100%;
        }
        button {
            background-color: #27ae60;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #219150;
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        .back-link {
            margin-top: 30px;
            text-align: center;
        }
        .back-link a {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }
        .back-link a:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>

<h2>Validasi Laporan Peserta Magang</h2>

<table>
    <tr>
        <th>Nama</th>
        <th>Tanggal</th>
        <th>Aktivitas</th>
        <th>Bukti</th>
        <th>Validasi</th>
        <th>Catatan</th>
        <th>Aksi</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <form method="POST">
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['aktivitas'])) ?></td>
            <td>
                <?php if (!empty($row['bukti_kegiatan'])): ?>
                    <a href="uploads/<?= htmlspecialchars($row['bukti_kegiatan']) ?>" target="_blank">Lihat</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td>
                <select name="validasi" required>
                    <option value="">-- Pilih --</option>
                    <option value="Valid" <?= $row['validasi'] == 'Valid' ? 'selected' : '' ?>>Valid</option>
                    <option value="Revisi" <?= $row['validasi'] == 'Revisi' ? 'selected' : '' ?>>Revisi</option>
                </select>
            </td>
            <td>
                <textarea name="catatan"><?= htmlspecialchars($row['catatan'] ?? '') ?></textarea>
            </td>
            <td>
                <input type="hidden" name="laporan_id" value="<?= $row['id'] ?>">
                <button type="submit">Simpan</button>
            </td>
        </form>
    </tr>
    <?php endwhile; ?>
</table>

<div class="back-link">
    <a href="hrd_dashboard.php">← Kembali ke Dashboard</a>
</div>

</body>
</html>
