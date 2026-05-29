<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';
?>

<!-- public/tambah.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu - Lokale Select</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F4F7FB; color: #1E293B; }
        
        .navbar-custom { background: linear-gradient(135deg, #005B8F, #07779D); padding: 18px 0; box-shadow: 0 4px 15px rgba(0, 91, 143, 0.15); }
        .logo-wrapper { background-color: #FFFFFF; padding: 8px 25px; border-radius: 12px; display: inline-flex; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); pointer-events: none; }
        .logo-wrapper img { height: 42px; object-fit: contain; }
        
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 91, 143, 0.08); overflow: hidden; border-top: 6px solid #07779D; background: white;}
        .form-label { font-weight: 600; color: #003F63; font-size: 0.95rem; margin-bottom: 8px;}
        .form-control, .form-select, .input-group-text { border-radius: 10px; padding: 12px 18px; border: 1px solid #CBD5E1; background-color: #F8FAFC; color: #334155; transition: all 0.3s;}
        .form-control:focus, .form-select:focus { border-color: #07779D; box-shadow: 0 0 0 4px rgba(7, 119, 157, 0.1); background-color: #FFFFFF;}
        
        .btn-custom { background: linear-gradient(135deg, #07779D, #0A91BE); color: white; border: none; border-radius: 10px; font-weight: 600; padding: 14px 24px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(7, 119, 157, 0.2); }
        .btn-custom:hover { background: linear-gradient(135deg, #005B8F, #07779D); color: white; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(7, 119, 157, 0.3); }
        .btn-back { background: #FFFFFF; color: #64748B; border: 1px solid #CBD5E1; font-weight: 600; transition: all 0.3s; border-radius: 10px; padding: 14px 24px;}
        .btn-back:hover { background: #F1F5F9; color: #0F172A; }
        .btn-nav-back { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s;}
        .btn-nav-back:hover { background: rgba(255,255,255,0.2); color: white;}
    </style>
</head>
<body>

    <nav class="navbar navbar-custom mb-5">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <div class="navbar-brand mb-0 logo-wrapper">
                <img src="img/logo-lokale.png" alt="Lokale Logo">
            </div>
            <a href="kelola_menu.php" class="btn btn-nav-back fw-medium rounded-pill px-4"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="card card-custom">
                    <div class="card-header bg-white border-0 pt-5 pb-3 px-5 d-flex align-items-center gap-4">
                        <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm flex-shrink-0" style="width: 65px; height: 65px; background: #E1EFF6; color: #07779D;">
                            <i class="bi bi-plus-lg fs-2"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1" style="color: #003F63;">Tambah Menu</h4>
                            <p class="text-secondary small mb-0">Lengkapi formulir di bawah ini untuk menambahkan produk baru ke dalam sistem.</p>
                        </div>
                    </div>
                    <div class="card-body px-5 pb-5">
                        <form action="../process/insert.php" method="POST">
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">ID Menu</label>
                                    <input type="text" name="id_menu" class="form-control" required maxlength="5" placeholder="Contoh: MN022">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kategori</label>
                                    <select name="kategori" class="form-select" required>
                                        <option value="" disabled selected>Pilih Kategori...</option>
                                        <option value="Coffee">Coffee</option>
                                        <option value="Main Dish">Main Dish</option>
                                        <option value="Dessert">Dessert</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-4 mb-5">
                                <div class="col-md-7">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" name="nama_menu" class="form-control" required maxlength="100" placeholder="Masukkan nama menu">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Harga Produk</label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-medium text-secondary">Rp</span>
                                        <input type="number" name="harga" class="form-control" required step="0.01" placeholder="0">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-3 pt-4 border-top">
                                <a href="kelola_menu.php" class="btn btn-back">Batal</a>
                                <button type="submit" class="btn btn-custom"><i class="bi bi-cloud-arrow-up me-2"></i>Simpan Produk Baru</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php 
if(isset($_GET['status'])): 
    $status = $_GET['status'];
    $title = ""; $text = ""; $icon = "error";

    if($status == 'duplicate_id') { 
        $title = "ID Sudah Ada!"; 
        $text = "ID Menu ini sudah digunakan oleh produk lain. Silakan gunakan ID yang berbeda."; 
    }
    else if($status == 'duplicate_name') { 
        $title = "Nama Sudah Ada!"; 
        $text = "Nama menu ini sudah terdaftar. Gunakan nama yang lain."; 
    }
    
    if($title != ""):
?>
<script>
    Swal.fire({
        title: '<?= $title ?>',
        text: '<?= $text ?>',
        icon: '<?= $icon ?>',
        confirmButtonColor: '#07779D',
        confirmButtonText: 'Mengerti',
        backdrop: `rgba(0, 91, 143, 0.2)`
    });
    window.history.replaceState(null, null, window.location.pathname);
</script>
<?php endif; endif; ?>
</body>
</html>