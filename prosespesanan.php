<?php
require "config.php";
$user_id = $_SESSION['user_id'];

if (isset($_POST['pesan'])) {
    // Retrieve form data
    $idproduk = $_POST['idproduct'] ?? null;
    $jumlah = $_POST['jumlah'] ?? 0;

    // Validate input
    if ($idproduk === null || $jumlah <= 0) {
        $message = "Data pesanan tidak lengkap atau jumlah tidak valid.";
    } else {
        // Start a transaction to ensure atomicity
        $conn->beginTransaction();

        try {
            // Verify product exists and check stock
            $sql = "SELECT idproduk, namaproduk, stok FROM produk WHERE idproduk = :id FOR UPDATE";
            $query = $conn->prepare($sql);
            $query->execute([':id' => $idproduk]);
            $product = $query->fetch(PDO::FETCH_ASSOC);

            if ($product && $product['stok'] >= $jumlah) {
                // Insert order into orders table
                $sql = "INSERT INTO pesanan (idpengguna, idproduk, jumlah, status) VALUES (:idpengguna, :idproduk, :jumlah, 'Diproses')";
                $query = $conn->prepare($sql);
                $query->bindParam(':idpengguna',$user_id,PDO::PARAM_INT);
                $query->bindParam(':idproduk',$idproduk,PDO::PARAM_INT);
                $query->bindParam(':jumlah',$jumlah,PDO::PARAM_STR);
                $result = $query->execute();

                // Update stock in produk table
                $sql = "UPDATE produk SET stok = stok - :jumlah WHERE idproduk = :id";
                $query = $conn->prepare($sql);
                $query->execute([':jumlah' => $jumlah, ':id' => $idproduk]);

                // Commit transaction
                $conn->commit();
                $message = "Pesanan untuk " . htmlspecialchars($product['namaproduk']) . " berhasil ditambahkan!";
            } else {
                // Rollback if stock is insufficient or product not found
                $conn->rollBack();
                $message = "Stok untuk " . ($product ? htmlspecialchars($product['namaproduk']) : "produk") . " tidak mencukupi atau produk tidak ditemukan.";
            }
        } catch (PDOException $e) {
            // Rollback on database error
            $conn->rollBack();
            $message = "Error database: " . $e->getMessage();
        }
    }

    // Redirect back with message
    header("Location: pesanproduk.php?message=" . urlencode($message));
    exit();
} else {
    die("Invalid request method.");
}


?>