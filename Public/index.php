<?php
// public/index.php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

date_default_timezone_set('Asia/Jakarta');

$jam = date('H');
if ($jam < 12) { $sapaan = "Selamat Pagi"; } 
elseif ($jam < 15) { $sapaan = "Selamat Siang"; } 
elseif ($jam < 18) { $sapaan = "Selamat Sore"; } 
else { $sapaan = "Selamat Malam"; }

$hari_inggris = date('l');
$hari_indo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$tanggal = $hari_indo[$hari_inggris] . ', ' . date('d F Y');

$role = $_SESSION['role'];
$nama_user = $_SESSION['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard <?= $role ?> - Lokale Select</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        /* Paste semua CSS dari index.php lama di sini */
        body { font-family: 'Poppins', sans-serif; background-color: #F4F7FB; color: #1E293B; height: 100vh; display: flex; flex-direction: column; overflow-x: hidden; }
        .navbar-custom { background: linear-gradient(135deg, #005B8F, #07779D); padding: 18px 0; box-shadow: 0 4px 15px rgba(0, 91, 143, 0.15); z-index: 10; }
        .logo-wrapper { background-color: #FFFFFF; padding: 8px 25px; border-radius: 12px; display: inline-flex; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); pointer-events: none; user-select: none; }
        .logo-wrapper img { height: 42px; object-fit: contain; }
        .user-badge { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); padding: 5px 15px; border-radius: 30px; color: white; font-weight: 500; display: flex; align-items: center; gap: 8px; font-size: 0.9rem; }
        .main-content { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; width: 100%; }
        .hero-section { text-align: center; margin-bottom: 45px; }
        .hero-section h1 { font-weight: 800; color: #003F63; font-size: 2.1rem; letter-spacing: -0.5px; margin-bottom: 2px; }
        .badge-date { background-color: #E1EFF6; color: #005B8F; font-weight: 600; padding: 6px 18px; border-radius: 20px; font-size: 0.85rem; display: inline-block; margin-bottom: 15px; border: 1px dashed #07779D; }
        .menu-card { background: white; border-radius: 20px; border-top: 6px solid #07779D; box-shadow: 0 15px 35px rgba(0, 91, 143, 0.08); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; padding: 40px 25px; text-align: center; position: relative; overflow: hidden; height: 100%; }
        .menu-card::before { content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.8) 50%, rgba(255,255,255,0) 100%); transform: skewX(-25deg); transition: all 0.7s ease; z-index: 1; }
        .menu-card:hover::before { left: 200%; }
        .menu-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0, 91, 143, 0.15); color: inherit; }
        .icon-wrapper { width: 90px; height: 90px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin-bottom: 20px; position: relative; z-index: 2; transition: transform 0.3s ease; }
        .menu-card:hover .icon-wrapper { transform: scale(1.1); }
        .icon-transaksi { background: linear-gradient(135deg, #07779D, #0A91BE); color: white; box-shadow: 0 10px 20px rgba(7, 119, 157, 0.2); }
        .icon-kelola { background: linear-gradient(135deg, #003F63, #005B8F); color: white; box-shadow: 0 10px 20px rgba(0, 63, 99, 0.2); }
        .icon-riwayat { background: linear-gradient(135deg, #0A91BE, #31B0D5); color: white; box-shadow: 0 10px 20px rgba(10, 145, 190, 0.2); }
        .menu-card h3 { font-weight: 800; margin-bottom: 12px; font-size: 1.4rem; color: #003F63; position: relative; z-index: 2; }
        .menu-card p { color: #64748B; font-size: 0.9rem; margin: 0; line-height: 1.5; position: relative; z-index: 2; }
        .footer-custom { background-color: transparent; padding: 15px 0; text-align: center; font-size: 0.85rem; color: #64748B; margin-top: auto; border-top: 1px solid #EEF2F6; position: relative; z-index: 10; }
        .footer-custom span { font-weight: 600; color: #005B8F; }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="navbar-brand mb-0 logo-wrapper">
                <img src="img/logo-lokale.png" alt="Lokale Logo">
            </div>
            
            <div class="d-flex gap-3 align-items-center">
                <div class="user-badge shadow-sm">
                    <i class="bi bi-person-circle fs-6"></i>
                    <span><?= htmlspecialchars($nama_user) ?> (<?= $role ?>)</span>
                </div>
                <!-- Tombol baru -->
<a href="#" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-medium" onclick="konfirmasiLogout(event)"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="hero-section">
            <div class="badge-date shadow-sm">
                <i class="bi bi-calendar3 me-2"></i> <?= $tanggal ?>
            </div>
            <h1><?= $sapaan ?>, <?= htmlspecialchars($nama_user) ?>!</h1>
            <p class="text-secondary fs-6 mt-1 mb-0">Kamu login sebagai <strong><?= $role ?></strong>. Pilih menu operasional di bawah.</p>
        </div>

        <div class="container">
            <div class="row justify-content-center g-4">
                
                <?php if($role == 'Kasir'): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="transaksi.php" class="menu-card">
                        <div class="icon-wrapper icon-transaksi">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h3>Mulai Transaksi</h3>
                        <p>Catat pesanan (Dine-In/Takeaway), hitung total belanja otomatis, tambah poin member, dan cetak struk pembayaran.</p>
                    </a>
                </div>
                <?php endif; ?>
                
                <?php if($role == 'Admin'): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="kelola_menu.php" class="menu-card">
                        <div class="icon-wrapper icon-kelola">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <h3>Kelola Menu</h3>
                        <p>Akses sistem untuk menambah varian menu baru, memperbarui harga, atau menghapus produk dari daftar penjualan.</p>
                    </a>
                </div>
                <?php endif; ?>

                <?php if($role == 'Manajer'): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="riwayat_transaksi.php" class="menu-card">
                        <div class="icon-wrapper icon-riwayat">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h3>Riwayat Laporan</h3>
                        <p>Pantau seluruh catatan penjualan, lihat total pendapatan, dan cetak ulang struk pesanan pelanggan yang lalu.</p>
                    </a>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
   
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function konfirmasiLogout(e) {
            e.preventDefault();
            
            Swal.fire({
                title: '<span style="color:#003F63">Yakin ingin keluar?</span>',
                text: "Sesi kamu saat ini akan diakhiri.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#94A3B8',
                confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> Ya, Logout!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'border-0 shadow-lg rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php'; 
                }
            });
        }
    </script>
</body>
</html>

    <footer class="footer-custom">
        <div class="container">
            Mini Project IV - Sistem Point of Sale (PoS) <br>
            Dikembangkan Oleh <span>Kelompok 2</span>: Nelson Saputra, Bintang Andhara Putra, Gwenna Jasmine Farani.
        </div>
    </footer>
</body>
</html>