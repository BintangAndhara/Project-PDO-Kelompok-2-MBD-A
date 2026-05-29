<?php
require_once '../config/database.php';
if ($_POST['harga'] <= 0) { die("Harga tidak valid"); }

try {
    $stmt = $conn->prepare("UPDATE Menu SET Kategori = :kategori, Nama_Menu = :nama_menu, Harga = :harga WHERE Id_Menu = :id_menu");
    $stmt->execute([
        ':kategori'  => $_POST['kategori'],
        ':nama_menu' => $_POST['nama_menu'],
        ':harga'     => $_POST['harga'],
        ':id_menu'   => $_POST['id_menu']
    ]);
    header("Location: ../public/kelola_menu.php?status=updated");
} catch (PDOException $e) {
    echo "Gagal update data: " . $e->getMessage();
}
?>