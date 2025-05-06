<?php
require_once 'config.php';

if (!cekLogin()) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idpenjualan = $_GET['id'];

    try {
        // Check if sale record exists
        $stmt_check = $conn->prepare("SELECT idpenjualan FROM penjualan WHERE idpenjualan = :idpenjualan");
        $stmt_check->bindParam(':idpenjualan', $idpenjualan);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() > 0) {
            $stmt_delete = $conn->prepare("DELETE FROM penjualan WHERE idpenjualan = :idpenjualan");
            $stmt_delete->bindParam(':idpenjualan', $idpenjualan);
            $stmt_delete->execute();
            
            if ($stmt_delete->rowCount() > 0) {
                $_SESSION['success_message'] = "Data penjualan berhasil dihapus!";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus data penjualan. Tidak ada baris yang terpengaruh.";
            }
        } else {
            $_SESSION['error_message'] = "Data penjualan tidak ditemukan atau sudah dihapus.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Gagal menghapus data penjualan: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "ID Penjualan tidak valid untuk penghapusan.";
}

header("Location: penjualan.php");
exit;
?>
