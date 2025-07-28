<?php
session_start();
include('config.php');

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi password dan konfirmasi
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Password dan konfirmasi password tidak cocok!";
        echo "<script>
            alert('{$_SESSION['error_message']}');
            window.location.href = 'register.php';
        </script>";
        exit();
    }

    // Enkripsi password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Proses simpan ke tabel admin saja
    $sql = "INSERT INTO admin (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Registrasi admin berhasil! Silakan login."; // Simpan pesan sukses ke dalam session
        echo "<script>
            alert('{$_SESSION['success_message']}');
            window.location.href = 'index.php';
        </script>";
    } else { // Jika eksekusi gagal, simpan pesan error ke session
        $_SESSION['error_message'] = "Terjadi kesalahan saat registrasi!";
        echo "<script>
            alert('{$_SESSION['error_message']}');
            window.location.href = 'register.php';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
