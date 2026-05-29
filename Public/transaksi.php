<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Kasir') {
    header("Location: index.php");
    exit;
}
require_once '../config/database.php';
// public/transaksi.php
require_once '../config/database.php';

// Ambil data untuk pilihan di form
$kasirs = $conn->query("SELECT * FROM Kasir")->fetchAll(PDO::FETCH_ASSOC);
$members = $conn->query("SELECT * FROM Member")->fetchAll(PDO::FETCH_ASSOC);
// Filter agar HANYA menu yang aktif (1) yang tampil di sistem kasir
$menus = $conn->query("SELECT * FROM Menu WHERE Status_Aktif = 1")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Kasir - Lokale Select</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F4F7FB; color: #1E293B; }
        .navbar-custom { background: linear-gradient(135deg, #005B8F, #07779D); padding: 18px 0; box-shadow: 0 4px 15px rgba(0, 91, 143, 0.15); z-index: 1050; }
        .logo-wrapper { background-color: #FFFFFF; padding: 8px 25px; border-radius: 12px; display: inline-flex; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); pointer-events: none; }
        .logo-wrapper img { height: 42px; object-fit: contain; }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 91, 143, 0.08); background: #FFFFFF; border-top: 6px solid #07779D; }
        .btn-custom { background: linear-gradient(135deg, #07779D, #0A91BE); color: white; border: none; border-radius: 10px; font-weight: 600; transition: all 0.3s ease; }
        .btn-custom:hover { background: linear-gradient(135deg, #005B8F, #07779D); color: white; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(7, 119, 157, 0.3); }
        .form-label { font-weight: 600; color: #003F63; font-size: 0.95rem; margin-bottom: 8px; }
        .form-control, .form-select { border-radius: 10px; padding: 12px 18px; border: 1px solid #CBD5E1; background-color: #F8FAFC; color: #334155; }
        .form-control:focus, .form-select:focus { border-color: #07779D; box-shadow: 0 0 0 4px rgba(7, 119, 157, 0.1); background-color: #FFFFFF; }
        .table-cart th { background-color: #005B8F; color: white; border: none; font-weight: 600; padding: 15px; text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem;}
        .table-cart td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #EEF2F6; }
        .grand-total-box { background-color: #E1EFF6; border-radius: 12px; padding: 15px 20px; border: 2px dashed #07779D; }
        .btn-nav-back { background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s;}
        .btn-nav-back:hover { background: rgba(255,255,255,0.2); color: white;}
        /* Swal Custom Font */
        div:where(.swal2-container) { font-family: 'Poppins', sans-serif !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom mb-4 sticky-top">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <div class="navbar-brand mb-0 logo-wrapper"><img src="img/logo-lokale.png" alt="Lokale Logo"></div>
            <a href="index.php" class="btn btn-nav-back rounded-pill px-4 fw-medium"><i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container-fluid px-4 pb-5">
        <form action="../process/proses_transaksi.php" method="POST" id="formTransaksi">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card card-custom h-100">
                        <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                            <h5 class="fw-bold mb-0" style="color: #003F63;"><i class="bi bi-person-badge me-2"></i>Detail Transaksi</h5>
                        </div>
                        <div class="card-body px-4">
                            <div class="mb-3">
                                <label class="form-label">Tipe Pesanan</label>
                                <select name="tipe_pesanan" class="form-select" required>
                                    <option value="Dine In">🍽️ Dine In (Makan di Tempat)</option>
                                    <option value="Takeaway">🛍️ Takeaway (Bawa Pulang)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Kasir</label>
                                <select name="id_kasir" class="form-select" required>
                                    <option value="" disabled selected>Pilih Kasir yang bertugas...</option>
                                    <?php foreach($kasirs as $k): ?>
                                        <option value="<?= $k['Id_Kasir'] ?>"><?= htmlspecialchars($k['Nama_Kasir']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Pelanggan (Member)</label>
                                <select name="id_member" class="form-select">
                                    <option value="">-- Pelanggan Umum (Non-Member) --</option>
                                    <?php foreach($members as $m): ?>
                                        <option value="<?= $m['Id_Member'] ?>"><?= htmlspecialchars($m['Nama_Member']) ?> (Poin: <?= $m['Jumlah_Poin'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text text-success fw-medium"><i class="bi bi-info-circle"></i> Kelipatan Rp10.000 = 1 Poin.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card card-custom h-100">
                        <div class="card-body p-4">
                            
                            <div class="row g-2 mb-4 align-items-end" style="background: #F8FAFC; padding: 20px; border-radius: 12px; border: 1px solid #CBD5E1;">
                                <div class="col-md-7">
                                    <label class="form-label">Pilih Menu</label>
                                    <select id="pilih_menu" class="form-select">
                                        <option value="" disabled selected>Cari dan pilih menu...</option>
                                        <?php foreach($menus as $mn): ?>
                                            <option value="<?= $mn['Id_Menu'] ?>" data-nama="<?= htmlspecialchars($mn['Nama_Menu']) ?>" data-harga="<?= $mn['Harga'] ?>">
                                                <?= htmlspecialchars($mn['Nama_Menu']) ?> - Rp <?= number_format($mn['Harga'],0,',','.') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Qty</label>
                                    <input type="number" id="qty" class="form-control" value="1" min="1">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-custom w-100 py-2" onclick="tambahKeKeranjang()">
                                        <i class="bi bi-cart-plus-fill me-1"></i> Tambah
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive rounded-3 border mb-4">
                                <table class="table table-hover table-cart mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nama Menu</th>
                                            <th>Harga</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Subtotal</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="keranjang_body"></tbody>
                                </table>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-1 text-muted fw-medium">
                                        <span>Subtotal Harga:</span><span id="display_total">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 text-muted fw-medium">
                                        <span>Pajak (10%):</span><span id="display_pajak">Rp 0</span>
                                    </div>
                                    <div class="grand-total-box d-flex justify-content-between align-items-center">
                                        <span class="fs-5 fw-bold" style="color: #005B8F;">TOTAL BAYAR</span>
                                        <span class="fs-4 fw-bold" style="color: #005B8F;" id="display_grand">Rp 0</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3 mt-md-0 d-flex flex-column gap-2">
                                    <button type="submit" class="btn btn-success fw-bold py-3 fs-5 shadow-sm rounded-3">
                                        <i class="bi bi-check2-circle me-2"></i>Proses Pembayaran
                                    </button>
                                    <button type="reset" class="btn btn-outline-danger fw-bold py-2 rounded-3" onclick="document.getElementById('keranjang_body').innerHTML=''; hitungTotal();">
                                        <i class="bi bi-trash3 me-1"></i> Kosongkan Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function tambahKeKeranjang() {
            let menu = document.getElementById('pilih_menu');
            let qtyInput = document.getElementById('qty');
            let qty = parseInt(qtyInput.value);

            // --- VALIDASI DENGAN SWEETALERT2 ---
            if (menu.value === "") {
                Swal.fire({
                    title: 'Menu Belum Dipilih!',
                    text: 'Silakan pilih menu terlebih dahulu sebelum menambah pesanan.',
                    icon: 'warning',
                    confirmButtonColor: '#07779D',
                    confirmButtonText: 'Kembali',
                    borderRadius: '16px'
                });
                return;
            }

            if (isNaN(qty) || qty <= 0) {
                Swal.fire({
                    title: 'Jumlah Tidak Valid!',
                    text: 'Jumlah pesanan minimal adalah 1.',
                    icon: 'error',
                    confirmButtonColor: '#07779D',
                    confirmButtonText: 'Kembali',
                    borderRadius: '16px'
                });
                return;
            }

            if (qty > 99) {
    Swal.fire({
        title: 'Batas Maksimal!',
        text: 'Maksimal pembelian untuk satu jenis menu adalah 99 item.',
        icon: 'warning',
        confirmButtonColor: '#07779D',
        borderRadius: '16px'
    });
    return;
}
            if (qty > 99) {
                Swal.fire({
                    title: 'Batas Maksimal!',
                    text: 'Maksimal pembelian untuk satu jenis menu adalah 99 item.',
                    icon: 'warning',
                    confirmButtonColor: '#07779D',
                    borderRadius: '16px'
                });
    return;
}
            // --- AKHIR VALIDASI ---

            let nama = menu.options[menu.selectedIndex].getAttribute('data-nama');
            let harga = parseFloat(menu.options[menu.selectedIndex].getAttribute('data-harga'));
            let subtotal = harga * qty;

            let tbody = document.getElementById('keranjang_body');
            let tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-semibold" style="color: #003F63;">${nama} <input type='hidden' name='id_menu[]' value='${menu.value}'></td>
                <td>Rp ${harga.toLocaleString('id-ID')} <input type='hidden' name='harga[]' value='${harga}'></td>
                <td class="text-center">${qty} <input type='hidden' name='qty[]' value='${qty}'></td>
                <td class='text-end fw-semibold subtotal-item' style="color: #07779D;" data-subtotal='${subtotal}'>Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="this.closest('tr').remove(); hitungTotal();">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            hitungTotal();
            
            // Reset input setelah tambah
            menu.value = "";
            qtyInput.value = "1";
        }

        function hitungTotal() {
            let items = document.querySelectorAll('.subtotal-item');
            let total_harga = 0;
            items.forEach(item => { 
                total_harga += parseFloat(item.getAttribute('data-subtotal')); 
            });

            let pajak = total_harga * 0.10; 
            let grand_total = total_harga + pajak;

            document.getElementById('display_total').innerText = "Rp " + total_harga.toLocaleString('id-ID');
            document.getElementById('display_pajak').innerText = "Rp " + pajak.toLocaleString('id-ID');
            document.getElementById('display_grand').innerText = "Rp " + grand_total.toLocaleString('id-ID');
        }

        // Validasi saat form di-submit jika keranjang kosong
        document.getElementById('formTransaksi').addEventListener('submit', function(e) {
            let items = document.querySelectorAll('.subtotal-item');
            if(items.length === 0) {
                e.preventDefault(); // Menghentikan form agar tidak pindah halaman
                Swal.fire({
                    title: 'Keranjang Kosong!',
                    text: 'Tidak ada pesanan untuk diproses. Silakan tambah menu terlebih dahulu.',
                    icon: 'error',
                    confirmButtonColor: '#07779D',
                    borderRadius: '16px'
                });
            }
        });
    </script>
</body>
</html>