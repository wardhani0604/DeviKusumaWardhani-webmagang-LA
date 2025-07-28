<?php 
session_start(); 
date_default_timezone_set('Asia/Jakarta');  

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {     
    header('Location: login.php');     
    exit(); 
}  

$conn = new mysqli("localhost", "root", "", "pertanian_db");  

$where = "1"; 
$namaPeserta = ""; 
$lembagaPendidikan = "";  

if (!empty($_GET['nama_peserta'])) {     
    $namaInput = $conn->real_escape_string($_GET['nama_peserta']);     
    $where .= " AND p.nama LIKE '%$namaInput%'";      
// Ambil lembaga dan nama peserta untuk ditampilkan di kop surat
    $res = $conn->query("SELECT nama, lembaga_pendidikan FROM peserta WHERE nama LIKE '%$namaInput%' LIMIT 1");     
    if ($res && $res->num_rows > 0) {         
        $dataPeserta = $res->fetch_assoc();         
        $namaPeserta = $dataPeserta['nama'];         
        $lembagaPendidikan = $dataPeserta['lembaga_pendidikan'];     
    } 
}  

if (!empty($_GET['bulan'])) {     
    $bulan = $_GET['bulan'];     
    $year = substr($bulan, 0, 4);     
    $month = substr($bulan, 5, 2);     
    $where .= " AND MONTH(a.tanggal) = '$month' AND YEAR(a.tanggal) = '$year'";      

    $bulanNama = date('F Y', strtotime("$year-$month-01")); 
} else {     
    $bulanNama = "Semua Bulan"; 
}  
// ambil data absensi
$query = "     
    SELECT a.*, p.nama      
    FROM absensi a      
    JOIN peserta p ON a.peserta_id = p.peserta_id      
    WHERE $where     
    ORDER BY a.tanggal ASC 
"; 

$data = $conn->query($query); 
?>  

<!DOCTYPE html> 
<html lang="id"> 
<head>     
    <meta charset="UTF-8">     
    <title>Rekap Bulanan Absensi</title>     
    <style>         
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-wrapper {
            flex-grow: 1;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-top: 40px;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="month"], button {
            padding: 8px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background: #27ae60;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #219150;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #27ae60;
            color: white;
        }

        .print-btn {
            text-align: center;
            margin: 20px;
        }

        .print-btn button {
            background: #2980b9;
        }

        .print-btn button:hover {
            background-color: #1f6391;
        }

        /* Tombol Kembali */
        .btn-back {
            display: inline-block;
            padding: 10px 18px;
            background-color: #3498db;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-bottom: 20px;
            width: fit-content;
        }

        .btn-back:hover {
            background-color: #2980b9;
            transform: translateX(-3px);
        }

        .kop-surat {
            text-align: center;
            margin-bottom: 30px;
            margin-top: 20px;
        }

        .kop-surat h3, .kop-surat p {
            margin: 0;
        }

        .kop-surat .tanggal {
            text-align: right;
            margin-top: 20px;
        }

        .ttd {
            text-align: right;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .ttd p {
            margin: 0;
        }

        .content-container {
            padding: 20px;
            margin-top: 40px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        @media print {
            body {
                display: block;
            }

            form, .print-btn, .btn-back {
                display: none;
            }

            table {
                box-shadow: none;
            }

            .kop-surat {
                margin-top: 0;
            }

            .content-container {
                border: none;
                box-shadow: none;
            }

            .ttd {
                position: fixed;
                bottom: 20px;
                right: 20px;
            }
        }
    </style> 
</head> 
<body>  

<a href="admin_dashboard.php" class="btn-back">← Kembali</a>  

<div class="kop-surat">     
    <h3>DAFTAR RINGKASAN ABSENSI Peserta Magang <?= htmlspecialchars($lembagaPendidikan) ?></h3> 
</div>  

<div class="content-wrapper">     
    <h2>Rekap Absensi Bulanan <?= htmlspecialchars($bulanNama) ?> <?= $namaPeserta ? " - " . htmlspecialchars($namaPeserta) : "" ?></h2>     
    <form method="GET">         
        <input type="text" name="nama_peserta" placeholder="Ketik nama peserta" value="<?= $_GET['nama_peserta'] ?? '' ?>">         
        <input type="month" name="bulan" value="<?= $_GET['bulan'] ?? '' ?>">         
        <button type="submit">Tampilkan</button>     
    </form>      

    <div class="print-btn">         
        <button onclick="window.print()">🖨️ Cetak</button>     
    </div>      

    <table>         
        <thead>             
            <tr>                 
                <th>No</th>                 
                <th>Nama</th>                 
                <th>Tanggal</th>                 
                <th>Datang</th>                 
                <th>Pulang</th>             
            </tr>         
        </thead>         
        <tbody>             
            <?php             
            if ($data->num_rows > 0) {                 
                $no = 1;                 
                while ($row = $data->fetch_assoc()) {                     
                    echo "<tr>                             
                        <td>{$no}</td>                             
                        <td>" . htmlspecialchars($row['nama']) . "</td>                             
                        <td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>                             
                        <td>{$row['datang']}</td>                             
                        <td>{$row['pulang']}</td>                           
                    </tr>";                     
                    $no++;                 
                }             
            } else {                 
                echo "<tr><td colspan='5'>Tidak ada data</td></tr>";             
            }             
            ?>         
        </tbody>     
    </table> 

    <?php
    // Hitung jumlah Hadir, Izin, dan Sakit
    $rekapQuery = "     
        SELECT a.status     
        FROM absensi a     
        JOIN peserta p ON a.peserta_id = p.peserta_id     
        WHERE $where
    ";
    $rekapResult = $conn->query($rekapQuery);

    $jumlahHadir = 0;
    $jumlahIzin = 0;
    $jumlahSakit = 0;

    if ($rekapResult && $rekapResult->num_rows > 0) {
        while ($row = $rekapResult->fetch_assoc()) {
            switch ($row['status']) {
                case 'Hadir': $jumlahHadir++; break;
                case 'Izin': $jumlahIzin++; break;
                case 'Sakit': $jumlahSakit++; break;
            }
        }
    }
    ?>

    <p><b>Jumlah Hadir:</b> <?= $jumlahHadir ?></p>
    <p><b>Jumlah Izin:</b> <?= $jumlahIzin ?></p>
    <p><b>Jumlah Sakit:</b> <?= $jumlahSakit ?></p>

</div>  

<div class="ttd">     
    <p>Palembang, <?= date('d F Y') ?></p>     
    <p><b>Kepala Subbagian Umum dan Kepegawaian</b></p>     
    <br><br><br><br>     
    <p><b>Lili Rozali, S.Kom, SP</b><br>        
        Penata Tingkat I (III/d)<br>        
        NIP : 197806222005011001</p> 
</div>  

</body> 
</html>
