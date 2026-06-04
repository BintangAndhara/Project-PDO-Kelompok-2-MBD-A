<?php
// process/proses_transaksi.php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/index.php");
    exit;
}

date_default_timezone_set('Asia/Jakarta');

if (empty($_POST['id_menu']) || empty($_POST['qty']) || empty($_POST['ukuran'])) {
    die("Keranjang kosong atau data tidak lengkap! Silakan kembali dan pilih menu.");
}

if (count($_POST['id_menu']) !== count($_POST['qty'])) {
    die("Terdeteksi manipulasi struktur form pesanan.");
}

$id_kasir = $_POST['id_kasir'];
$id_member = empty($_POST['id_member']) ? NULL : $_POST['id_member'];
$tipe_pesanan = $_POST['tipe_pesanan']; 
$waktu = date('Y-m-d H:i:s');

$stmt_all_tr = $conn->query("SELECT Id_Transaksi FROM Transaksi");
$existing_tr = $stmt_all_tr->fetchAll(PDO::FETCH_COLUMN);

$urutan_tr = 1;
do {
    $id_transaksi = 'TR' . str_pad($urutan_tr, 3, '0', STR_PAD_LEFT);
    $urutan_tr++;
} while (in_array($id_transaksi, $existing_tr));

$total_harga = 0;
$items_valid = []; 

for ($i = 0; $i < count($_POST['id_menu']); $i++) {
    $id_menu = $_POST['id_menu'][$i];
    $jumlah = (int)$_POST['qty'][$i];
    $ukuran = $_POST['ukuran'][$i];
    
    if ($jumlah <= 0 || $jumlah > 99) {
        die("Terdeteksi manipulasi jumlah pesanan.");
    }

    // Ambil Nama_Menu dan Harga dari database untuk disimpan sebagai Snapshot
    $stmt_harga = $conn->prepare("SELECT Nama_Menu, Harga_Reguler, Harga_Large FROM Menu WHERE Id_Menu = ?");
    $stmt_harga->execute([$id_menu]);
    $data_menu = $stmt_harga->fetch(PDO::FETCH_ASSOC);

    if (!$data_menu) {
        die("Menu dengan ID {$id_menu} tidak valid atau sudah dihapus.");
    }

    // Set harga berdasarkan ukuran yang dipilih kasir
    $harga_asli = ($ukuran === 'Large') ? $data_menu['Harga_Large'] : $data_menu['Harga_Reguler'];
    $subtotal_item = $harga_asli * $jumlah;
    $total_harga += $subtotal_item;
    
    // Simpan snapshot nama dan harga ke array validasi
    $items_valid[] = [
        'id' => $id_menu,
        'nama_snapshot' => $data_menu['Nama_Menu'],
        'qty' => $jumlah,
        'ukuran' => $ukuran,
        'harga_snapshot' => $harga_asli,
        'subtotal' => $subtotal_item
    ];
}

$nilai_pajak = $total_harga * 0.10;
$total_akhir = $total_harga + $nilai_pajak;

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("INSERT INTO Transaksi (Id_Transaksi, Waktu_Transaksi, Id_Kasir, Id_Member, Tipe_Pesanan, Total_Harga, Nilai_Pajak, Total_Akhir) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_transaksi, $waktu, $id_kasir, $id_member, $tipe_pesanan, $total_harga, $nilai_pajak, $total_akhir]);

    $stmt_all_dt = $conn->query("SELECT Id_Detail FROM Detail_Transaksi");
    $existing_dt = $stmt_all_dt->fetchAll(PDO::FETCH_COLUMN);
    
    // Query INSERT detail transaksi dengan kolom snapshot yang baru
    $stmt_detail = $conn->prepare("INSERT INTO Detail_Transaksi (Id_Detail, Id_Transaksi, Id_Menu, Nama_Menu_Snapshot, Ukuran, Harga_Snapshot, Jumlah_Beli, Total_Harga_Item) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $urutan_dt = 1;
    foreach ($items_valid as $item) {
        do {
            $id_detail = 'DT' . str_pad($urutan_dt, 3, '0', STR_PAD_LEFT);
            $urutan_dt++;
        } while (in_array($id_detail, $existing_dt));
        
        $existing_dt[] = $id_detail;
        
        // Eksekusi parameter dengan data snapshot
        $stmt_detail->execute([$id_detail, $id_transaksi, $item['id'], $item['nama_snapshot'], $item['ukuran'], $item['harga_snapshot'], $item['qty'], $item['subtotal']]);
    }

    if ($id_member != NULL) {
        $poin_didapat = floor($total_akhir / 10000);
        $stmt_poin = $conn->prepare("UPDATE Member SET Jumlah_Poin = Jumlah_Poin + ? WHERE Id_Member = ?");
        $stmt_poin->execute([$poin_didapat, $id_member]);
    }

    $conn->commit();
    header("Location: ../public/struk.php?id=$id_transaksi");
    exit;

} catch(PDOException $e) {
    $conn->rollBack(); 
    die("Transaksi Gagal: " . $e->getMessage());
}
?>