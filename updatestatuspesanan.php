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

    // Ambil status saat ini dari pesanan
    $stmt = $conn->prepare("SELECT status FROM pesanan WHERE idpesanan = :idpesanan FOR UPDATE");
    $stmt->bindParam(':idpesanan', $idpesanan, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $current_status = $order['status'];

        // Pastikan status saat ini adalah "Diproses"
        if ($current_status !== "Diproses") {
            throw new Exception("Status hanya dapat diubah dari 'Diproses' ke 'Dikirim'. Status saat ini: " . htmlspecialchars($current_status));
        }

        // Perbarui status menjadi "Dikirim"
        $stmt = $conn->prepare("UPDATE pesanan SET status = :status WHERE idpesanan = :idpesanan");
        $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
        $stmt->bindParam(':idpesanan', $idpesanan, PDO::PARAM_INT);
        $stmt->execute();

        // Commit transaksi
        $conn->commit();
        $message = "Status pesanan berhasil diubah menjadi 'Dikirim'.";
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