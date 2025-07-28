<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "pertanian_db");

if (!isset($_GET['id'])) {
    header("Location: manage_peserta.php");
    exit();
}
$id = $conn->real_escape_string($_GET['id']);
$peserta = $conn->query("SELECT * FROM peserta WHERE peserta_id = '$id'")->fetch_assoc();

if (!$peserta) {
    echo "Peserta tidak ditemukan.";
    exit();
}

$notif = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $peserta_id = $conn->real_escape_string($_POST['peserta_id']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $lembaga_pendidikan = $conn->real_escape_string($_POST['lembaga_pendidikan']);
    $jurusan = $conn->real_escape_string($_POST['jurusan']);
    $username = $conn->real_escape_string($_POST['username']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);
    $passwordBaru = $_POST['password'];

    $cek_id = $conn->query("SELECT peserta_id FROM peserta WHERE peserta_id = '$peserta_id' AND peserta_id != '$id'");
    $cek_user = $conn->query("SELECT username FROM peserta WHERE username = '$username' AND peserta_id != '$id'");

    if ($cek_id->num_rows > 0) {
        $notif = "<div class='alert error'>ID Peserta sudah digunakan oleh peserta lain.</div>";
    } elseif ($cek_user->num_rows > 0) {
        $notif = "<div class='alert error'>Username sudah digunakan oleh peserta lain.</div>";
    } else {
        if (!empty($passwordBaru)) {
            $password = password_hash($passwordBaru, PASSWORD_DEFAULT);
            $conn->query("UPDATE peserta SET 
                peserta_id = '$peserta_id',
                nama = '$nama',
                lembaga_pendidikan = '$lembaga_pendidikan',
                jurusan = '$jurusan',
                username = '$username',
                no_hp = '$no_hp',
                password = '$password'
                WHERE peserta_id = '$id'");
        } else {
            $conn->query("UPDATE peserta SET 
                peserta_id = '$peserta_id',
                nama = '$nama',
                lembaga_pendidikan = '$lembaga_pendidikan',
                jurusan = '$jurusan',
                username = '$username',
                no_hp = '$no_hp'
                WHERE peserta_id = '$id'");
        }

        $notif = "<div class='alert success'>Data berhasil diperbarui.</div>";
        $id = $peserta_id;
        $peserta = $conn->query("SELECT * FROM peserta WHERE peserta_id = '$id'")->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Peserta</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #e9f5e1 url('https://images.unsplash.com/photo-1447433819943-74a20887a81e?auto=format&fit=crop&w=1950&q=80') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }
        .overlay {
            background: rgba(255, 255, 255, 0.95);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 650px;
            margin: auto;
            background: #fefefe;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-left: 8px solid #27ae60;
        }
        h2 {
            text-align: center;
            color: #2ecc71;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input, button {
            margin-bottom: 15px;
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input:focus {
            border-color: #27ae60;
            outline: none;
            box-shadow: 0 0 5px rgba(39, 174, 96, 0.3);
        }
        button {
            background-color: #27ae60;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #219150;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
            text-align: center;
        }
        .alert.success {
            background-color: #2ecc71;
            color: white;
        }
        .alert.error {
            background-color: #e74c3c;
            color: white;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
        }
        .back-link a {
            text-decoration: none;
            color: #27ae60;
            font-weight: 500;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="container">
        <h2>Edit Data Peserta</h2>
        <?= $notif ?>
        <form method="POST">
            <input type="text" name="peserta_id" value="<?= htmlspecialchars($peserta['peserta_id']) ?>" required>
            <input type="text" name="nama" value="<?= htmlspecialchars($peserta['nama']) ?>" required>
            <input type="text" name="lembaga_pendidikan" value="<?= htmlspecialchars($peserta['lembaga_pendidikan']) ?>" required>
            <input type="text" name="jurusan" value="<?= htmlspecialchars($peserta['jurusan']) ?>" required>
            <input type="text" name="username" value="<?= htmlspecialchars($peserta['username']) ?>" required>
            <input type="text" name="no_hp" value="<?= htmlspecialchars($peserta['no_hp']) ?>" required>
            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
            <button type="submit">🌱 Simpan Perubahan</button>
        </form>
        <div class="back-link">
            <a href="manage_peserta.php">← Kembali ke Daftar Peserta</a>
        </div>
    </div>
</div>
</body>
</html>
