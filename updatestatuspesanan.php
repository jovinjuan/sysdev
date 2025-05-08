<?php
require "config.php";

// Cek apakah admin sudah login
$admin_id = $_SESSION['admin_id'] ?? null;
if (!isset($admin_id)) {
    header("Location: index.php");
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

if (!in_array($new_status, ['Dikirim', 'Batal'])) {
    $message = "Status yang diminta tidak valid.";
    header("Location: pesanan.php?message=" . urlencode($message));
    exit();
}

try {
    // Mulai transaksi
    $conn->beginTransaction();

    // Ambil detail pesanan (status, idpengguna, idproduk, jumlah)
    $stmt = $conn->prepare("SELECT status, idpengguna, idproduk, jumlah FROM pesanan WHERE idpesanan = :idpesanan FOR UPDATE");
    $stmt->bindParam(':idpesanan', $idpesanan, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception("Pesanan dengan ID $idpesanan tidak ditemukan.");
    }

    $current_status = $order['status'];
    $user_id = $order['idpengguna'];
    $idproduk = $order['idproduk'];
    $jumlah = $order['jumlah'];

    // Pastikan status saat ini adalah "Diproses"
    if ($current_status !== "Diproses") {
        throw new Exception("Status hanya dapat diubah dari 'Diproses' ke 'Dikirim' atau 'Batal'. Status saat ini: " . htmlspecialchars($current_status));
    }

    // Validasi idpengguna
    $stmt = $conn->prepare("SELECT id FROM pengguna WHERE id = :idpengguna");
    $stmt->bindParam(':idpengguna', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("Pengguna dengan ID $user_id tidak ditemukan.");
    }

    // Generate nomor resi
    $nomor_resi = 'ORD' . str_pad($idpesanan, 3, '0', STR_PAD_LEFT);

    // Jika status baru adalah "Dikirim", periksa dan kurangi stok
    if ($new_status === "Dikirim") {
        // Ambil data produk untuk validasi stok
        $stmt = $conn->prepare("SELECT namaproduk, stok FROM produk WHERE idproduk = :idproduk FOR UPDATE");
        $stmt->bindParam(':idproduk', $idproduk, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Produk dengan ID $idproduk tidak ditemukan.");
        }

        if ($product['stok'] < $jumlah) {
            throw new Exception("Stok untuk " . htmlspecialchars($product['namaproduk']) . " tidak mencukupi. Stok tersedia: " . $product['stok'] . ", Dibutuhkan: $jumlah");
        }

        // Kurangi stok
        $stmt = $conn->prepare("UPDATE produk SET stok = stok - :jumlah WHERE idproduk = :idproduk");
        $stmt->bindParam(':jumlah', $jumlah, PDO::PARAM_INT);
        $stmt->bindParam(':idproduk', $idproduk, PDO::PARAM_INT);
        $stmt->execute();

        // Verifikasi bahwa stok diperbarui
        $affected_rows = $stmt->rowCount();
        if ($affected_rows === 0) {
            throw new Exception("Gagal memperbarui stok untuk produk ID $idproduk. Tidak ada baris yang terpengaruh.");
        }

        $namaproduk = $product['namaproduk'];
    } else {
        // Jika status "Batal", ambil nama produk untuk notifikasi tanpa mengubah stok
        $stmt = $conn->prepare("SELECT namaproduk FROM produk WHERE idproduk = :idproduk");
        $stmt->bindParam(':idproduk', $idproduk, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $namaproduk = $product['namaproduk'] ?? 'Produk tidak ditemukan';
    }

    // Perbarui status pesanan
    $stmt = $conn->prepare("UPDATE pesanan SET status = :status WHERE idpesanan = :idpesanan");
    $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
    $stmt->bindParam(':idpesanan', $idpesanan, PDO::PARAM_INT);
    $stmt->execute();

    // Set notifikasi berdasarkan status baru
    if ($new_status === "Dikirim") {
        $pesan = "Pesanan Anda ($nomor_resi - " . htmlspecialchars($namaproduk) . ") sedang dalam pengiriman! Status: Dikirim";
    } else { // $new_status === "Batal"
        $pesan = "Pesanan Anda ($nomor_resi - " . htmlspecialchars($namaproduk) . ") telah dibatalkan oleh admin. Status: Batal";
    }
    $status_dibaca = 'Belum Dibaca';
    $stmt = $conn->prepare("INSERT INTO notifikasi (idpengguna, pesan, statusdibaca) VALUES (:idpengguna, :pesan, :statusdibaca)");
    $stmt->bindParam(':idpengguna', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':pesan', $pesan, PDO::PARAM_STR);
    $stmt->bindParam(':statusdibaca', $status_dibaca, PDO::PARAM_STR);
    $stmt->execute();

    // Commit transaksi
    $conn->commit();
    $message = "Status pesanan berhasil diubah menjadi '$new_status' dan notifikasi telah dikirim ke pelanggan.";
} catch (Exception $e) {
    // Rollback jika terjadi error
    $conn->rollBack();
    // Log error untuk debugging
    error_log("Error in updatestatus.php: " . $e->getMessage());
    $message = "Error: " . $e->getMessage();
}

// Redirect kembali ke halaman pesanan dengan pesan
header("Location: pesanan.php?message=" . urlencode($message));
exit();
?>