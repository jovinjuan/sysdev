<?php
require_once 'config.php';

if (!cekLogin()) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success_message = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_produk = $_GET['id'];

    try {
        // Check if product exists before attempting to delete
        $stmt_check = $conn->prepare("SELECT idproduk FROM produk WHERE idproduk = :idproduk");
        $stmt_check->bindParam(':idproduk', $id_produk);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() > 0) {
            $stmt_delete = $conn->prepare("DELETE FROM produk WHERE idproduk = :idproduk");
            $stmt_delete->bindParam(':idproduk', $id_produk);
            $stmt_delete->execute();
            
            if ($stmt_delete->rowCount() > 0) {
                $_SESSION['success_message'] = "Produk berhasil dihapus!";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus produk. Tidak ada baris yang terpengaruh.";
            }
        } else {
            $_SESSION['error_message'] = "Produk tidak ditemukan atau sudah dihapus.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Gagal menghapus produk: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "ID Produk tidak valid untuk penghapusan.";
}

header("Location: produk.php");
exit;
?>
