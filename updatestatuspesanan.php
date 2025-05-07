<?php
require "config.php";

// Cek apakah admin sudah login
$admin_id = $_SESSION['admin_id'] ?? null;
if (!isset($admin_id)) {
    header("Location: login.php");
    exit();
}

// Ambil parameter dari URL
$idpesanan = $_GET['idpesanan'] ?? null;
$new_status = $_GET['status'] ?? null;

// Validasi parameter
if (!is_numeric($idpesanan) || $idpesanan <= 0) {
    $message = "ID pesanan tidak valid.";
    header("Location: pesanan.php?message=" . urlencode($message));
    exit();
}

if ($new_status !== "Dikirim") {
    $message = "Status yang diminta tidak valid.";
    header("Location: pesanan.php?message=" . urlencode($message));
    exit();
}

try {
    // Mulai transaksi
    $conn->beginTransaction();

    // Ambil status saat ini dan idpengguna dari pesanan
    $stmt = $conn->prepare("SELECT status, idpengguna FROM pesanan WHERE idpesanan = :idpesanan FOR UPDATE");
    $stmt->bindParam(':idpesanan', $idpesanan, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $current_status = $order['status'];
        $user_id = $order['idpengguna'];

        // Pastikan status saat ini adalah "Diproses"
        if ($current_status !== "Diproses") {
            throw new Exception("Status hanya dapat diubah dari 'Diproses' ke 'Dikirim'. Status saat ini: " . htmlspecialchars($current_status));
        }

        // Ambil nama produk untuk notifikasi
        $stmt = $conn->prepare("SELECT namaproduk FROM pesanan p JOIN produk pr ON p.idproduk = pr.idproduk WHERE p.idpesanan = :idpesanan");
        $stmt->bindParam(':idpesanan', $idpesanan, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $namaproduk = $product['namaproduk'];

        // Perbarui status menjadi "Dikirim"
        $stmt = $conn->prepare("UPDATE pesanan SET status = :status WHERE idpesanan = :idpesanan");
        $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
        $stmt->bindParam(':idpesanan', $idpesanan, PDO::PARAM_INT);
        $stmt->execute();

        // Simpan notifikasi ke tabel notifikasi
        $pesan = "Pesanan Anda (ORD" . str_pad($idpesanan, 3, '0', STR_PAD_LEFT) . " - " . htmlspecialchars($namaproduk) . ") sedang dalam pengiriman! Status: Dikirim";
        $status_dibaca = 0; // Belum Dibaca
        $waktu = date('Y-m-d H:i:s'); // Waktu saat ini
        $stmt = $conn->prepare("INSERT INTO notifikasi (idpengguna, pesan, statusdibaca) VALUES (:idpengguna, :pesan, :statusdibaca)");
        $stmt->bindParam(':idpengguna', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':pesan', $pesan, PDO::PARAM_STR);
        $stmt->bindParam(':statusdibaca', $status_dibaca, PDO::PARAM_INT);
        $stmt->execute();

        // Commit transaksi
        $conn->commit();
        $message = "Status pesanan berhasil diubah menjadi 'Dikirim' dan notifikasi telah dikirim ke pelanggan.";
    } else {
        throw new Exception("Pesanan dengan ID tersebut tidak ditemukan.");
    }
} catch (Exception $e) {
    // Rollback jika terjadi error
    $conn->rollBack();
    $message = "Error: " . $e->getMessage();
}

// Redirect kembali ke halaman pesanan dengan pesan
header("Location: pesanan.php?message=" . urlencode($message));
exit();
?>