<?php
require_once 'config.php';

if (!cekLogin()) {
    header("Location: login.php");
    exit;
}

$nama_produk = '';
$harga_jual = '';
$stok = '';
$namagudang = '';
$waktuperubahan = '';
$berat = ''; // New variable for weight
$errors = [];
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = trim($_POST['nama_produk']);
    $harga_jual = trim($_POST['harga_jual']);
    $stok = trim($_POST['stok']);
    $namagudang = trim($_POST['namagudang']);
    $waktuperubahan = trim($_POST['waktuperubahan']);
    $berat = trim($_POST['berat']); // Get the new weight input

    if (empty($nama_produk)) {
        $errors[] = "Nama produk tidak boleh kosong.";
    }
    if (empty($harga_jual)) {
        $errors[] = "Harga jual tidak boleh kosong.";
    } elseif (!is_numeric($harga_jual) || $harga_jual < 0) {
        $errors[] = "Harga jual harus berupa angka positif.";
    }
    if (empty($stok)) {
        $errors[] = "Stok tidak boleh kosong.";
    } elseif (!is_numeric($stok) || $stok < 0) {
        $errors[] = "Stok harus berupa angka positif.";
    }
    if (empty($namagudang)) {
        $errors[] = "Lokasi penyimpanan tidak boleh kosong.";
    }
    if (empty($waktuperubahan)) {
        $errors[] = "Waktu perubahan stok tidak boleh kosong.";
    } elseif (!DateTime::createFromFormat('Y-m-d', $waktuperubahan)) {
        $errors[] = "Format tanggal perubahan stok harus YYYY-MM-DD.";
    }
    if (empty($berat)) {
        $errors[] = "Berat tidak boleh kosong.";
    } elseif (!is_numeric($berat) || $berat < 0) {
        $errors[] = "Berat harus berupa angka positif.";
    }

    if (empty($errors)) {
        try {
            // Start a transaction to ensure data consistency
            $conn->beginTransaction();

            // Insert the new storage location into gudang table
            $stmt_gudang = $conn->prepare("INSERT INTO gudang (namagudang) VALUES (:namagudang)");
            $stmt_gudang->bindParam(':namagudang', $namagudang);
            $stmt_gudang->execute();

            // Retrieve the newly created idgudang
            $idgudang = $conn->lastInsertId();

            // Insert into produk table with the new idgudang, waktuperubahan, and berat
            $stmt_produk = $conn->prepare("INSERT INTO produk (namaproduk, hargajual, stok, idgudang, waktuperubahan, berat) VALUES (:namaproduk, :hargajual, :stok, :idgudang, :waktuperubahan, :berat)");
            $stmt_produk->bindParam(':namaproduk', $nama_produk);
            $stmt_produk->bindParam(':hargajual', $harga_jual);
            $stmt_produk->bindParam(':stok', $stok);
            $stmt_produk->bindParam(':idgudang', $idgudang);
            $stmt_produk->bindParam(':waktuperubahan', $waktuperubahan);
            $stmt_produk->bindParam(':berat', $berat); // Bind the new weight field
            $stmt_produk->execute();

            // Commit the transaction
            $conn->commit();

            $success_message = "Produk berhasil ditambahkan!";
            $nama_produk = '';
            $harga_jual = '';
            $stok = '';
            $namagudang = '';
            $waktuperubahan = '';
            $berat = ''; // Reset the new field
        } catch (PDOException $e) {
            // Rollback the transaction on error
            $conn->rollBack();
            $errors[] = "Gagal menambahkan produk: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Sistem Gudang</title>
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
            <h2>Tambah Produk Baru</h2>

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

            <form method="POST" action="tambah_produk.php">
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
                <div class="mb-3">
                    <label for="berat" class="form-label">Berat (kg)</label>
                    <input type="number" class="form-control" id="berat" name="berat" value="<?php echo htmlspecialchars($berat); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="namagudang" class="form-label">Lokasi Penyimpanan</label>
                    <input type="text" class="form-control" id="namagudang" name="namagudang" value="<?php echo htmlspecialchars($namagudang); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="waktuperubahan" class="form-label">Waktu Perubahan Stok</label>
                    <input type="date" class="form-control" id="waktuperubahan" name="waktuperubahan" value="<?php echo htmlspecialchars($waktuperubahan); ?>" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-custom">Tambah Produk</button>
                    <a href="produk.php" class="btn btn-secondary">Kembali ke Daftar Produk</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>