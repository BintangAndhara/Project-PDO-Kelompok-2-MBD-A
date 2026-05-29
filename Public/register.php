<?php
// public/register.php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$file_json = '../config/users.json';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    
    $users = [];
    if (file_exists($file_json)) {
        $json_data = file_get_contents($file_json);
        $users = json_decode($json_data, true) ?? [];
    }

    // Cek apakah username sudah ada
    $username_exists = false;
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            $username_exists = true;
            break;
        }
    }

    if ($username_exists) {
        $error = "Username sudah digunakan!";
    } else {
        
        $new_id = count($users) > 0 ? end($users)['id'] + 1 : 1;

        
        $new_user = [
            'id' => $new_id,
            'nama' => $nama,
            'username' => $username,
            'password' => $password,
            'role' => $role
        ];
        
        $users[] = $new_user;

        file_put_contents($file_json, json_encode($users, JSON_PRETTY_PRINT));
        
        header("Location: login.php?status=registered");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Lokale Select</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F4F7FB; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-auth { border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0, 91, 143, 0.1); border-top: 6px solid #07779D; width: 100%; max-width: 450px; }
        .btn-custom { background: linear-gradient(135deg, #07779D, #0A91BE); color: white; border: none; border-radius: 10px; font-weight: 600; padding: 12px; transition: all 0.3s; }
        .btn-custom:hover { background: linear-gradient(135deg, #005B8F, #07779D); color: white; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="card card-auth p-4 p-md-5">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #003F63;">Buat Akun</h3>
            <p class="text-secondary small">Daftarkan diri Anda ke sistem Lokale Select</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger small rounded-3"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-medium small">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium small">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium small">Posisi (Role)</label>
                <select name="role" class="form-select" required>
                    <option value="" disabled selected>Pilih Role...</option>
                    <option value="Kasir">Kasir (Transaksi)</option>
                    <option value="Admin">Admin (Kelola Menu)</option>
                    <option value="Manajer">Manajer (Laporan)</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label fw-medium small">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-custom w-100 mb-3">Daftar Sekarang</button>
            <div class="text-center small">
                Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold" style="color: #07779D;">Login di sini</a>
            </div>
        </form>
    </div>
</body>
</html>