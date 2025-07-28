<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hrd') {
    header("Location: index.php");
    exit();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard HRD</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body {
      background: #f0f4f3;
      color: #333;
    }
    header {
      background-color: #4CAF50;
      padding: 20px;
      color: white;
      text-align: center;
      font-size: 22px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    .container {
      max-width: 960px;
      margin: 40px auto;
      padding: 20px;
    }
    .welcome {
      font-size: 20px;
      margin-bottom: 25px;
      text-align: center;
    }
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
    }
    .card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.3s;
    }
    .card:hover {
      transform: scale(1.05);
    }
    .card a {
      text-decoration: none;
      color: #4CAF50;
      font-weight: 600;
    }
    .logout {
      margin-top: 30px;
      text-align: center;
    }
    .logout a {
      background: #e74c3c;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s;
    }
    .logout a:hover {
      background: #c0392b;
    }
  </style>
</head>
<body>

<header>
  Dashboard HRD - Sistem Magang
</header>

<div class="container">
  <div class="welcome">Selamat datang, <strong><?= htmlspecialchars($username) ?></strong> 👋</div>

  <div class="card-grid">
    <div class="card">
      <h3>Validasi Peserta</h3>
      <p>Kelola data peserta yang mendaftar.</p>
      <a href="validasi_peserta.php">Lihat</a>
    </div>
    <div class="card">
      <h3>Laporan Harian</h3>
      <p>Periksa laporan kegiatan peserta setiap hari.</p>
      <a href="validasi_laporan.php">Lihat</a>
    </div>
    <div class="card">
      <h3>Penilaian</h3>
      <p>Nilai peserta berdasarkan performa mereka.</p>
      <a href="penilaian.php">Lihat</a>
    </div>
  </div>

  <div class="logout">
    <a href="logout.php">Logout</a>
  </div>
</div>

</body>
</html>
