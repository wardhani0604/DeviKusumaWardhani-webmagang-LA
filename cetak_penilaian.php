<?php
session_start();
include 'config.php';

// Cek apakah user login sebagai admin atau HRD
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil semua data penilaian + peserta
$sql = "SELECT ps.nama, p.* 
        FROM penilaian p
        JOIN peserta ps ON p.peserta_id = ps.peserta_id
        ORDER BY ps.nama ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Nilai Peserta</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        input[type="text"] {
            padding: 8px;
            width: 300px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #2e8b57;
            color: white;
        }

        .button-container {
            margin: 20px 0;
            text-align: center;
        }

        .button-container button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            color: white;
            background-color: #2e8b57;
            cursor: pointer;
            border-radius: 5px;
        }

        @media print {
            .button-container,
            #searchInput {
                display: none;
            }
        }
    </style>
    <script>
        function filterTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr");

            rows.forEach(row => {
                const nama = row.querySelector("td").innerText.toLowerCase();
                row.style.display = nama.includes(input) ? "" : "none";
            });
        }
    </script>
</head>
<body>

<h2>Rekapitulasi Nilai Peserta Magang</h2>

<div class="button-container">
    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari nama peserta...">
    <button onclick="window.print()">🖨️ Cetak</button>
</div>

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Persiapan</th>
            <th>Alat</th>
            <th>Penyelesaian</th>
            <th>Kualitas</th>
            <th>Rata Teknis</th>
            <th>Disiplin</th>
            <th>Kerjasama</th>
            <th>Inisiatif</th>
            <th>Tanggung Jawab</th>
            <th>Kebersihan</th>
            <th>Kerapihan</th>
            <th>Rata Non-Teknis</th>
            <th>Rata Akhir</th>
            <th>Predikat</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row['nama']) . "</td>
                    <td>{$row['persiapan_pekerjaan']}</td>
                    <td>{$row['penggunaan_alat']}</td>
                    <td>{$row['penyelesaian_pekerjaan']}</td>
                    <td>{$row['kualitas_pekerjaan']}</td>
                    <td>{$row['rata_teknis']}</td>
                    <td>{$row['disiplin_kerja']}</td>
                    <td>{$row['kerjasama']}</td>
                    <td>{$row['inisiatif']}</td>
                    <td>{$row['tanggung_jawab']}</td>
                    <td>{$row['kebersihan']}</td>
                    <td>{$row['kerapihan']}</td>
                    <td>{$row['rata_non_teknis']}</td>
                    <td>{$row['rata_rata_akhir']}</td>
                    <td>{$row['predikat']}</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='15'>Tidak ada data penilaian.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
