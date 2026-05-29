<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';
// public/kelola_menu.php
require_once '../config/database.php';


$stmt = $conn->query("SELECT * FROM Menu WHERE Status_Aktif = 1 ORDER BY Id_Menu DESC");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


$tot_menu = $conn->query("SELECT COUNT(*) FROM Menu WHERE Status_Aktif = 1")->fetchColumn();
$tot_coffee = $conn->query("SELECT COUNT(*) FROM Menu WHERE Kategori='Coffee' AND Status_Aktif = 1")->fetchColumn();
$tot_main = $conn->query("SELECT COUNT(*) FROM Menu WHERE Kategori='Main Dish' AND Status_Aktif = 1")->fetchColumn();
$tot_dessert = $conn->query("SELECT COUNT(*) FROM Menu WHERE Kategori='Dessert' AND Status_Aktif = 1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Lokale Select</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #F4F7FB; 
            color: #1E293B; 
            padding-bottom: 80px; 
        }
        .navbar-custom { 
            background: linear-gradient(135deg, #005B8F, #07779D); 
            padding: 18px 0; 
            box-shadow: 0 4px 15px rgba(0, 91, 143, 0.15);
            z-index: 1050; 
        }
        .logo-wrapper { background-color: #FFFFFF; padding: 8px 25px; border-radius: 12px; display: inline-flex; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); pointer-events: none; user-select: none; }
        .logo-wrapper img { height: 42px; object-fit: contain; }
        
        .stat-card {
            border: none; border-radius: 20px; color: white; padding: 25px; display: flex;
            align-items: center; gap: 20px; box-shadow: 0 10px 30px rgba(7, 119, 157, 0.1); 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; overflow: hidden;
        }
        .stat-card::after {
            content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.15) 50%, rgba(255,255,255,0) 100%);
            transform: skewX(-25deg); transition: all 0.7s ease;
        }
        .stat-card:hover::after { left: 200%; }
        .stat-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(7, 119, 157, 0.2); }
        .stat-icon { font-size: 2.8rem; opacity: 0.9; }
        
        .bg-total { background: linear-gradient(135deg, #003F63, #005B8F); }
        .bg-coffee { background: linear-gradient(135deg, #07779D, #0A91BE); }
        .bg-main { background: linear-gradient(135deg, #0A91BE, #31B0D5); }
        .bg-dessert { background: linear-gradient(135deg, #E1EFF6, #FFFFFF); color: #005B8F; border: 1px solid #D4EBF8; }
        .bg-dessert .stat-icon { color: #07779D; }

        .card-table { border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); overflow: hidden; background: #FFFFFF; }
        
        .table-scroll-wrapper { max-height: 400px; overflow-y: auto; }
        .table-scroll-wrapper::-webkit-scrollbar { width: 8px; }
        .table-scroll-wrapper::-webkit-scrollbar-track { background: #F8FAFC; border-radius: 0 20px 20px 0; }
        .table-scroll-wrapper::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
        .table-scroll-wrapper::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        .table-hover tbody tr:hover { background-color: #F8FAFC; }
        
        th { 
            background-color: #005B8F !important; color: #FFFFFF !important; font-weight: 600; 
            text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; padding: 18px 15px !important; 
            border: none; position: sticky; top: 0; z-index: 10;
        }
        td { padding: 18px 15px !important; vertical-align: middle; border-bottom: 1px solid #EEF2F6; color: #334155; }
        
        .btn-custom { background: linear-gradient(135deg, #07779D, #0A91BE); color: white; border: none; border-radius: 10px; font-weight: 500; padding: 12px 24px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(7, 119, 157, 0.2); }
        .btn-custom:hover { background: linear-gradient(135deg, #005B8F, #07779D); color: white; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(7, 119, 157, 0.3); }
        .btn-nav-back { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s;}
        .btn-nav-back:hover { background: rgba(255,255,255,0.2); color: white;}
        
        .btn-action { border-radius: 8px; font-weight: 500; padding: 6px 16px; transition: all 0.2s; }
        .btn-action:hover { transform: translateY(-2px); }
        .btn-edit-custom { background: linear-gradient(135deg, #003F63, #005B8F); color: #FFFFFF; border: none; }
        .btn-edit-custom:hover { background: linear-gradient(135deg, #002B44, #003F63); color: #FFFFFF; box-shadow: 0 4px 10px rgba(0, 63, 99, 0.2); }
        
        .badge-kategori { font-weight: 600; padding: 8px 14px; border-radius: 8px; font-size: 0.8rem; letter-spacing: 0.5px; }
        .badge-coffee { background-color: #E1EFF6; color: #005B8F; }
        .badge-main { background-color: #D4EBF8; color: #07779D; }
        .badge-dessert { background-color: #F0F8FF; color: #0A91BE; border: 1px solid #D4EBF8; }

        .footer-custom {
            background-color: #FFFFFF; padding: 14px 0; border-top: 1px solid #EEF2F6;
            box-shadow: 0 -5px 20px rgba(0, 91, 143, 0.05); text-align: center;
            position: fixed; bottom: 0; left: 0; width: 100%; z-index: 1040;
        }
        .footer-custom span { color: #005B8F; font-weight: 600; }
        div:where(.swal2-container) { font-family: 'Poppins', sans-serif !important; }
    </style>
</head>
<body>

    <nav class="navbar navbar-custom sticky-top mb-5">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <div class="navbar-brand mb-0 logo-wrapper">
                <img src="img/logo-lokale.png" alt="Lokale Logo">
            </div>
            <a href="index.php" class="btn btn-nav-back fw-medium rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container-fluid px-4 pb-4">
        
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
            <div>
                <h3 class="fw-bold mb-1" style="color: #003F63;"><i class="bi bi-grid-1x2-fill me-2 opacity-75"></i>Manajemen Menu</h3>
                <p class="text-secondary small mb-0 ms-1">Monitor statistik dan kelola ketersediaan produk Lokale Select.</p>
            </div>
            <a href="tambah.php" class="btn btn-custom fs-6"><i class="bi bi-plus-lg me-2"></i>Tambah Menu Baru</a>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card bg-total">
                    <i class="bi bi-box-seam stat-icon"></i>
                    <div>
                        <h3 class="fw-bold mb-0 fs-2"><?= $tot_menu ?></h3>
                        <div class="small opacity-75 fw-medium">Total Menu Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card bg-coffee">
                    <i class="bi bi-cup-hot stat-icon"></i>
                    <div>
                        <h3 class="fw-bold mb-0 fs-2"><?= $tot_coffee ?></h3>
                        <div class="small opacity-75 fw-medium">Varian Coffee</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card bg-main">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="stat-icon">
                        <path d="M2 12h20c0 5.52-4.48 10-10 10S2 17.52 2 12Z"/>
                        <path d="M12 3v4"/><path d="M8 4v3"/><path d="M16 4v3"/>
                    </svg>
                    <div>
                        <h3 class="fw-bold mb-0 fs-2"><?= $tot_main ?></h3>
                        <div class="small opacity-75 fw-medium">Varian Main Dish</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card bg-dessert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="stat-icon">
                        <path d="M20 21v-8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8"/>
                        <path d="M4 16s.5-1 2-1 2.5 2 4 2 2.5-2 4-2 2.5 2 4 2 2-1 2-1"/>
                        <path d="M2 21h20"/><path d="M7 8v3"/><path d="M12 8v3"/><path d="M17 8v3"/>
                        <path d="M7 4h.01"/><path d="M12 4h.01"/><path d="M17 4h.01"/>
                    </svg>
                    <div>
                        <h3 class="fw-bold mb-0 fs-2"><?= $tot_dessert ?></h3>
                        <div class="small opacity-75 fw-medium">Varian Dessert</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-table">
            <div class="card-body p-0">
                <div class="table-scroll-wrapper">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="10%">ID Menu</th>
                                <th width="20%">Kategori</th>
                                <th width="35%">Nama Produk</th>
                                <th width="20%">Harga</th>
                                <th class="text-center" width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data) > 0): ?>
                                <?php foreach ($data as $row): 
                                    $badge_class = 'badge-coffee';
                                    if($row['Kategori'] == 'Main Dish') $badge_class = 'badge-main';
                                    if($row['Kategori'] == 'Dessert') $badge_class = 'badge-dessert';
                                ?>
                                <tr>
                                    <td class="text-center fw-bold" style="color: #64748B;"><?= htmlspecialchars($row['Id_Menu']); ?></td>
                                    <td><span class="badge badge-kategori <?= $badge_class ?>"><?= htmlspecialchars($row['Kategori']); ?></span></td>
                                    <td class="fw-bold" style="color: #003F63; font-size: 1.05rem;"><?= htmlspecialchars($row['Nama_Menu']); ?></td>
                                    <td class="fw-semibold" style="color: #07779D;">Rp <?= number_format($row['Harga'], 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="edit.php?id=<?= $row['Id_Menu']; ?>" class="btn btn-sm btn-edit-custom btn-action shadow-sm" title="Edit"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                                            <button type="button" class="btn btn-sm btn-light text-danger border btn-action shadow-sm" title="Hapus" onclick="konfirmasiHapus('<?= $row['Id_Menu']; ?>', '<?= htmlspecialchars(addslashes($row['Nama_Menu'])); ?>')"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Belum ada menu yang aktif.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>

    <footer class="footer-custom">
        <div class="container">
            <p class="mb-1 fw-medium" style="color: #1E293B;">Mini Project III - Sistem Point of Sale (PoS)</p>
            <p class="mb-0 small text-muted">Dikembangkan Oleh <span>Kelompok 2</span>: Nelson Saputra, Bintang Andhara Putra, Gwenna Jasmine Farani.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function konfirmasiHapus(id, namaMenu) {
            Swal.fire({
                title: '<span style="color:#003F63">Hapus Menu?</span>',
                html: `Apakah kamu yakin ingin menyembunyikan <b>${namaMenu}</b> dari daftar kasir?<br><span style="color:#EF4444; font-size:14px; font-weight:500;">(Riwayat transaksi lama tetap aman).</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#94A3B8',
                confirmButtonText: '<i class="bi bi-eye-slash me-1"></i> Ya, Sembunyikan!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                borderRadius: '20px',
                customClass: { popup: 'border-0 shadow-lg' }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `hapus.php?id=${id}`;
                }
            });
        }
    </script>

    <?php 
    if(isset($_GET['status'])): 
        $status = $_GET['status'];
        $title = ""; $text = "";

        if($status == 'sukses') { $title = "Berhasil!"; $text = "Menu baru telah ditambahkan."; }
        else if($status == 'updated') { $title = "Diperbarui!"; $text = "Data menu berhasil disimpan."; }
        else if($status == 'deleted') { $title = "Terhapus!"; $text = "Menu berhasil dinonaktifkan dari sistem."; }
        
        if($title != ""):
    ?>
    <script>
        Swal.fire({
            title: '<?= $title ?>',
            text: '<?= $text ?>',
            icon: 'success',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            customClass: { popup: 'rounded-4 shadow-sm border border-light' }
        });
        window.history.replaceState(null, null, window.location.pathname);
    </script>
    <?php endif; endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>