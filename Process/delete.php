<?php
// process/delete.php
require_once '../config/database.php';


if (!isset($_GET['id'])) {
    header("Location: ../public/kelola_menu.php");
    exit;
}

try {
  
    $stmt = $conn->prepare("UPDATE Menu SET Status_Aktif = 0 WHERE Id_Menu = :id_menu");
    $stmt->execute([':id_menu' => $_GET['id']]);
    
    header("Location: ../public/kelola_menu.php?status=deleted");
    exit;
} catch (PDOException $e) {
    die("Gagal menghapus data: " . $e->getMessage());
}
?>