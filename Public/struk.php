<?php
// public/struk.php
require_once '../config/database.php';


date_default_timezone_set('Asia/Jakarta');

$id_transaksi = $_GET['id'];


$stmt = $conn->prepare("
    SELECT t.*, k.Nama_Kasir, m.Nama_Member, m.Jumlah_Poin 
    FROM Transaksi t
    JOIN Kasir k ON t.Id_Kasir = k.Id_Kasir
    LEFT JOIN Member m ON t.Id_Member = m.Id_Member
    WHERE t.Id_Transaksi = ?
");
$stmt->execute([$id_transaksi]);
$transaksi = $stmt->fetch(PDO::FETCH_ASSOC);
$tipe_pesanan = $transaksi['Tipe_Pesanan'];

$stmt_detail = $conn->prepare("
    SELECT dt.*, m.Nama_Menu 
    FROM Detail_Transaksi dt
    JOIN Menu m ON dt.Id_Menu = m.Id_Menu
    WHERE dt.Id_Transaksi = ?
");
$stmt_detail->execute([$id_transaksi]);
$details = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);

$poin_baru = floor($transaksi['Total_Akhir'] / 10000);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - <?= htmlspecialchars($id_transaksi) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
    
    <style>
        body { 
            background-color: #F4F7FB; 
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

       
        .receipt-wrapper {
            width: 320px;
            background-color: #ffffff;
            padding: 25px 20px 40px;
            box-shadow: 0 10px 25px rgba(0, 91, 143, 0.1);
            position: relative;
            margin-bottom: 30px;
          
            font-family: 'Courier New', Courier, monospace; 
            font-size: 13px;
            color: #000000;
            line-height: 1.4;
        }

        /* Efek robekan kertas di bawah */
        .receipt-wrapper::after {
            content: "";
            position: absolute;
            bottom: -6px;
            left: 0;
            right: 0;
            height: 6px;
            background-size: 12px 12px;
            background-image: radial-gradient(circle at 6px 6px, transparent 7px, #ffffff 8px);
        }

       
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        
        .dashed-line {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }

        
        .store-name { font-size: 20px; font-weight: bold; letter-spacing: 1px; margin-bottom: 5px; }
        .store-info { font-size: 11px; margin-bottom: 10px; }

       
        .meta-table { width: 100%; margin-bottom: 10px; font-size: 12px; }
        .meta-table td { padding: 1px 0; vertical-align: top; }

       
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .item-table th { border-bottom: 1px dashed #000; padding-bottom: 5px; text-align: left; font-weight: normal; }
        .item-table td { padding: 5px 0; vertical-align: top; }
        .item-name { max-width: 140px; word-wrap: break-word; }

       
        .total-table { width: 100%; margin-top: 5px; }
        .total-table td { padding: 3px 0; }
        .grand-total { font-size: 15px; font-weight: bold; border-top: 1px dashed #000; border-bottom: 1px dashed #000; }
        .grand-total td { padding: 8px 0; }

        
        .member-info { margin-top: 15px; border: 1px solid #000; padding: 10px; text-align: center; border-radius: 4px; font-size: 12px; }

      
        .barcode { font-family: 'Libre Barcode 39', cursive; font-size: 38px; margin-top: 10px; line-height: 1; }
        .barcode-text { font-size: 11px; letter-spacing: 2px; }

        
        @media print {
            body { background-color: transparent; padding: 0; align-items: flex-start; }
            .receipt-wrapper { box-shadow: none; margin: 0; width: 100%; max-width: 300px; padding: 10px; }
            .receipt-wrapper::after { display: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="receipt-wrapper">
        <div class="text-center">
            <div class="store-name">LOKALE SELECT</div>
            <div class="store-info">
                Jl. Dr. Wahidin No. 29A, Kel. Sungai Jawi<br>
                Pontianak Kota, Kalimantan Barat 78243<br>
                IG: @lokaleid 
            </div>
        </div>

        <div class="dashed-line"></div>

        <table class="meta-table">
            <tr>
                <td width="25%">Order</td>
                <td width="5%">:</td>
                <td width="70%"><?= htmlspecialchars($transaksi['Id_Transaksi']) ?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td><?= date('d M Y H:i', strtotime($transaksi['Waktu_Transaksi'])) ?> WIB</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>:</td>
                <td><?= htmlspecialchars($transaksi['Nama_Kasir']) ?></td>
            </tr>
            <tr>
                <td>Tipe</td>
                <td>:</td>
                <td><?= htmlspecialchars($tipe_pesanan) ?></td>
            </tr>
        </table>

        <div class="dashed-line"></div>

        <table class="item-table">
            <thead>
                <tr>
                    <th width="50%">Item</th>
                    <th width="15%" class="text-center">Qty</th>
                    <th width="35%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($details as $d): ?>
                <tr>
                    <td class="item-name"><?= htmlspecialchars($d['Nama_Menu']) ?></td>
                    <td class="text-center"><?= $d['Jumlah_Beli'] ?>x</td>
                    <td class="text-right"><?= number_format($d['Total_Harga_Item'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="dashed-line"></div>

        <table class="total-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right"><?= number_format($transaksi['Total_Harga'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Tax (10%)</td>
                <td class="text-right"><?= number_format($transaksi['Nilai_Pajak'], 0, ',', '.') ?></td>
            </tr>
            <tr class="grand-total">
                <td>TOTAL</td>
                <td class="text-right">Rp <?= number_format($transaksi['Total_Akhir'], 0, ',', '.') ?></td>
            </tr>
        </table>

        <?php if($transaksi['Nama_Member']): ?>
        <div class="member-info">
            <div class="fw-bold">MEMBER LOKALE</div>
            <div><?= htmlspecialchars($transaksi['Nama_Member']) ?></div>
            <div class="dashed-line" style="margin: 5px 0;"></div>
            <div>Poin didapat  : +<?= $poin_baru ?></div>
            <div class="fw-bold">Total Poin : <?= $transaksi['Jumlah_Poin'] ?></div>
        </div>
        <?php endif; ?>

        <div class="text-center" style="margin-top: 20px;">
            <div>Terima kasih atas kunjungan Anda</div>
        </div>
    </div>

    <div class="no-print d-flex gap-3 mt-4">
        <a href="index.php" class="btn shadow-sm rounded-pill px-4 fw-medium text-white" style="background-color: #003F63;"><i class="bi bi-house-door me-2"></i>Dashboard</a>
        <a href="transaksi.php" class="btn shadow-sm rounded-pill px-4 fw-medium text-white" style="background-color: #0A91BE;"><i class="bi bi-arrow-counterclockwise me-2"></i>Transaksi Baru</a>
        <button onclick="window.print()" class="btn shadow-sm rounded-pill px-4 fw-medium text-white" style="background-color: #07779D;"><i class="bi bi-printer me-2"></i>Cetak Struk</button>
    </div>

</body>
</html>