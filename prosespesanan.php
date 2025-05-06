<?php
require "config.php";
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pesan'])) {
    $items = $_POST['items'] ?? [];
    $alamat = $_POST['alamat'] ?? '';
    $message = "";

    // Validasi input
    if (empty($items)) {
        $message = "Keranjang Anda kosong.";
        header("Location: keranjang.php?message=" . urlencode($message));
        exit();
    }

    if (empty($alamat)) {
        $message = "Alamat pengiriman wajib diisi.";
        header("Location: keranjang.php?message=" . urlencode($message));
        exit();
    }

    // Start a transaction to ensure atomicity
    $conn->beginTransaction();

    try {
        // Verify stock for all items before proceeding
        foreach ($items as $item) {
            $idproduk = $item['idproduk'];
            $jumlah = $item['jumlah'];

            $sql = "SELECT idproduk, namaproduk, stok FROM produk WHERE idproduk = :id FOR UPDATE";
            $query = $conn->prepare($sql);
            $query->execute([':id' => $idproduk]);
            $product = $query->fetch(PDO::FETCH_ASSOC);

            if (!$product || $product['stok'] < $jumlah) {
                throw new Exception("Stok untuk " . ($product ? htmlspecialchars($product['namaproduk']) : "produk") . " tidak mencukupi atau produk tidak ditemukan.");
            }
        }

        // Insert each cart item into pesanan table
        foreach ($items as $item) {
            $idproduk = $item['idproduk'];
            $jumlah = $item['jumlah'];
            $totalharga = $item['totalharga'];

            $sql = "INSERT INTO pesanan (idpengguna, idproduk, jumlah, alamatpengiriman, status, totalharga) 
                    VALUES (:idpengguna, :idproduk, :jumlah, :alamat, 'Diproses', :totalharga)";
            $query = $conn->prepare($sql);
            $query->bindParam(':idpengguna', $user_id, PDO::PARAM_INT);
            $query->bindParam(':idproduk', $idproduk, PDO::PARAM_INT);
            $query->bindParam(':jumlah', $jumlah, PDO::PARAM_INT);
            $query->bindParam(':totalharga', $totalharga, PDO::PARAM_INT);
            $query->bindParam(':alamat', $alamat, PDO::PARAM_STR);
            $query->execute();

            // Update stock in produk table
            $sql = "UPDATE produk SET stok = stok - :jumlah WHERE idproduk = :id";
            $query = $conn->prepare($sql);
            $query->execute([':jumlah' => $jumlah, ':id' => $idproduk]);
        }

        // Clear the cart after successful order
        $sql = "DELETE FROM keranjang WHERE idpengguna = :user_id";
        $query = $conn->prepare($sql);
        $query->execute([':user_id' => $user_id]);

        // Commit transaction
        $conn->commit();
        $message = "Pesanan Anda berhasil ditempatkan!";
        header("Location: dashboardpelanggan.php?message=" . urlencode($message));
        exit();
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollBack();
        $message = "Error: " . $e->getMessage();
        header("Location: keranjang.php?message=" . urlencode($message));
        exit();
    }
} else {
    die("Invalid request method.");
}
?>