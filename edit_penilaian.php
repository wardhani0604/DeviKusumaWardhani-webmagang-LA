<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hrd') {
    header("Location: index.php");
    exit();
}

// Ambil ID penilaian yang akan diedit
if (!isset($_GET['id'])) {
    header("Location: penilaian.php");
    exit();
}

$id = $_GET['id'];

// Ambil data penilaian berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM penilaian WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$penilaian = $result->fetch_assoc();

if (!$penilaian) {
    echo "Data penilaian tidak ditemukan.";
    exit();
}

// Update data jika form disubmit
if (isset($_POST['update'])) {
    $nilai_kerajinan = $_POST['nilai_kerajinan'];
    $nilai_sikap = $_POST['nilai_sikap'];
    $nilai_tanggungjawab = $_POST['nilai_tanggungjawab'];

    $stmt = $conn->prepare("UPDATE penilaian SET nilai_kerajinan = ?, nilai_sikap = ?, nilai_tanggungjawab = ? WHERE id = ?");
    $stmt->bind_param("iiii", $nilai_kerajinan, $nilai_sikap, $nilai_tanggungjawab, $id);
    $stmt->execute();

    header("Location: penilaian.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Penilaian</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }
        h2 { color: #2c3e50; }
        form {
            background: #fff;
            padding: 20px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        input {
            padding: 8px;
            width: 100%;
            margin: 5px 0 15px;
        }
        button {
            padding: 8px 16px;
            background: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #e74c3c;
        }
    </style>
</head>
<body>

<h2>Edit Penilaian Peserta</h2>
<form method="POST">
    <label>Nilai Kerajinan:</label>
    <input type="number" name="nilai_kerajinan" value="<?= $penilaian['nilai_kerajinan'] ?>" min="0" max="100" required>

    <label>Nilai Sikap:</label>
    <input type="number" name="nilai_sikap" value="<?= $penilaian['nilai_sikap'] ?>" min="0" max="100" required>

    <label>Nilai Tanggung Jawab:</label>
    <input type="number" name="nilai_tanggungjawab" value="<?= $penilaian['nilai_tanggungjawab'] ?>" min="0" max="100" required>

    <button type="submit" name="update">Update</button>
    <a href="penilaian.php">Kembali</a>
</form>

</body>
</html>
