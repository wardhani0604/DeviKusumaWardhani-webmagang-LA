<?php
session_start();
include('config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'peserta') {
    header("Location: login.php");
    exit();
}

$peserta_id = $_SESSION['peserta_id'];
date_default_timezone_set('Asia/Jakarta');
$current_time = date('H:i:s');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $tanggal = $_POST['tanggal'];
    $waktu_absen = $_POST['waktu'];
//memeriksa apakah sudah absen
    $cek = $conn->query("SELECT * FROM absensi WHERE peserta_id = '$peserta_id' AND tanggal = '$tanggal'");

    if ($cek->num_rows > 0) {
        $row = $cek->fetch_assoc();
        $id = $row['id'];

        if ($waktu_absen == 'datang' && empty($row['datang'])) {
            $sql = "UPDATE absensi SET datang = '$current_time', status = '$status' WHERE id = $id";
        } elseif ($waktu_absen == 'pulang' && empty($row['pulang'])) {
            $sql = "UPDATE absensi SET pulang = '$current_time', status = '$status' WHERE id = $id";
        } else {
            $sql = null;
            echo "<script>alert('Anda sudah absen untuk waktu tersebut.');</script>";
        }
    } else {
        if ($waktu_absen == 'datang') {
            $sql = "INSERT INTO absensi (peserta_id, tanggal, datang, status) VALUES ('$peserta_id', '$tanggal', '$current_time', '$status')";
        } elseif ($waktu_absen == 'pulang') {
            $sql = "INSERT INTO absensi (peserta_id, tanggal, pulang, status) VALUES ('$peserta_id', '$tanggal', '$current_time', '$status')";
        } else {
            $sql = null;
        }
    }

    if (!empty($sql)) {
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Absensi berhasil disimpan');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}
?>
<!--desain-->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi Peserta Magang</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> <!--font-->
    <script src="https://unpkg.com/lucide@latest"></script> <!--icon-->
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #d0e6a5, #86af49);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            color: #2c3e50;
        }

        .absen-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            position: relative;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px 16px 14px 42px;
            font-size: 15px;
            border: none;
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0.9);
            transition: 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            box-shadow: 0 0 0 2px #27ae60;
        }

        .form-group label {
            position: absolute;
            top: -10px;
            left: 14px;
            background: #fff;
            padding: 0 6px;
            font-size: 12px;
            color: #555;
        }

        .form-group .icon {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #555;
        }

        .button {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            border: none;
            color: white;
            padding: 14px;
            font-size: 16px;
            border-radius: 14px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.3s ease;
        }

        .button:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 20px rgba(39, 174, 96, 0.4);
        }

        .button-back {
            background-color: #e67e22;
            color: white;
            margin-top: 15px;
            border: none;
            padding: 12px;
            font-size: 15px;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
        }

        .button-back:hover {
            background-color: #d35400;
            transform: translateY(-2px);
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 25px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Absensi Peserta Magang</h2>

    <form class="absen-form" method="POST">
        <div class="form-group">
            <i class="icon" data-lucide="calendar"></i>
            <input type="date" name="tanggal" id="tanggal" required value="<?= date('Y-m-d') ?>">
            <label for="tanggal">Tanggal Absensi</label>
        </div>

        <div class="form-group">
            <i class="icon" data-lucide="user-check"></i>
            <select name="status" id="status" required>
                <option value="" disabled selected hidden></option>
                <option value="Hadir">Hadir</option>
                <option value="Sakit">Sakit</option>
                <option value="Izin">Izin</option>
            </select>
            <label for="status">Status Kehadiran</label>
        </div>

        <div class="form-group">
            <i class="icon" data-lucide="clock"></i>
            <select name="waktu" id="waktu" required>
                <option value="" disabled selected hidden></option>
                <option value="datang">Datang</option>
                <option value="pulang">Pulang</option>
            </select>
            <label for="waktu">Waktu Absensi</label>
        </div>

        <button type="submit" class="button">
            <i data-lucide="check-circle"></i> Absen Sekarang
        </button>
    </form>

    <a href="peserta_dashboard.php">
        <button class="button-back">← Kembali ke Dashboard</button>
    </a>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>
