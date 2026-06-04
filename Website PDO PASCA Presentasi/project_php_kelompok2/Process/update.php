<?php
require_once '../config/database.php';

if ($_POST['harga_reguler'] <= 0 || $_POST['harga_large'] <= 0) { die("Harga tidak valid"); }

$harga_reguler = str_replace(',', '.', $_POST['harga_reguler']);
$harga_large = str_replace(',', '.', $_POST['harga_large']);

try {
    $stmt = $conn->prepare("UPDATE Menu SET Kategori = :kategori, Nama_Menu = :nama_menu, Harga_Reguler = :harga_reguler, Harga_Large = :harga_large WHERE Id_Menu = :id_menu");
    $stmt->execute([
        ':kategori'      => $_POST['kategori'],
        ':nama_menu'     => $_POST['nama_menu'],
        ':harga_reguler' => $harga_reguler,
        ':harga_large'   => $harga_large,
        ':id_menu'       => $_POST['id_menu']
    ]);
    header("Location: ../public/kelola_menu.php?status=updated");
} catch (PDOException $e) {
    echo "Gagal update data: " . $e->getMessage();
}
?>