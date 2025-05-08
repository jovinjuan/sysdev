<?php
require "config.php";
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: index.php");
    exit();
}

// Handle clear cart action
if (isset($_GET['clear']) && $_GET['clear'] === 'true') {
    $stmt = $conn->prepare("DELETE FROM keranjang WHERE idpengguna = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    header("Location: keranjang.php");
    exit();
}

// Handle update alamat
$alamat = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_alamat'])) {
    $alamat_input = $_POST['alamat'] ?? '';
    if (!empty($alamat_input)) {
        $stmt = $conn->prepare("UPDATE keranjang SET alamat = :alamat WHERE idpengguna = :user_id");
        $stmt->bindParam(':alamat', $alamat_input);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        header("Location: keranjang.php");
        exit();
    } else {
        $error = "Alamat wajib diisi.";
    }
}

// Fetch cart items with product details and weight
$stmt = $conn->prepare("
    SELECT k.idkeranjang, k.idproduk, k.jumlah, k.totalharga, k.biayapengiriman, k.grandtotal, p.berat, p.namaproduk, p.hargajual
    FROM keranjang k
    JOIN produk p ON k.idproduk = p.idproduk
    WHERE k.idpengguna = :user_id
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user details (telepon only)
$user_stmt = $conn->prepare("SELECT telepon FROM pengguna WHERE id = :user_id");
$user_stmt->bindParam(':user_id', $user_id);
$user_stmt->execute();
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Calculate total items, shipping cost based on weight, and grand total
$total_items = count($cart_items);
$subtotal = 0;
$total_berat = 0;

foreach ($cart_items as $item) {
    $subtotal += $item['totalharga'];
    $total_berat += $item['berat'] * $item['jumlah']; // Total weight = weight per item * quantity
}

$biaya_per_kg = 2000;
$total_biayapengiriman = $total_berat * $biaya_per_kg;

// Update shipping cost in the database for each item
foreach ($cart_items as $item) {
    $item_biayapengiriman = ($item['berat'] * $item['jumlah']) * $biaya_per_kg;
    $item_grandtotal = $item['totalharga'] + $item_biayapengiriman;
    
    $update_stmt = $conn->prepare("
        UPDATE keranjang 
        SET biayapengiriman = :biayapengiriman, grandtotal = :grandtotal 
        WHERE idkeranjang = :idkeranjang
    ");
    $update_stmt->bindParam(':biayapengiriman', $item_biayapengiriman);
    $update_stmt->bindParam(':grandtotal', $item_grandtotal);
    $update_stmt->bindParam(':idkeranjang', $item['idkeranjang']);
    $update_stmt->execute();
}

$grand_total = $subtotal + $total_biayapengiriman;

// Default telepon if not found
$telepon = $user['telepon'] ?? '081234567890';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #1a2b3c;
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
      color: #ffffff;
      margin: 0;
    }
    .cart-container {
      max-width: 1500px;
      margin: 40px auto;
      background: #2a3b4c;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }
    .cart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }
    .cart-header h2 {
      font-size: 28px;
      font-weight: 600;
    }
    .cart-item {
      display: flex;
      align-items: center;
      padding: 15px;
      background: #34495e;
      border-radius: 10px;
      margin-bottom: 10px;
      transition: transform 0.2s;
    }
    .cart-item:hover {
      transform: translateY(-2px);
    }
    .cart-item img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      margin-right: 20px;
      border-radius: 8px;
    }
    .cart-item h5 {
      font-size: 16px;
      font-weight: 500;
      margin-bottom: 5px;
    }
    .cart-item p {
      font-size: 14px;
      color: #b0bec5;
      margin-bottom: 0;
    }
    .cart-details {
      background: #34495e;
      padding: 20px;
      border-radius: 10px;
      margin-top: 20px;
    }
    .cart-details p {
      margin-bottom: 10px;
      font-size: 16px;
    }
    .cart-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 0;
      border-top: 1px solid #4a5b6c;
      margin-top: 20px;
    }
    .cart-footer h4 {
      font-size: 22px;
      font-weight: 600;
    }
    .btn-checkout {
      background-color: #dc3545;
      border: none;
      padding: 12px 30px;
      font-size: 16px;
      font-weight: 500;
      border-radius: 8px;
    }
    .btn-checkout:hover {
      background-color: #c82333;
    }
    .btn-outline-secondary {
      border-color: #6c757d;
      color: #ffffff;
      padding: 8px 15px;
      border-radius: 8px;
    }
    .btn-outline-secondary:hover {
      background-color: #6c757d;
      color: #ffffff;
    }
    .empty-cart {
      text-align: center;
      color: #b0bec5;
      font-size: 18px;
      padding: 20px;
    }
  </style>
