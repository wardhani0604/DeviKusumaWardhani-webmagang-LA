<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hrd') {
    header("Location: index.php");
    exit();
}

// Tambah penilaian
if (isset($_POST['tambah'])) {
    $peserta_id = $_POST['peserta_id'];
    $persiapan = $_POST['persiapan_pekerjaan'];
    $alat = $_POST['penggunaan_alat'];
    $penyelesaian = $_POST['penyelesaian_pekerjaan'];
    $kualitas = $_POST['kualitas_pekerjaan'];
    $disiplin = $_POST['disiplin_kerja'];
    $kerjasama = $_POST['kerjasama'];
    $inisiatif = $_POST['inisiatif'];
    $tanggungjawab = $_POST['tanggung_jawab'];
    $kebersihan = $_POST['kebersihan'];
    $kerapihan = $_POST['kerapihan'];

    $rata_teknis = round(($persiapan + $alat + $penyelesaian + $kualitas) / 4);
    $rata_non_teknis = round(($disiplin + $kerjasama + $inisiatif + $tanggungjawab + $kebersihan + $kerapihan) / 6);
    $rata_akhir = round(($rata_teknis + $rata_non_teknis) / 2);

    // Predikat otomatis
    if ($rata_akhir >= 85) {
        $predikat = 'Sangat Baik';
    } elseif ($rata_akhir >= 70) {
        $predikat = 'Baik';
    } elseif ($rata_akhir >= 60) {
        $predikat = 'Cukup';
    } else {
        $predikat = 'Kurang';
    }

    $stmt = $conn->prepare("INSERT INTO penilaian 
        (peserta_id, persiapan_pekerjaan, penggunaan_alat, penyelesaian_pekerjaan, kualitas_pekerjaan,
         rata_teknis, disiplin_kerja, kerjasama, inisiatif, tanggung_jawab, kebersihan, kerapihan,
         rata_non_teknis, rata_rata_akhir, predikat)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("siiiiiiiiiiiiis", $peserta_id, $persiapan, $alat, $penyelesaian, $kualitas,
        $rata_teknis, $disiplin, $kerjasama, $inisiatif, $tanggungjawab,
        $kebersihan, $kerapihan, $rata_non_teknis, $rata_akhir, $predikat);

    $stmt->execute();
    header("Location: penilaian.php");
    exit();
}

// Ambil data peserta
$peserta = $conn->query("SELECT * FROM peserta ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penilaian Peserta</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f9f9f9;
        padding: 20px;
    }

    h2 {
        color: #2c3e50;
        margin-bottom: 15px;
    }

    .form-actions {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-start;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-kembali,
    button[name="tambah"],
    a.btn-pdf {
        padding: 8px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
    }

    .btn-kembali {
        background-color: #2980b9;
        color: white;
    }

    button[name="tambah"] {
        background-color: #27ae60;
        color: white;
    }

    a.btn-pdf {
        background-color: #e74c3c;
        color: white;
        transition: background-color 0.3s ease;
    }

    a.btn-pdf:hover {
        background-color: #c0392b;
    }

    form {
        background: #fff;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    form label {
        font-weight: 500;
        margin-bottom: 5px;
        display: block;
    }

    form input,
    form select {
        padding: 8px;
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
    }
    </style>
</head>
<body>

<h2>Form Tambah Penilaian</h2>
<form method="POST">
    <div>
        <label>Peserta:</label>
        <select name="peserta_id" required>
            <option value="">-- Pilih Peserta --</option>
            <?php while ($p = $peserta->fetch_assoc()): ?>
                <option value="<?= $p['peserta_id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div><label>Persiapan Pekerjaan:</label><input type="number" name="persiapan_pekerjaan" min="0" max="100" required></div>
    <div><label>Penggunaan Alat:</label><input type="number" name="penggunaan_alat" min="0" max="100" required></div>
    <div><label>Penyelesaian Pekerjaan:</label><input type="number" name="penyelesaian_pekerjaan" min="0" max="100" required></div>
    <div><label>Kualitas Pekerjaan:</label><input type="number" name="kualitas_pekerjaan" min="0" max="100" required></div>
    <div><label>Disiplin Kerja:</label><input type="number" name="disiplin_kerja" min="0" max="100" required></div>
    <div><label>Kerjasama:</label><input type="number" name="kerjasama" min="0" max="100" required></div>
    <div><label>Inisiatif:</label><input type="number" name="inisiatif" min="0" max="100" required></div>
    <div><label>Tanggung Jawab:</label><input type="number" name="tanggung_jawab" min="0" max="100" required></div>
    <div><label>Kebersihan:</label><input type="number" name="kebersihan" min="0" max="100" required></div>
    <div><label>Kerapihan:</label><input type="number" name="kerapihan" min="0" max="100" required></div>

    <div class="form-actions">
        <button type="submit" name="tambah">Simpan</button>
        <a class="btn-kembali" href="hrd_dashboard.php">Kembali ke Dashboard</a>
        <a class="btn btn-pdf" href="penilaian_rekap.php" target="_blank">🖨 Rekap Nilai</a>
    </div>
</form>

</body>
</html>
