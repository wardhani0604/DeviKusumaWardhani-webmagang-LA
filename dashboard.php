<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

echo "<h1>Selamat datang di Sistem Pertanian, " . $_SESSION['username'] . "</h1>";
echo "<p>Ini adalah halaman dashboard untuk sistem pertanian.</p>";
echo "<a href='logout.php'>Logout</a>";
?>
