<?php
session_start();
include('config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'peserta') {
    header("Location: index.php");
    exit();
}

$peserta_id = $_SESSION['peserta_id']; // Ambil data peserta dari database berdasarkan ID
$sql = "SELECT * FROM peserta WHERE peserta_id='$peserta_id'";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "Data peserta tidak ditemukan.";
    exit();
} // Jika data tidak ditemukan atau query gagal

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Peserta Magang</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #d0f0c0, #a0d468);
            color: #2f4f2f;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #3a6b35;
            color: white;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        }

        .logo {
            width: 48px;
            height: 48px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .logo svg {
            fill: #c8e6c9;
        }

        header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 2px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        .container {
            max-width: 960px;
            margin: 40px auto 60px;
            padding: 25px 30px;
            background: #f6fff4;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(46, 125, 50, 0.2);
            animation: fadeInUp 1s ease forwards;
            flex-grow: 1;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            font-size: 26px;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 18px;
        }

        .card-body p {
            font-size: 17px;
            color: #3b5323;
            margin-bottom: 24px;
        }

        .button-container {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(170px,1fr));
            gap: 20px;
        }

        .button {
            background: linear-gradient(135deg, #6fbf73, #3a6b35);
            color: white;
            padding: 14px 0;
            border: none;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .button:hover {
            background: linear-gradient(135deg, #4a7d32, #2b451b);
            transform: translateY(-4px);
            box-shadow: 0 12px 18px rgba(0,0,0,0.25);
        }

        .button:focus {
            outline: 2px solid #a4d68d;
            outline-offset: 3px;
        }

        .button svg {
            stroke: #e0f2f1;
            width: 20px;
            height: 20px;
        }

        .sejarah-section {
            margin-top: 50px;
        }

        .sejarah-section h2 {
            font-size: 24px;
            font-weight: 700;
            color: #2e7d32;
            text-align: center;
            margin-bottom: 22px;
            letter-spacing: 1.5px;
        }

        .sejarah-card {
            background: #d8f0c8;
            border-left: 6px solid #4caf50;
            border-radius: 10px;
            padding: 22px 20px;
            margin-bottom: 22px;
            box-shadow: 0 5px 10px rgba(46, 125, 50, 0.15);
            transition: transform 0.3s ease;
            animation: slideUp 1s ease forwards;
        }

        .sejarah-card:hover {
            transform: scale(1.04);
        }

        .sejarah-card h3 {
            margin: 0 0 12px;
            font-size: 20px;
            color: #2e7d32;
            font-weight: 700;
        }

        .sejarah-card p {
            margin: 0;
            font-size: 16px;
            color: #3b5323;
            line-height: 1.6;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .footer {
            background-color: #3a6b35;
            color: #c8e6c9;
            text-align: center;
            padding: 14px 0;
            font-size: 14px;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>

<header>
    <div class="logo" aria-label="Logo Pertanian">
        <!-- Ikon Padi SVG sederhana -->
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" >
            <path d="M32 2c-1.104 0-2 .896-2 2v12.586l-7.293-7.293-1.414 1.414L29.172 20 20 29.172l-1.414-1.414L11.293 33.172l1.414 1.414 7.293-7.293V40c0 1.104.896 2 2 2s2-.896 2-2v-16.586l7.293 7.293 1.414-1.414L34.828 28 44 18.828l1.414 1.414L52.707 14.828l-1.414-1.414-7.293 7.293V4c0-1.104-.896-2-2-2z" />
        </svg>
    </div>
    <h1>Dashboard Peserta Magang</h1>
</header>

<div class="container">
    <div class="card-header">Selamat datang, <?= htmlspecialchars($user['nama']); ?>!</div>
    <div class="card-body">
        <p>Anda dapat melakukan absensi harian dan mengisi laporan kegiatan melalui menu berikut:</p>
        <div class="button-container">
            <a href="absensi.php" class="button" aria-label="Absensi"><svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>Absensi</a>

            <a href="laporan.php" class="button" aria-label="Laporan Harian"><svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>Laporan Harian</a>

            <a href="print_ringkasan_absensipeserta.php?peserta_id=<?= $peserta_id; ?>" class="button" aria-label="Cetak Ringkasan Absensi"><svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-printer" viewBox="0 0 24 24"><path d="M6 9v6h12V9"/><path d="M6 18h12v4H6z"/><path d="M6 6h12v3H6z"/></svg>Cetak Absensi</a>

            <a href="print_ringkasan_laporanpeserta.php?peserta_id=<?= $peserta_id; ?>" class="button" aria-label="Cetak Ringkasan Pelaporan"><svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-printer" viewBox="0 0 24 24"><path d="M6 9v6h12V9"/><path d="M6 18h12v4H6z"/><path d="M6 6h12v3H6z"/></svg>Cetak Laporan</a>
            
            <a href="penilaian_peserta.php" class="button" aria-label="Lihat Penilaian"><svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award" viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"/><path d="M8.21 13.89L7 23l5-3 5 3-1.21-9.12"/></svg>Lihat Nilai</a>

            <a href="logout.php" class="button" style="background:#b71c1c;" aria-label="Logout"><svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out" viewBox="0 0 24 24"><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/><path d="M9 19v-14"/></svg>Logout</a>
        </div>
    </div>

    
    <div class="sejarah-section" aria-label="Sejarah Dinas Pertanian">
        <h2>Sejarah Dinas Pertanian</h2>
        <div class="sejarah-card" style="animation-delay: 0.2s;">
            <h3>1953 - 1970</h3>
            <p>Dinas Pertanian pertama kali dikenal dengan nama Jawatan Pertanian Rakjat Propinsi Sumatera Selatan pada tahun 1953. Kemudian pada tahun 1970, berubah menjadi Dinas Pertanian Rakjat Propinsi Sumatera Selatan.</p>
        </div>
        <div class="sejarah-card" style="animation-delay: 0.4s;">
            <h3>1980 - 2001</h3>
            <p>Pada tahun 1980, nama instansi ini kembali berubah menjadi Dinas Pertanian Tanaman Pangan Propinsi Daerah Tingkat I Sumatera Selatan. Pada tahun 2001, berganti nama menjadi Dinas Pertanian Propinsi Daerah Tingkat I Sumatera Selatan.</p>
        </div>
        <div class="sejarah-card" style="animation-delay: 0.6s;">
            <h3>2003 - 2016</h3>
            <p>Pada tahun 2003, Dinas ini berganti nama menjadi Dinas Pertanian Tanaman Pangan dan Hortikultura Provinsi Sumatera Selatan hingga sekarang.</p>
        </div>
    </div>
</div>

<footer class="footer">
    &copy; <?= date('Y'); ?> Dinas Pertanian Provinsi Sumatera Selatan
</footer>

<script>
    // Lucide icons init
    lucide.replace();
</script>

</body>
</html>
