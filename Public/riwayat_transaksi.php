
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manajer') {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';

// public/riwayat_transaksi.php
require_once '../config/database.php';

$stmt = $conn->query("
    SELECT t.Id_Transaksi, t.Waktu_Transaksi, k.Nama_Kasir, m.Nama_Member, t.Tipe_Pesanan, t.Total_Akhir 
    FROM Transaksi t
    LEFT JOIN Kasir k ON t.Id_Kasir = k.Id_Kasir
    LEFT JOIN Member m ON t.Id_Member = m.Id_Member
    ORDER BY t.Waktu_Transaksi DESC
");
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tot_transaksi = count($riwayat);
$tot_pendapatan = 0;
foreach($riwayat as $r) {
    $tot_pendapatan += $r['Total_Akhir'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Lokale Select</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
        
        /* Statistik Cards */
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
        .bg-revenue { background: linear-gradient(135deg, #07779D, #0A91BE); }

        /* Tabel Penyesuaian */
        .card-table { border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); overflow: hidden; background: #FFFFFF; }
        .table-scroll-wrapper { max-height: 500px; overflow-y: auto; }
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
        td { padding: 15px !important; vertical-align: middle; border-bottom: 1px solid #EEF2F6; color: #334155; }
        
        .btn-nav-back { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s;}
        .btn-nav-back:hover { background: rgba(255,255,255,0.2); color: white;}
        
        .footer-custom {
            background-color: #FFFFFF; padding: 14px 0; border-top: 1px solid #EEF2F6;
            box-shadow: 0 -5px 20px rgba(0, 91, 143, 0.05); text-align: center;
            position: fixed; bottom: 0; left: 0; width: 100%; z-index: 1040;
        }
        .footer-custom span { color: #005B8F; font-weight: 600; }
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
                <h3 class="fw-bold mb-1" style="color: #003F63;"><i class="bi bi-clock-history me-2 opacity-75"></i>Riwayat Transaksi</h3>
                <p class="text-secondary small mb-0 ms-1">Pantau seluruh catatan penjualan dan cetak ulang struk pesanan pelanggan.</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="stat-card bg-total">
                    <i class="bi bi-receipt stat-icon"></i>
                    <div>
                        <h3 class="fw-bold mb-0 fs-2"><?= number_format($tot_transaksi, 0, ',', '.') ?></h3>
                        <div class="small opacity-75 fw-medium">Total Transaksi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card bg-revenue">
                    <i class="bi bi-cash-coin stat-icon"></i>
                    <div>
                        <h3 class="fw-bold mb-0 fs-2">Rp <?= number_format($tot_pendapatan, 0, ',', '.') ?></h3>
                        <div class="small opacity-75 fw-medium">Total Pendapatan Akhir</div>
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
                                <th class="text-center" width="12%">ID Order</th>
                                <th width="20%">Tanggal & Waktu</th>
                                <th width="20%">Nama Kasir</th>
                                <th width="20%">Pelanggan (Member)</th>
                                <th width="15%">Tipe Pesanan</th>
                                <th width="18%">Total Akhir</th>
                                <th class="text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                       <tbody>
    <?php if (count($riwayat) > 0): ?>
        <?php foreach ($riwayat as $row): ?>
        <tr>
            <td class="text-center fw-bold" style="color: #64748B;"><?= htmlspecialchars($row['Id_Transaksi']); ?></td>
            <td><?= date('d M Y - H:i', strtotime($row['Waktu_Transaksi'])); ?> WIB</td>
            <td class="fw-medium" style="color: #003F63;"><?= htmlspecialchars($row['Nama_Kasir']); ?></td>
            <td>
                <?php if ($row['Nama_Member']): ?>
                    <span class="fw-medium" style="color: #07779D;"><?= htmlspecialchars($row['Nama_Member']); ?></span>
                <?php else: ?>
                    <span class="badge bg-light text-secondary border">Non-Member</span>
                <?php endif; ?>
            </td>
            
            <td>
                <?php if ($row['Tipe_Pesanan'] == 'Takeaway'): ?>
                    <span class="badge bg-warning text-dark"><i class="bi bi-bag"></i> Takeaway</span>
                <?php else: ?>
                    <span class="badge bg-success"><i class="bi bi-cup-straw"></i> Dine In</span>
                <?php endif; ?>
            </td>
            <td class="fw-bold" style="color: #005B8F;">Rp <?= number_format($row['Total_Akhir'], 0, ',', '.'); ?></td>
            <td class="text-center">
                <a href="struk.php?id=<?= $row['Id_Transaksi']; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" title="Lihat/Cetak Struk">
                    <i class="bi bi-printer me-1"></i>Struk
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" class="text-center py-5 text-muted">Belum ada data transaksi.</td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>