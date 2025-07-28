<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hrd') {
    header("Location: index.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "pertanian_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// API WhatsApp Fonnte
$api_key = 'qA4A5SMuARinwJovGDZg';

// Proses validasi
if (isset($_GET['id']) && isset($_GET['validasi'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $validasi = $conn->real_escape_string($_GET['validasi']);

    if (in_array($validasi, ['diterima', 'ditolak'])) {
        $peserta = $conn->query("SELECT * FROM peserta WHERE peserta_id = '$id'")->fetch_assoc();

        if ($peserta) {
            $conn->query("UPDATE peserta SET status_validasi = '$validasi' WHERE peserta_id = '$id'");

            $nama = $peserta['nama'];
            $no_hp = preg_replace('/^0/', '62', $peserta['no_hp']); // Format 08xx -> 628xx

            // Pesan WhatsApp
            if ($validasi === 'diterima') {
                $pesan = "*Assalamu'alaikum Wr. Wb.*\n\n" .
                         "Yth. Saudara/i *$nama*,\n\n" .
                         "Berdasarkan hasil seleksi, Anda *DITERIMA* untuk melaksanakan kegiatan magang di:\n" .
                         "*Dinas Pertanian Tanaman Pangan dan Hortikultura Provinsi Sumatera Selatan*.\n\n" .
                         "Mohon hadir sesuai jadwal dan menjaga etika serta kedisiplinan selama magang berlangsung.\n\n" .
                         "*Wassalamu'alaikum Wr. Wb.*\n- Dinas Pertanian Tanaman Pangan dan Hortikultura Provinsi Sumatera Selatan";
            } else {
                $pesan = "*Assalamu'alaikum Wr. Wb.*\n\n" .
                         "Yth. Saudara/i *$nama*,\n\n" .
                         "Kami sampaikan bahwa Anda *BELUM DITERIMA* untuk melaksanakan magang di:\n" .
                         "*Dinas Pertanian Tanaman Pangan dan Hortikultura Provinsi Sumatera Selatan*.\n\n" .
                         "Kami ucapkan terima kasih atas partisipasinya.\n\n" .
                         "*Wassalamu'alaikum Wr. Wb.*\n- Dinas Pertanian Tanaman Pangan dan Hortikultura Provinsi Sumatera Selatan";
            }

            // Kirim WA via Fonnte
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => array(
                    'target' => $no_hp,
                    'message' => $pesan,
                ),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: $api_key"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                echo "<script>alert('Validasi berhasil, tapi WA gagal dikirim: $err'); window.location.href='validasi_peserta.php';</script>";
            } else {
                echo "<script>alert('Peserta berhasil divalidasi dan WA berhasil dikirim.'); window.location.href='validasi_peserta.php';</script>";
            }
            exit();
        } else {
            echo "<script>alert('ID peserta tidak ditemukan.'); window.location.href='validasi_peserta.php';</script>";
            exit();
        }
    }
}

// Tampil data
$keyword = isset($_GET['cari']) ? $conn->real_escape_string($_GET['cari']) : "";
$peserta = !empty($keyword) ?
    $conn->query("SELECT * FROM peserta WHERE nama LIKE '%$keyword%' ORDER BY nama ASC") :
    $conn->query("SELECT * FROM peserta ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Validasi Peserta Magang</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 0;
            color: #2c3e50;
        }

        .header {
            background: linear-gradient(90deg, #2ecc71, #27ae60);
            color: white;
            padding: 25px 0;
            text-align: center;
            font-weight: 700;
            font-size: 28px;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(44, 62, 80, 0.1);
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-form input[type="text"] {
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            flex: 1;
        }

        .search-form button {
            background-color: #27ae60;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            border: none;
            cursor: pointer;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        thead {
            background: #2ecc71;
            color: white;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .status-link {
            font-weight: bold;
            border-radius: 6px;
            padding: 6px 10px;
            text-decoration: none;
            display: inline-block;
            margin: 5px 0;
            font-size: 13px;
            width: 90px;
            text-align: center;
        }

        .diterima {
            background-color: #2ecc71;
            color: white;
        }

        .ditolak {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
<div class="header">Validasi Peserta Magang</div>
<div class="container">
    <a href="hrd_dashboard.php" class="back-button">&larr; Kembali ke Dashboard</a>
    <form method="GET" class="search-form">
        <input type="text" name="cari" placeholder="Cari nama peserta..." value="<?= htmlspecialchars($keyword) ?>" />
        <button type="submit">&#128269; Cari</button>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th><th>ID</th><th>Nama</th><th>Lembaga</th><th>Jurusan</th><th>Username</th><th>Password</th><th>No HP</th><th>Tgl Masuk</th><th>Tgl Keluar</th><th>Durasi</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($peserta->num_rows > 0): $no = 1; while ($row = $peserta->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['peserta_id']) ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['lembaga_pendidikan']) ?></td>
                    <td><?= htmlspecialchars($row['jurusan']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= substr(htmlspecialchars($row['password']), 0, 10) ?>...</td>
                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_masuk']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_keluar']) ?></td>
                    <td><?= htmlspecialchars($row['durasi_magang']) ?></td>
                    <td>
                        <?php
                            $id = urlencode($row['peserta_id']);
                            if (empty($row['status_validasi'])) {
                                echo "<div style='display: flex; flex-direction: column; align-items: center;'>";
                                echo "<a href='?id=$id&validasi=diterima' class='status-link diterima' onclick=\"return confirm('Yakin ingin menerima peserta ini?')\">Diterima</a>";
                                echo "<a href='?id=$id&validasi=ditolak' class='status-link ditolak' onclick=\"return confirm('Yakin ingin menolak peserta ini?')\">Ditolak</a>";
                                echo "</div>";
                            } elseif ($row['status_validasi'] === 'diterima') {
                                echo "<span class='status-link diterima'>Diterima</span>";
                            } elseif ($row['status_validasi'] === 'ditolak') {
                                echo "<span class='status-link ditolak'>Ditolak</span>";
                            }
                        ?>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="12">Tidak ada peserta ditemukan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
