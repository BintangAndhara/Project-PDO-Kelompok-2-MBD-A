<?php
// public/login.php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$file_json = '../config/users.json';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $users = [];
    if (file_exists($file_json)) {
        $json_data = file_get_contents($file_json);
        $users = json_decode($json_data, true) ?? [];
    }

    $login_success = false;
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            // Set Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            $login_success = true;
            break;
        }
    }

    if ($login_success) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lokale Select</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F4F7FB; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-auth { border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0, 91, 143, 0.1); border-top: 6px solid #005B8F; width: 100%; max-width: 400px; }
        .btn-custom { background: linear-gradient(135deg, #005B8F, #07779D); color: white; border: none; border-radius: 10px; font-weight: 600; padding: 12px; transition: all 0.3s; }
        .btn-custom:hover { background: linear-gradient(135deg, #003F63, #005B8F); color: white; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="card card-auth p-4 p-md-5">
        <div class="text-center mb-4">
            <img src="img/logo-lokale.png" alt="Logo" height="50" class="mb-3">
            <h4 class="fw-bold" style="color: #003F63;">Selamat Datang</h4>
            <p class="text-secondary small">Silakan login untuk melanjutkan</p>
        </div>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'registered'): ?>
            <div class="alert alert-success small rounded-3">Registrasi berhasil! Silakan login.</div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger small rounded-3"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-medium small">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-medium small">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-custom w-100 mb-3">Login</button>
            <div class="text-center small">
                Belum punya akun? <a href="register.php" class="text-decoration-none fw-bold" style="color: #005B8F;">Daftar di sini</a>
            </div>
        </form>
    </div>
</body>
</html>