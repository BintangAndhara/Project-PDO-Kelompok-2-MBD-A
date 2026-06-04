<?php
// process/delete.php
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: ../public/kelola_menu.php");
    exit;
}

try {
    // SOFT DELETE: Mengubah status menjadi 0
    // Data tetap ada di MySQL agar relasi Foreign Key tidak rusak
    $stmt = $conn->prepare("UPDATE Menu SET Status_Aktif = 0 WHERE Id_Menu = :id_menu");
    $stmt->execute([':id_menu' => $_GET['id']]);
    
    header("Location: ../public/kelola_menu.php?status=deleted");
    exit;
} catch (PDOException $e) {
    // Kalau masih ada error lain dari PDO, tampilkan pesannya
    die("Gagal menghapus data: " . $e->getMessage());
}
?>