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
        // Start a transaction to ensure atomicity
        $conn->beginTransaction();

        // Fetch the product's idgudang before deletion
        $stmt_check = $conn->prepare("SELECT idgudang FROM produk WHERE idproduk = :idproduk");
        $stmt_check->bindParam(':idproduk', $id_produk);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            $product = $stmt_check->fetch(PDO::FETCH_ASSOC);
            $idgudang = $product['idgudang'];

            // Delete the product
            $stmt_delete_produk = $conn->prepare("DELETE FROM produk WHERE idproduk = :idproduk");
            $stmt_delete_produk->bindParam(':idproduk', $id_produk);
            $stmt_delete_produk->execute();

            if ($stmt_delete_produk->rowCount() > 0) {
                // Check if any other products are using this idgudang 
                $stmt_check_gudang = $conn->prepare("SELECT COUNT(*) FROM produk WHERE idgudang = :idgudang");
                $stmt_check_gudang->bindParam(':idgudang', $idgudang);
                $stmt_check_gudang->execute();
                $gudang_usage_count = $stmt_check_gudang->fetchColumn();

                if ($gudang_usage_count == 0) {
                    // No other products use this gudang, so delete it
                    $stmt_delete_gudang = $conn->prepare("DELETE FROM gudang WHERE idgudang = :idgudang");
                    $stmt_delete_gudang->bindParam(':idgudang', $idgudang);
                    $stmt_delete_gudang->execute();
                }

                // Commit the transaction
                $conn->commit();
                $_SESSION['success_message'] = "Produk dan lokasi penyimpanan berhasil dihapus!";
            } else {
                $conn->rollBack();
                $_SESSION['error_message'] = "Gagal menghapus produk. Tidak ada baris yang terpengaruh.";
            }
        } else {
            $conn->rollBack();
            $_SESSION['error_message'] = "Produk tidak ditemukan atau sudah dihapus.";
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Gagal menghapus produk: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "ID Produk tidak valid untuk penghapusan.";
}

header("Location: produk.php");
exit;
?>