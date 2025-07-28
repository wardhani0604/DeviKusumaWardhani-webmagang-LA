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

$notif = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $peserta_id = $conn->real_escape_string($_POST['peserta_id']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $username = $conn->real_escape_string($_POST['username']);
    $password_plain = $_POST['password'];
    $password = password_hash($password_plain, PASSWORD_DEFAULT);
    $lembaga_pendidikan = $conn->real_escape_string($_POST['lembaga_pendidikan']);
    $jurusan = $conn->real_escape_string($_POST['jurusan']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $tanggal_keluar = $_POST['tanggal_keluar'];

    if (strtotime($tanggal_keluar) <= strtotime($tanggal_masuk)) {
        $notif = "<div class='alert error'>Tanggal keluar harus setelah tanggal masuk.</div>";
    } else {
        $masuk = new DateTime($tanggal_masuk);
        $keluar = new DateTime($tanggal_keluar);
        $interval = $masuk->diff($keluar);
        $durasi_magang = ($interval->y * 12) + $interval->m;

        $cek_id = $conn->query("SELECT peserta_id FROM peserta WHERE peserta_id = '$peserta_id'");
        $cek_user = $conn->query("SELECT username FROM peserta WHERE username = '$username'");

        if ($cek_id->num_rows > 0) {
            $notif = "<div class='alert error'>ID Peserta sudah digunakan.</div>";
        } elseif ($cek_user->num_rows > 0) {
            $notif = "<div class='alert error'>Username sudah digunakan.</div>";
        } else {
            $simpan = $conn->query("INSERT INTO peserta 
                (peserta_id, nama, username, password, lembaga_pendidikan, jurusan, no_hp, tanggal_masuk, tanggal_keluar, durasi_magang, status_validasi) 
                VALUES 
                ('$peserta_id', '$nama', '$username', '$password', '$lembaga_pendidikan', '$jurusan', '$no_hp', '$tanggal_masuk', '$tanggal_keluar', '$durasi_magang', 'belum')");

            if ($simpan) {
                // Kirim WA ke peserta
                $pesan = "Assalamu'alaikum Wr. Wb.\n\n" .
                         "Yth. Saudara/i *$nama*,\n" .
                         "Anda berhasil didaftarkan sebagai calon peserta magang di Dinas Pertanian Tanaman Pangan dan Hortikultura Provinsi Sumatera Selatan. \n" .
                         "Mohon menunggu proses validasi selanjutnya untuk mengetahui status akhir penerimaan Anda.\n\n" .
                         "Terima kasih.\nWassalamu'alaikum Wr. Wb.";

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.fonnte.com/send',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => array(
                        'target' => $no_hp,
                        'message' => $pesan,
                        'countryCode' => '62'
                    ),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: qA4A5SMuARinwJovGDZg"
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);

                $notif = "<div class='alert success'>Peserta berhasil ditambahkan dan notifikasi WhatsApp telah dikirim.</div>";
            } else {
                $notif = "<div class='alert error'>Gagal menambahkan peserta. Error: " . $conn->error . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Peserta</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #27ae60;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input, button {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        button {
            background: #27ae60;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #219150;
        }
        .alert {
            padding: 12px;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
        }
        .alert.success {
            background: #2ecc71;
            color: white;
        }
        .alert.error {
            background: #e74c3c;
            color: white;
        }
        .back-button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #bdc3c7;
            color: #2c3e50;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #95a5a6;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Form Tambah Peserta Magang</h2>
    <?= $notif ?>
    <form method="POST">
        <label>ID Peserta</label>
        <input type="text" name="peserta_id" required>
        <label>Nama Lengkap</label>
        <input type="text" name="nama" required>
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <label>Lembaga Pendidikan</label>
        <input type="text" name="lembaga_pendidikan" required>
        <label>Jurusan</label>
        <input type="text" name="jurusan" required>
        <label>No HP (format 628xxx)</label>
        <input type="text" name="no_hp" required>
        <label>Tanggal Masuk</label>
        <input type="date" name="tanggal_masuk" required>
        <label>Tanggal Keluar</label>
        <input type="date" name="tanggal_keluar" required>
        <button type="submit">Simpan dan Kirim WA</button>
    </form>
    <div style="text-align:center;">
        <a href="manage_peserta.php" class="back-button">&larr; Kembali ke Daftar Peserta</a>
    </div>
</div>
</body>
</html>
