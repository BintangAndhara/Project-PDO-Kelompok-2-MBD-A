<?php
// process/insert.php
require_once '../config/database.php';

if ($_POST['harga_reguler'] <= 0 || $_POST['harga_large'] <= 0) { die("Harga tidak valid"); }

$id_menu = $_POST['id_menu'];
$nama_menu = $_POST['nama_menu'];
$kategori = $_POST['kategori'];
// Konversi koma ke titik (jika kasir ngetik pake koma manual)
$harga_reguler = str_replace(',', '.', $_POST['harga_reguler']);
$harga_large = str_replace(',', '.', $_POST['harga_large']);

try {
    // Cek apakah ID atau Nama menu sudah pernah ada di database
    $check = $conn->prepare("SELECT * FROM Menu WHERE Id_Menu = :id OR Nama_Menu = :nama");
    $check->execute([':id' => $id_menu, ':nama' => $nama_menu]);
    $matches = $check->fetchAll(PDO::FETCH_ASSOC);

    $id_soft_deleted = false;

    foreach ($matches as $row) {
        if ($row['Status_Aktif'] == 1) {
            // Kalau masih aktif (1), lempar error duplikat ke form tambah
            $error_type = ($row['Id_Menu'] == $id_menu) ? 'duplicate_id' : 'duplicate_name';
            header("Location: ../public/tambah.php?status=$error_type");
            exit;
        } else {
            // Kalau statusnya 0 (udah dihapus) dan ID-nya sama persis, tandai buat di-reuse!
            if ($row['Id_Menu'] == $id_menu) {
                $id_soft_deleted = true;
            }
        }
    }

    if ($id_soft_deleted) {
        // REUSE ID (TIDAK BOROS ID): 
        // Timpa data lama yang udah mati pakai data menu baru, dan aktifkan lagi (Status_Aktif = 1)
        // Struk lama tetap aman karena pakai sistem Snapshot di Detail_Transaksi
        $stmt = $conn->prepare("UPDATE Menu SET Kategori = :kategori, Nama_Menu = :nama_menu, Harga_Reguler = :harga_reguler, Harga_Large = :harga_large, Status_Aktif = 1 WHERE Id_Menu = :id_menu");
        $stmt->execute([
            ':kategori'      => $kategori,
            ':nama_menu'     => $nama_menu,
            ':harga_reguler' => $harga_reguler,
            ':harga_large'   => $harga_large,
            ':id_menu'       => $id_menu
        ]);
    } else {
        // INSERT BARU: Kalau ID-nya beneran belum pernah dibikin sama sekali
        $stmt = $conn->prepare("INSERT INTO Menu (Id_Menu, Kategori, Nama_Menu, Harga_Reguler, Harga_Large, Status_Aktif) VALUES (:id_menu, :kategori, :nama_menu, :harga_reguler, :harga_large, 1)");
        $stmt->execute([
            ':id_menu'       => $id_menu,
            ':kategori'      => $kategori,
            ':nama_menu'     => $nama_menu,
            ':harga_reguler' => $harga_reguler,
            ':harga_large'   => $harga_large
        ]);
    }

    header("Location: ../public/kelola_menu.php?status=sukses");
} catch (PDOException $e) {
    echo "Gagal menambah data: " . $e->getMessage();
}
?>