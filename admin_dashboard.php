<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$conn = new mysqli("localhost", "root", "", "pertanian_db");

$totalPesertaQuery = "SELECT COUNT(*) AS total FROM peserta";
$totalPesertaResult = $conn->query($totalPesertaQuery);
$totalPeserta = $totalPesertaResult->fetch_assoc()['total'];

$tanggalHariIni = date('Y-m-d');
$absensiHariIniQuery = "SELECT COUNT(*) AS total FROM absensi WHERE tanggal = '$tanggalHariIni'";
$absensiHariIniResult = $conn->query($absensiHariIniQuery);
$absensiHariIni = $absensiHariIniResult->fetch_assoc()['total'];

$laporanTerkirimQuery = "SELECT COUNT(*) AS total FROM laporan WHERE tanggal = '$tanggalHariIni'";
$laporanTerkirimResult = $conn->query($laporanTerkirimQuery);
$laporanTerkirim = $laporanTerkirimResult->fetch_assoc()['total'];

$aktivitasTerbaruQuery = "
    SELECT p.nama, l.aktivitas, l.tanggal 
    FROM laporan l 
    JOIN peserta p ON l.peserta_id = p.peserta_id 
    ORDER BY l.tanggal DESC LIMIT 5
";
$aktivitasTerbaruResult = $conn->query($aktivitasTerbaruQuery);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Dashboard Admin - Sistem Magang</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #d0f0c0, rgb(209, 219, 199));
        color: #2e4a1f;
        min-height: 100vh;
        overflow-x: hidden;
    }

    .wrapper {
        display: flex;
        min-height: 100vh;
    }

    .menu-btn {
        display: none;
        position: fixed;
        top: 15px;
        left: 15px;
        background-color: #3a6b35;
        color: white;
        border: none;
        padding: 10px 16px;
        font-size: 18px;
        border-radius: 8px;
        z-index: 1001;
    }

    .sidebar {
        background-color: #3a6b35;
        color: #f0f9f0;
        width: 260px;
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        box-shadow: 4px 0 8px rgba(0,0,0,0.2);
        height: 100vh;
        position: relative;
        transition: left 0.3s ease;
    }

    .sidebar h2 {
        font-weight: 700;
        font-size: 26px;
        margin-bottom: 40px;
        text-align: center;
        letter-spacing: 2px;
    }

    .sidebar a {
        color: #d9f5d9;
        padding: 15px 18px;
        margin-bottom: 14px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 17px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background-color 0.3s ease;
    }

    .sidebar a:hover {
        background-color: #529d3a;
        color: #e8f7e8;
    }

    .sidebar a.logout {
        margin-top: auto;
        margin-bottom: 20px;
        color: #f8c1c1;
    }

    .content {
        flex: 1;
        padding: 40px 50px;
    }

    .header h1 {
        font-weight: 700;
        font-size: 32px;
        margin-bottom: 35px;
    }

    .cards {
        display: flex;
        gap: 30px;
        margin-bottom: 45px;
        flex-wrap: wrap;
    }

    .card {
        background: #f1f9e4;
        flex: 1 1 250px;
        padding: 30px 25px;
        border-radius: 18px;
        box-shadow: 0 6px 16px rgba(46, 125, 50, 0.3);
        border-left: 6px solid #4caf50;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .card h3 {
        font-size: 20px;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 14px;
    }

    .card p {
        font-size: 36px;
        font-weight: 800;
        color: #33691e;
        text-align: center;
    }

    h2 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 22px;
        color:rgb(244, 247, 245);
    }

    .table-container {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 300px;
        border-radius: 12px;
        background: #fff;
    }

    table {
        width: 100%;
        min-width: 600px;
        border-collapse: separate;
        border-spacing: 0 12px;
        font-size: 16px;
    }

    th, td {
        padding: 14px 15px;
        text-align: center;
        background-color: #f1f9e4;
        border-radius: 12px;
        color: #2f4f2f;
    }

    th {
        background-color: #529d3a;
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    tr td:first-child {
        text-align: left;
        padding-left: 25px;
    }

    tr:hover td {
        background-color: #a7d67a;
        color: #1b3a0f;
    }

    @media (max-width: 768px) {
        .menu-btn {
            display: block;
        }

        .wrapper {
            flex-direction: column;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: -260px;
            z-index: 1000;
            height: 100%;
        }

        .sidebar.active {
            left: 0;
        }

        .content {
            padding: 80px 20px 20px;
        }
    }
</style>
</head>
<body>

<button id="menu-toggle" class="menu-btn">☰ Menu</button>

<div class="wrapper">
    <div class="sidebar" id="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">🏠 Dashboard</a>
        <a href="manage_peserta.php">👤 Kelola Peserta</a>
        <a href="absensi_admin.php">🗓️ Lihat Absensi</a>
        <a href="laporan_admin.php">📝 Laporan Kegiatan</a>
        <a href="nilai_peserta.php">📋 Nilai Peserta</a>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h1>Selamat Datang, Admin!</h1>
        </div>

        <div class="cards">
            <div class="card"><h3>Total Peserta</h3><p><?= $totalPeserta ?></p></div>
            <div class="card"><h3>Absensi Hari Ini</h3><p><?= $absensiHariIni ?></p></div>
            <div class="card"><h3>Laporan Terkirim</h3><p><?= $laporanTerkirim ?></p></div>
        </div>

        <h2>Aktivitas Terbaru</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Peserta</th>
                        <th>Aktivitas</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $aktivitasTerbaruResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['aktivitas']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('menu-toggle').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>

</body>
</html>
