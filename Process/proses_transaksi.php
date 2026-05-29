<?php
// process/proses_transaksi.php
require_once '../config/database.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/index.php");
    exit;
}

date_default_timezone_set('Asia/Jakarta');


if (empty($_POST['id_menu']) || empty($_POST['qty'])) {
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
    
    
    if ($jumlah <= 0 || $jumlah > 99) {
        die("Terdeteksi manipulasi jumlah pesanan (Qty tidak valid pada menu ID: {$id_menu}).");
    }

   
    $stmt_harga = $conn->prepare("SELECT Harga FROM Menu WHERE Id_Menu = ?");
    $stmt_harga->execute([$id_menu]);
    $data_menu = $stmt_harga->fetch(PDO::FETCH_ASSOC);

  
    if (!$data_menu) {
        die("Menu dengan ID {$id_menu} tidak valid atau sudah dihapus.");
    }

    $harga_asli = $data_menu['Harga'];
    $subtotal_item = $harga_asli * $jumlah;
    $total_harga += $subtotal_item;
    
   
    $items_valid[] = [
        'id' => $id_menu,
        'qty' => $jumlah,
        'subtotal' => $subtotal_item
    ];
}

$nilai_pajak = $total_harga * 0.10;
$total_akhir = $total_harga + $nilai_pajak;

try {
    
    $conn->beginTransaction();

   
   // KODE BARU (Tambahkan Tipe_Pesanan)
    $stmt = $conn->prepare("INSERT INTO Transaksi (Id_Transaksi, Waktu_Transaksi, Id_Kasir, Id_Member, Tipe_Pesanan, Total_Harga, Nilai_Pajak, Total_Akhir) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_transaksi, $waktu, $id_kasir, $id_member, $tipe_pesanan, $total_harga, $nilai_pajak, $total_akhir]);

   
    $stmt_all_dt = $conn->query("SELECT Id_Detail FROM Detail_Transaksi");
    $existing_dt = $stmt_all_dt->fetchAll(PDO::FETCH_COLUMN);
    
    $stmt_detail = $conn->prepare("INSERT INTO Detail_Transaksi (Id_Detail, Id_Transaksi, Id_Menu, Jumlah_Beli, Total_Harga_Item) VALUES (?, ?, ?, ?, ?)");
    
    $urutan_dt = 1;
    
    foreach ($items_valid as $item) {
        do {
            $id_detail = 'DT' . str_pad($urutan_dt, 3, '0', STR_PAD_LEFT);
            $urutan_dt++;
        } while (in_array($id_detail, $existing_dt));
        
        $existing_dt[] = $id_detail;
        
        $stmt_detail->execute([$id_detail, $id_transaksi, $item['id'], $item['qty'], $item['subtotal']]);
    }

    if ($id_member != NULL) {
        $poin_didapat = floor($total_akhir / 10000); // 1 poin tiap Rp10.000
        $stmt_poin = $conn->prepare("UPDATE Member SET Jumlah_Poin = Jumlah_Poin + ? WHERE Id_Member = ?");
        $stmt_poin->execute([$poin_didapat, $id_member]);
    }

 
    $conn->commit();

 
    header("Location: ../public/struk.php?id=$id_transaksi&tipe=$tipe_pesanan");
    exit;

} catch(PDOException $e) {
    $conn->rollBack(); 
    die("Transaksi Gagal: " . $e->getMessage());
}
?>