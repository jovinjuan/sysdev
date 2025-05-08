<?php
require "config.php";

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['pesan'])) {
        // Proses pemesanan
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

        // Start a transaction
        $conn->beginTransaction();

        try {
            // Verify stock for all items
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

                // Ambil idpesanan yang baru dibuat
                $idpesanan = $conn->lastInsertId();

                // Generate nomor resi
                $nomor_resi = 'ORD' . str_pad($idpesanan, 3, '0', STR_PAD_LEFT);

                // Ambil nama produk untuk notifikasi
                $sql = "SELECT namaproduk FROM produk WHERE idproduk = :idproduk";
                $query = $conn->prepare($sql);
                $query->bindParam(':idproduk', $idproduk, PDO::PARAM_INT);
                $query->execute();
                $product = $query->fetch(PDO::FETCH_ASSOC);
                $namaproduk = $product['namaproduk'] ?? 'Produk tidak ditemukan';

                // Kirim notifikasi ke pelanggan
                $pesan = "Pesanan Anda ($nomor_resi - " . htmlspecialchars($namaproduk) . ") telah berhasil dibuat. Status: Diproses";
                $status_dibaca = 'Belum Dibaca';
                $sql = "INSERT INTO notifikasi (idpengguna, pesan, statusdibaca) VALUES (:idpengguna, :pesan, :statusdibaca)";
                $query = $conn->prepare($sql);
                $query->bindParam(':idpengguna', $user_id, PDO::PARAM_INT);
                $query->bindParam(':pesan', $pesan, PDO::PARAM_STR);
                $query->bindParam(':statusdibaca', $status_dibaca, PDO::PARAM_STR);
                $query->execute();
            }

            // Clear the cart
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
    } elseif (isset($_POST['cancel_order_id'])) {
        // Proses pembatalan pesanan
        $order_id = $_POST['cancel_order_id'];
        $message = "";

        try {
            // Start a transaction
            $conn->beginTransaction();

            // Verify order exists and is in cancellable status
            $sql = "SELECT status FROM pesanan WHERE idpesanan = :idpesanan AND idpengguna = :idpengguna";
            $query = $conn->prepare($sql);
            $query->bindParam(':idpesanan', $order_id, PDO::PARAM_INT);
            $query->bindParam(':idpengguna', $user_id, PDO::PARAM_INT);
            $query->execute();
            $order = $query->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new Exception("Pesanan tidak ditemukan.");
            }

            if ($order['status'] !== 'Diproses') {
                throw new Exception("Pesanan tidak dapat dibatalkan karena sudah dikirim atau dibatalkan.");
            }

            // Cancel the order
            $sql = "UPDATE pesanan SET status = 'Dibatalkan' WHERE idpesanan = :idpesanan";
            $query = $conn->prepare($sql);
            $query->bindParam(':idpesanan', $order_id, PDO::PARAM_INT);
            $query->execute();

            // Commit transaction
            $conn->commit();
            $message = "Pesanan berhasil dibatalkan.";
            header("Location: dashboardpelanggan.php?message=" . urlencode($message));
            exit();
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollBack();
            $message = "Error: " . $e->getMessage();
            header("Location: dashboardpelanggan.php?message=" . urlencode($message));
            exit();
        }
    } else {
        die("Invalid request.");
    }
} else {
    die("Invalid request method.");
}
?>