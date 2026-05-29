<?php
// public/hapus.php
$id = $_GET['id'];
header("Location: ../process/delete.php?id=" . $id);
?>