</head>
<body>
  <div class="cart-container">
    <div class="cart-header">
      <h2>Shopping Cart</h2>
      <div>
        <button type="button" class="btn btn-outline-secondary me-2">
          <a href="pesanproduk.php" class="text-decoration-none text-white"><i class="bi bi-arrow-left"></i>Kembali</a>
        </button>
        <button type="button" class="btn btn-outline-secondary" id="clearCart">
          <i class="bi bi-trash"></i> Hapus
        </button>
      </div>
    </div>

    <?php if (empty($cart_items)) { ?>
      <div class="empty-cart">Keranjang Anda kosong.</div>
    <?php } else { ?>
      <form action="prosespesanan.php" method="POST">
        <?php foreach ($cart_items as $index => $item) { ?>
          <div class="cart-item">
            <div class="flex-grow-1">
              <h5><?php echo htmlspecialchars($item['namaproduk']); ?></h5>
              <p>Quantity: <?php echo htmlspecialchars($item['jumlah']); ?></p>
              <p>Berat: <?php echo htmlspecialchars($item['berat'] * $item['jumlah']); ?> kg (<?php echo htmlspecialchars($item['berat']); ?> kg/unit)</p>
            </div>
            <h5>Rp <?php echo number_format($item['totalharga'], 0, ',', '.'); ?></h5>
            <input type="hidden" name="items[<?php echo $index; ?>][idproduk]" value="<?php echo htmlspecialchars($item['idproduk']); ?>">
            <input type="hidden" name="items[<?php echo $index; ?>][jumlah]" value="<?php echo htmlspecialchars($item['jumlah']); ?>">
            <input type="hidden" name="items[<?php echo $index; ?>][totalharga]" value="<?php echo htmlspecialchars($item['totalharga']); ?>">
            <input type="hidden" name="items[<?php echo $index; ?>][biayapengiriman]" value="<?php echo htmlspecialchars($item['biayapengiriman']); ?>">
            <input type="hidden" name="items[<?php echo $index; ?>][grandtotal]" value="<?php echo htmlspecialchars($item['grandtotal']); ?>">
          </div>
        <?php } ?>

        <div class="cart-details">
            <label for="alamat" class="form-label text-white fs-5"><strong>Alamat Pengiriman:</strong></label>
            <textarea class="form-control mt-3 mb-3" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat pengiriman" required><?php echo htmlspecialchars($alamat); ?></textarea>
          <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          <p><strong>Total Berat:</strong> <?php echo htmlspecialchars($total_berat); ?> kg</p>
          <p><strong>Telepon:</strong> <?php echo htmlspecialchars($telepon); ?></p>
          <p><strong>Biaya Pengiriman :</strong> Rp <?php echo number_format($total_biayapengiriman, 0, ',', '.'); ?></p>
        </div>

        <div class="cart-footer">
          <div>
            <p class="mb-0"><?php echo $total_items; ?> Items</p>
            <h4>Total: Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></h4>
          </div>
          <button type="submit" name="pesan" class="btn btn-checkout text-white">Pesan</button>
        </div>
      </form>
    <?php } ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
    document.getElementById('clearCart').addEventListener('click', function() {
      if (confirm('Apakah Anda yakin ingin menghapus semua item dari keranjang?')) {
        window.location.href = 'keranjang.php?clear=true';
      }
    });
  </script>
</body>
</html>