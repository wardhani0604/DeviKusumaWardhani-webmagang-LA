<?php
session_start();
include 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'peserta' || !isset($_SESSION['peserta_id'])) {
    header("Location: index.php");
    exit();
}

$peserta_id = $_SESSION['peserta_id'];
$notif = "";

// Proses kirim laporan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $aktivitas = $_POST['aktivitas'];
    $status = $_POST['status'];
    $buktiFile = "";

    // Proses upload file
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === 0) {
        $folder = "uploads/";
        $namaFile = time() . '_' . basename($_FILES['bukti']['name']);
        $target = $folder . $namaFile;
        $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'docx'];

        if (in_array($ext, $allowed)) {
            move_uploaded_file($_FILES['bukti']['tmp_name'], $target);
            $buktiFile = $namaFile;
        }
    }

    // Validasi peserta
    $cek = $conn->prepare("SELECT 1 FROM peserta WHERE peserta_id = ?");
    $cek->bind_param("s", $peserta_id);
    $cek->execute();
    $cek_result = $cek->get_result();

    if ($cek_result->num_rows === 0) {
        $notif = "<div class='notif error'>Peserta tidak valid.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO laporan (peserta_id, tanggal, aktivitas, status, bukti_kegiatan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $peserta_id, $tanggal, $aktivitas, $status, $buktiFile);

        if ($stmt->execute()) {
            $notif = "<div class='notif success'>Laporan berhasil disimpan.</div>";
        } else {
            $notif = "<div class='notif error'>Gagal menyimpan laporan: " . $stmt->error . "</div>";
        }
    }
}

// Ambil data laporan
$result = $conn->prepare("SELECT * FROM laporan WHERE peserta_id = ? ORDER BY tanggal DESC");
$result->bind_param("s", $peserta_id);
$result->execute();
$data = $result->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #a1ffce, #faffd1);
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 960px;
            background: #ffffff;
            border-radius: 24px;
            padding: 40px;
            margin: auto;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        form { margin-top: 30px; }
        label {
            font-weight: 600;
            margin-top: 16px;
            display: block;
            color: #34495e;
        }
        input[type="date"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            border-radius: 14px;
            border: 1px solid #ccc;
            font-size: 16px;
            background: #f4f6f9;
        }
        textarea { resize: vertical; }
        button {
            background: #10b981;
            color: white;
            padding: 14px 24px;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            margin-top: 24px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease-in-out;
        }
        button:hover {
            background: #059669;
            transform: scale(1.03);
        }
        .notif {
            padding: 16px;
            margin-top: 24px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
        }
        .notif.success { background-color: #d1fae5; color: #065f46; }
        .notif.error { background-color: #fee2e2; color: #991b1b; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }
        th, td {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        th {
            background-color: #10b981;
            color: white;
        }
        .kembali {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #6b7280;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 14px;
            margin-top: 30px;
        }
        .kembali:hover { background-color: #4b5563; }
        @media (max-width: 600px) {
            .container { padding: 24px; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Laporan Harian Peserta Magang</h2>

    <?= $notif ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="tanggal">Tanggal</label>
        <input type="date" name="tanggal" required>

        <label for="aktivitas">Aktivitas</label>
        <textarea name="aktivitas" rows="4" required></textarea>

        <label for="status">Status</label>
        <select name="status" required>
            <option value="">-- Pilih Status --</option>
            <option value="Selesai">Selesai</option>
            <option value="Dalam Proses">Dalam Proses</option>
        </select>

        <label for="bukti">Bukti Kegiatan</label>
        <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf,.docx">

        <button type="submit"><i data-lucide="send"></i> Simpan Laporan</button>
    </form>

    <h3>Riwayat Laporan</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Aktivitas</th>
                <th>Status</th>
                <th>Bukti</th>
                <th>Validasi</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($data->num_rows > 0) {
                while ($laporan = $data->fetch_assoc()) {
                    echo "<tr>
                            <td>{$laporan['tanggal']}</td>
                            <td>{$laporan['aktivitas']}</td>
                            <td>{$laporan['status']}</td>
                            <td>";
                    if (!empty($laporan['bukti_kegiatan'])) {
                        echo "<a href='uploads/{$laporan['bukti_kegiatan']}' target='_blank'>Lihat</a>";
                    } else {
                        echo "-";
                    }
                    echo "</td>
                          <td>" . htmlspecialchars($laporan['validasi'] ?? '-') . "</td>
                          <td>" . nl2br(htmlspecialchars($laporan['catatan'] ?? '-')) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Belum ada laporan.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="peserta_dashboard.php" class="kembali"><i data-lucide='arrow-left'></i> Kembali</a>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>
