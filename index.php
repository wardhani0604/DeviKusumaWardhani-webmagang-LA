<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login - Sistem Pertanian</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
<style>
  * {
    margin: 0; padding: 0; box-sizing: border-box;
  }
  body, html {
    height: 100%;
    font-family: 'Poppins', sans-serif;
    background: url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1650&q=80') no-repeat center center fixed;
    background-size: cover;
    position: relative;
    overflow: hidden;
  }
  
  body::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(20, 80, 20, 0.85), rgba(50, 110, 50, 0.65));
    z-index: 0;
    backdrop-filter: brightness(0.7);
  }

  .login-box {
    position: relative;
    z-index: 1;
    max-width: 420px;
    width: 90%;
    margin: auto;
    padding: 40px 35px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 8px 32px rgba(20, 50, 20, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #e6f0e6;
    animation: fadeInScale 0.6s ease forwards;
  }
  @keyframes fadeInScale {
    from {
      opacity: 0; transform: scale(0.95);
    } to {
      opacity: 1; transform: scale(1);
    }
  }

  .login-box h2 {
    text-align: center;
    font-weight: 600;
    font-size: 28px;
    margin-bottom: 25px;
    position: relative;
    letter-spacing: 0.05em;
  }
  /* Small leaf icon above title */
  .login-box h2::before {
    content: "\f06c";
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    position: absolute;
    top: -40px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 36px;
    color: #a8e063;
    filter: drop-shadow(0 0 2px #73a839);
  }

  form {
    display: flex;
    flex-direction: column;
    gap: 25px;
  }

  .input-group {
    position: relative;
    width: 100%;
  }

  .input-group input {
    width: 100%;
    padding: 16px 18px 16px 48px;
    border: none;
    border-radius: 14px;
    font-size: 16px;
    background: rgba(255, 255, 255, 0.25);
    color: #e6f0e6;
    outline: none;
    transition: all 0.3s ease;
    box-shadow: inset 1px 1px 3px rgba(255 255 255 / 0.3), inset -1px -1px 5px rgba(0 0 0 / 0.25);
  }
  .input-group input::placeholder {
    color: transparent;
  }
  .input-group label {
    position: absolute;
    left: 48px;
    top: 50%;
    transform: translateY(-50%);
    color: #c1d1b9;
    font-size: 15px;
    pointer-events: none;
    transition: 0.3s ease all;
  }
  .input-group input:focus {
    background: rgba(255, 255, 255, 0.4);
    box-shadow: inset 2px 2px 5px rgba(255 255 255 / 0.6), inset -2px -2px 8px rgba(0 0 0 / 0.3);
  }
  .input-group input:focus + label,
  .input-group input:not(:placeholder-shown) + label {
    top: -12px;
    font-size: 13px;
    color: #a8e063;
    font-weight: 600;
    text-shadow: 0 0 3px rgba(168, 224, 99, 0.7);
  }

  /* Icon on left */
  .input-group i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #a8e063;
    font-size: 20px;
    pointer-events: none;
    filter: drop-shadow(0 0 2px #73a839);
  }

  button {
    padding: 16px;
    border-radius: 16px;
    border: none;
    background: linear-gradient(90deg, #a8e063 0%, #56ab2f 100%);
    color: #1b3300;
    font-weight: 700;
    font-size: 18px;
    cursor: pointer;
    box-shadow: 0 6px 14px rgba(84, 138, 20, 0.7);
    transition: all 0.35s ease;
  }
  button:hover {
    background: linear-gradient(90deg, #7fc145 0%, #3c6e17 100%);
    box-shadow: 0 8px 18px rgba(120, 175, 30, 0.9);
    transform: scale(1.04);
  }
  button:active {
    transform: scale(0.95);
  }

  .message {
    text-align: center;
    color: #ff6b6b;
    font-weight: 600;
    margin-bottom: 15px;
    text-shadow: 0 0 3px #ff8f8f;
  }

  .register-link {
    text-align: center;
    margin-top: 22px;
    font-size: 15px;
    color: #d7e6c4;
  }
  .register-link a {
    color: #a8e063;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
  }
  .register-link a:hover {
    color: #d4f26f;
    text-decoration: underline;
  }

  /* Responsive */
  @media (max-width: 450px) {
    .login-box {
      padding: 35px 25px;
    }
    button {
      font-size: 16px;
    }
  }
</style>
</head>
<body>

  <div class="login-box">
    <h2>Login Sistem Pertanian Peserta Magang</h2>

    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="message"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <form action="process_login.php" method="POST" autocomplete="off">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="username" id="username" placeholder=" " required autocomplete="username" />
        <label for="username">Username</label>
      </div>
      <div class="input-group"> <!-- Input Password -->
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="password" placeholder=" " required autocomplete="current-password" />
        <label for="password">Password</label>
      </div>
      <button type="submit" name="login">Masuk</button>
    </form>

    <div class="register-link">
      Belum punya akun? <a href="register.php">Daftar di sini</a>
    </div>
  </div>

</body>
</html>
