<?php
require_once 'config.php';

if (!cekLogin()) {
    header("Location: login.php");
    exit;
}

$idpenjualan = '';
$notajual = '';
$namabarang = '';
$harga = '';
$jumlah = '';
$total = '';
$grandtotal = '';
$tanggalpenjualan = '';
$errors = [];
$success_message = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idpenjualan = $_GET['id'];
    try {
        $stmt = $conn->prepare("SELECT notajual, namabarang, harga, jumlah, total, grandtotal, tanggalpenjualan FROM penjualan WHERE idpenjualan = :idpenjualan");
        $stmt->bindParam(':idpenjualan', $idpenjualan);
        $stmt->execute();
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sale) {
            $notajual = $sale['notajual'];
            $namabarang = $sale['namabarang'];
            $harga = $sale['harga'];
            $jumlah = $sale['jumlah'];
            $total = $sale['total'];
            $grandtotal = $sale['grandtotal'];
            $tanggalpenjualan = $sale['tanggalpenjualan'];
        } else {
            $errors[] = "Data penjualan tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $errors[] = "Gagal mengambil data penjualan: " . $e->getMessage();
    }
} else {
    $errors[] = "ID Penjualan tidak valid.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['idpenjualan']) && is_numeric($_POST['idpenjualan'])) {
        $idpenjualan = $_POST['idpenjualan'];
    } else {
        $errors[] = "ID Penjualan tidak valid untuk pembaruan.";
    }

    $notajual_new = trim($_POST['notajual']);
    $namabarang_new = trim($_POST['namabarang']);
    $harga_new = trim($_POST['harga']);
    $jumlah_new = trim($_POST['jumlah']);
    $total_new = trim($_POST['total']);
    $grandtotal_new = trim($_POST['grandtotal']);
    $tanggalpenjualan_new = trim($_POST['tanggalpenjualan']);

    // Basic Validations
    if (empty($notajual_new)) $errors[] = "Nota Jual tidak boleh kosong.";
    if (empty($namabarang_new)) $errors[] = "Nama Barang tidak boleh kosong.";
    if (!is_numeric($harga_new) || $harga_new < 0) $errors[] = "Harga harus angka positif.";
    if (!is_numeric($jumlah_new) || $jumlah_new <= 0) $errors[] = "Jumlah harus angka positif lebih dari 0.";
    if (!is_numeric($total_new) || $total_new < 0) $errors[] = "Total harus angka positif.";
    if (!is_numeric($grandtotal_new) || $grandtotal_new < 0) $errors[] = "Grand Total harus angka positif.";
    if (empty($tanggalpenjualan_new)) $errors[] = "Tanggal Penjualan tidak boleh kosong.";

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE penjualan SET notajual = :notajual, namabarang = :namabarang, harga = :harga, jumlah = :jumlah, total = :total, grandtotal = :grandtotal, tanggalpenjualan = :tanggalpenjualan WHERE idpenjualan = :idpenjualan");
            $stmt->bindParam(':notajual', $notajual_new);
            $stmt->bindParam(':namabarang', $namabarang_new);
            $stmt->bindParam(':harga', $harga_new);
            $stmt->bindParam(':jumlah', $jumlah_new);
            $stmt->bindParam(':total', $total_new);
            $stmt->bindParam(':grandtotal', $grandtotal_new);
            $stmt->bindParam(':tanggalpenjualan', $tanggalpenjualan_new);
            $stmt->bindParam(':idpenjualan', $idpenjualan);
            $stmt->execute();
            $success_message = "Data penjualan berhasil diperbarui!";
            // Update current values
            $notajual = $notajual_new;
            $namabarang = $namabarang_new;
            $harga = $harga_new;
            $jumlah = $jumlah_new;
            $total = $total_new;
            $grandtotal = $grandtotal_new;
            $tanggalpenjualan = $tanggalpenjualan_new;
        } catch (PDOException $e) {
            $errors[] = "Gagal memperbarui data penjualan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penjualan - Sistem Gudang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #2d3748, #4a5568); min-height: 100vh; font-family: 'Poppins', sans-serif; }
        .sidebar { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-right: 1px solid rgba(255, 255, 255, 0.2); height: 100vh; width: 250px; position: fixed; top: 0; left: 0; padding-top: 2rem; }
        .sidebar .nav-link { color: #ffffff; padding: 0.75rem 1.5rem; font-size: 1.1rem; display: flex; align-items: center; }
        .sidebar .nav-link i { margin-right: 0.75rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .content { margin-left: 250px; padding: 2rem; }
        .form-container { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px; padding: 2rem; max-width: 800px; margin: 2rem auto; }
        .form-container h2 { color: #ffffff; text-align: center; margin-bottom: 1.5rem; }
        .form-label { color: #ffffff; }
        .form-control { background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff; }
        .form-control:focus { background: rgba(255, 255, 255, 0.3); border-color: #f59e0b; color: #ffffff; box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.25); }
        .btn-custom { background: #f59e0b; color: #ffffff; border: none; border-radius: 10px; padding: 0.75rem 1.5rem; }
        .btn-custom:hover { background: #d97706; color: #ffffff; }
        .alert-custom { border-radius: 10px; }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column">
        <h4 class="text-white text-center mb-4">Sistem Gudang</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i><span>Home</span></a></li>
            <li class="nav-item"><a class="nav-link" href="pesanan.php"><i class="fas fa-shopping-cart"></i><span>Pesanan</span></a></li>
            <li class="nav-item"><a class="nav-link" href="produk.php"><i class="fas fa-boxes"></i><span>Produk</span></a></li>
            <li class="nav-item"><a class="nav-link active" href="penjualan.php"><i class="fas fa-money-bill-wave"></i><span>Penjualan</span></a></li>
            <li class="nav-item mt-auto"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </div>

    <div class="content">
        <div class="form-container">
            <h2>Edit Data Penjualan</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-custom">
                    <strong>Error!</strong>
                    <ul><?php foreach ($errors as $error) echo "<li>".htmlspecialchars($error)."</li>"; ?></ul>
                </div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-custom"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if (empty($errors) || $success_message): ?>
            <form method="POST" action="edit_penjualan.php?id=<?php echo htmlspecialchars($idpenjualan); ?>">
                <input type="hidden" name="idpenjualan" value="<?php echo htmlspecialchars($idpenjualan); ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggalpenjualan" class="form-label">Tanggal Penjualan</label>
                        <input type="date" class="form-control" id="tanggalpenjualan" name="tanggalpenjualan" value="<?php echo htmlspecialchars($tanggalpenjualan); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="notajual" class="form-label">Nota Jual</label>
                        <input type="text" class="form-control" id="notajual" name="notajual" value="<?php echo htmlspecialchars($notajual); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="namabarang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="namabarang" name="namabarang" value="<?php echo htmlspecialchars($namabarang); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="harga" class="form-label">Harga Satuan (Rp)</label>
                        <input type="number" class="form-control" id="harga" name="harga" value="<?php echo htmlspecialchars($harga); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo htmlspecialchars($jumlah); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="total" class="form-label">Total Harga (Rp)</label>
                        <input type="number" class="form-control" id="total" name="total" value="<?php echo htmlspecialchars($total); ?>" required>
                    </div>
                </div>
                 <div class="mb-3">
                    <label for="grandtotal" class="form-label">Grand Total (Rp)</label>
                    <input type="number" class="form-control" id="grandtotal" name="grandtotal" value="<?php echo htmlspecialchars($grandtotal); ?>" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-custom">Simpan Perubahan</button>
                    <a href="penjualan.php" class="btn btn-secondary">Kembali ke Daftar Penjualan</a>
                </div>
            </form>
            <?php elseif(!empty($errors) && (strpos(implode(" ", $errors), "Data penjualan tidak ditemukan") !== false || strpos(implode(" ", $errors), "ID Penjualan tidak valid") !== false)): ?>
                 <div class="text-center">
                     <a href="penjualan.php" class="btn btn-secondary">Kembali ke Daftar Penjualan</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
