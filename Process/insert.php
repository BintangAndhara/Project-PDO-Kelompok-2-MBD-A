<?php
require_once '../config/database.php';

if ($_POST['harga'] <= 0) { die("Harga tidak valid"); }

$id_menu = $_POST['id_menu'];
$nama_menu = $_POST['nama_menu'];
$kategori = $_POST['kategori'];
$harga = $_POST['harga'];

try {
   
    $check = $conn->prepare("SELECT * FROM Menu WHERE Id_Menu = :id OR Nama_Menu = :nama");
    $check->execute([':id' => $id_menu, ':nama' => $nama_menu]);
    $matches = $check->fetchAll(PDO::FETCH_ASSOC);

    $id_soft_deleted = false;

    foreach ($matches as $row) {
      
        if ($row['Status_Aktif'] == 1) {
            $error_type = ($row['Id_Menu'] == $id_menu) ? 'duplicate_id' : 'duplicate_name';
            header("Location: ../public/tambah.php?status=$error_type");
            exit;
        } else {
           
            if ($row['Id_Menu'] == $id_menu) {
                $id_soft_deleted = true;
            }
        }
    }

    if ($id_soft_deleted) {
       
        $stmt = $conn->prepare("UPDATE Menu SET Kategori = :kategori, Nama_Menu = :nama_menu, Harga = :harga, Status_Aktif = 1 WHERE Id_Menu = :id_menu");
        $stmt->execute([
            ':kategori'  => $kategori,
            ':nama_menu' => $nama_menu,
            ':harga'     => $harga,
            ':id_menu'   => $id_menu
        ]);
    } else {
    
        $stmt = $conn->prepare("INSERT INTO Menu (Id_Menu, Kategori, Nama_Menu, Harga, Status_Aktif) VALUES (:id_menu, :kategori, :nama_menu, :harga, 1)");
        $stmt->execute([
            ':id_menu'   => $id_menu,
            ':kategori'  => $kategori,
            ':nama_menu' => $nama_menu,
            ':harga'     => $harga
        ]);
    }

    header("Location: ../public/kelola_menu.php?status=sukses");
} catch (PDOException $e) {
    echo "Gagal menambah data: " . $e->getMessage();
}
?>