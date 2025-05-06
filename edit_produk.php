<?php
require_once 'config.php';

if (!cekLogin()) {
    header("Location: login.php");
    exit;
}

$id_produk = '';
$nama_produk = '';
$harga_jual = '';
$stok = '';
$errors = [];
$success_message = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_produk = $_GET['id'];

    // Fetch product details
    try {
        $stmt = $conn->prepare("SELECT namaproduk, hargajual, stok FROM produk WHERE idproduk = :idproduk");
        $stmt->bindParam(':idproduk', $id_produk);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $nama_produk = $product['namaproduk'];
            $harga_jual = $product['hargajual'];
            $stok = $product['stok'];
        } else {
            $errors[] = "Produk tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errors[] = "Gagal mengambil data produk: " . $e->getMessage();
    }
} else {
    $errors[] = "ID Produk tidak valid.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id_produk']) && is_numeric($_POST['id_produk'])) {
        $id_produk = $_POST['id_produk']; // Ensure id_produk is set from POST for update
    } else {
        $errors[] = "ID Produk tidak valid untuk pembaruan.";
    }

    $nama_produk_new = trim($_POST['nama_produk']);
    $harga_jual_new = trim($_POST['harga_jual']);
    $stok_new = trim($_POST['stok']);

    if (empty($nama_produk_new)) {
        $errors[] = "Nama produk tidak boleh kosong.";
    }
    if (empty($harga_jual_new)) {
        $errors[] = "Harga jual tidak boleh kosong.";
    } elseif (!is_numeric($harga_jual_new) || $harga_jual_new < 0) {
        $errors[] = "Harga jual harus berupa angka positif.";
    }
    if (empty($stok_new)) {
        $errors[] = "Stok tidak boleh kosong.";
    } elseif (!is_numeric($stok_new) || $stok_new < 0) {
        $errors[] = "Stok harus berupa angka positif.";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE produk SET namaproduk = :namaproduk, hargajual = :hargajual, stok = :stok WHERE idproduk = :idproduk");
            $stmt->bindParam(':namaproduk', $nama_produk_new);
            $stmt->bindParam(':hargajual', $harga_jual_new);
            $stmt->bindParam(':stok', $stok_new);
            $stmt->bindParam(':idproduk', $id_produk);
            $stmt->execute();
            $success_message = "Produk berhasil diperbarui!";
            // Update current values to reflect changes
            $nama_produk = $nama_produk_new;
            $harga_jual = $harga_jual_new;
            $stok = $stok_new;
        } catch (PDOException $e) {
            $errors[] = "Gagal memperbarui produk: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Sistem Gudang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #2d3748, #4a5568);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }
        .sidebar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 2rem;
        }
        .sidebar .nav-link {
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link i {
            margin-right: 0.75rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }
        .content {
            margin-left: 250px;
            padding: 2rem;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            max-width: 700px;
            margin: 2rem auto;
        }
        .form-container h2 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-label {
            color: #ffffff;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.3);
            border-color: #f59e0b;
            color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.25);
        }
        .btn-custom {
            background: #f59e0b;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
        }
        .btn-custom:hover {
            background: #d97706;
            color: #ffffff;
        }
        .alert-custom {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <h4 class="text-white text-center mb-4">Sistem Gudang</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="index.php"><i class="fas fa-home"></i><span>Home</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pesanan.php"><i class="fas fa-shopping-cart"></i><span>Pesanan</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="produk.php"><i class="fas fa-boxes"></i><span>Produk</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="penjualan.php"><i class="fas fa-money-bill-wave"></i><span>Penjualan</span></a>
            </li>
            <li class="nav-item mt-auto">
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="form-container">
            <h2>Edit Produk</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-custom" role="alert">
                    <strong>Error!</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success alert-custom" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($errors) || $success_message): // Show form if no critical errors or after success ?>
            <form method="POST" action="edit_produk.php?id=<?php echo htmlspecialchars($id_produk); ?>">
                <input type="hidden" name="id_produk" value="<?php echo htmlspecialchars($id_produk); ?>">
                <div class="mb-3">
                    <label for="nama_produk" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?php echo htmlspecialchars($nama_produk); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="harga_jual" class="form-label">Harga Jual (Rp)</label>
                    <input type="number" class="form-control" id="harga_jual" name="harga_jual" value="<?php echo htmlspecialchars($harga_jual); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" class="form-control" id="stok" name="stok" value="<?php echo htmlspecialchars($stok); ?>" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-custom">Simpan Perubahan</button>
                    <a href="produk.php" class="btn btn-secondary">Kembali ke Daftar Produk</a>
                </div>
            </form>
            <?php elseif(!empty($errors) && (strpos(implode(" ", $errors), "Produk tidak ditemukan") !== false || strpos(implode(" ", $errors), "ID Produk tidak valid") !== false)): ?>
                <div class="text-center">
                     <a href="produk.php" class="btn btn-secondary">Kembali ke Daftar Produk</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
