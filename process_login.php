<?php
session_start();
include('config.php'); // Koneksi ke database

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // --- Cek login admin ---
    $sql_admin = "SELECT * FROM admin WHERE username = ?";
    $stmt_admin = $conn->prepare($sql_admin);
    $stmt_admin->bind_param("s", $username);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_admin->num_rows > 0) {
        $user = $result_admin->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'admin';
            $stmt_admin->close();
            $conn->close();
            header("Location: admin_dashboard.php");
            exit();
        }
    }
    $stmt_admin->close();

    // --- Cek login hrd (belum di-hash) ---
    $sql_hrd = "SELECT * FROM hrd WHERE username = ?";
    $stmt_hrd = $conn->prepare($sql_hrd);
    $stmt_hrd->bind_param("s", $username);
    $stmt_hrd->execute();
    $result_hrd = $stmt_hrd->get_result();

    if ($result_hrd->num_rows > 0) {
        $user = $result_hrd->fetch_assoc();
        // SEMENTARA pakai perbandingan biasa karena belum di-hash
        if ($password === $user['password']) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'hrd';
            $_SESSION['hrd_id'] = $user['hrd_id']; // pastikan kolom ini ada
            $stmt_hrd->close();
            $conn->close();
            header("Location: hrd_dashboard.php");
            exit();
        }
    }
    $stmt_hrd->close();

    // --- Cek login peserta ---
    $sql_peserta = "SELECT * FROM peserta WHERE username = ?";
    $stmt_peserta = $conn->prepare($sql_peserta);
    $stmt_peserta->bind_param("s", $username);
    $stmt_peserta->execute();
    $result_peserta = $stmt_peserta->get_result();

    if ($result_peserta->num_rows > 0) {
        $user = $result_peserta->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'peserta';
            $_SESSION['peserta_id'] = $user['peserta_id']; // pastikan kolom ini ada
            $stmt_peserta->close();
            $conn->close();
            header("Location: peserta_dashboard.php");
            exit();
        }
    }
    $stmt_peserta->close();

    // --- Jika semua gagal ---
    $conn->close();
    $_SESSION['error_message'] = "Username atau Password salah!";
    header("Location: index.php");
    exit();
}
?>
