<?php
require "config.php";

$user_id = $_SESSION['user_id'];

// Get form data
$idproduk = $_POST['idproduct'];
$hargajual = $_POST['hargajual'];
$jumlah = $_POST['jumlah'];

// Static shipping cost
$biayapengiriman = 50000;

// Calculate total price for this item
$totalharga = $hargajual * $jumlah;

// Calculate grand total (including shipping)
$grandtotal = $totalharga + $biayapengiriman;

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO keranjang (idpengguna, idproduk, jumlah, totalharga, biayapengiriman, grandtotal) VALUES (:idpengguna, :idproduk, :jumlah, :totalharga, :biayapengiriman, :grandtotal)");
$stmt->bindParam(':idpengguna', $user_id);
$stmt->bindParam(':idproduk', $idproduk);
$stmt->bindParam(':jumlah', $jumlah);
$stmt->bindParam(':totalharga', $totalharga);
$stmt->bindParam(':biayapengiriman', $biayapengiriman);
$stmt->bindParam(':grandtotal', $grandtotal);

try {
    $stmt->execute();
    // Redirect to cart page or show success message
    header("Location: pesanproduk.php?success=1");
    exit();
} catch (PDOException $e) {
    // Handle error (e.g., log it or show user-friendly message)
    echo "Error: " . $e->getMessage();
    // Optionally redirect with error parameter
    header("Location: pesanproduk.php?error=1");
    exit();
}
?>