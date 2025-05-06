<?php
require "config.php";

// Cek apakah admin sudah login
$admin_id = $_SESSION['admin_id'] ?? null;
if (!isset($admin_id)) {
    header("Location: login.php");
    exit();
}

// Hitung total penjualan (tanpa filter waktu)
try {
    $sqlPenjualan = "SELECT SUM(grandtotal) as total_penjualan FROM penjualan";
    $queryPenjualan = $conn->prepare($sqlPenjualan);
    $queryPenjualan->execute();
    $resultPenjualan = $queryPenjualan->fetch(PDO::FETCH_ASSOC);
    $totalPenjualan = $resultPenjualan['total_penjualan'] ?? 0;

    // Hitung total pesanan (tanpa filter waktu)
    $sqlPesanan = "SELECT COUNT(idpesanan) as total_pesanan FROM pesanan";
    $queryPesanan = $conn->prepare($sqlPesanan);
    $queryPesanan->execute();
    $resultPesanan = $queryPesanan->fetch(PDO::FETCH_ASSOC);
    $totalPesanan = $resultPesanan['total_pesanan'] ?? 0;

    // Hitung total produk terdaftar
    $sqlProduk = "SELECT COUNT(idproduk) as total_produk FROM produk";
    $queryProduk = $conn->prepare($sqlProduk);
    $queryProduk->execute();
    $resultProduk = $queryProduk->fetch(PDO::FETCH_ASSOC);
    $totalProduk = $resultProduk['total_produk'] ?? 0;
} catch (PDOException $e) {
    $totalPenjualan = 0;
    $totalPesanan = 0;
    $totalProduk = 0;
    $error_message = "Terjadi kesalahan: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sistem Gudang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background: linear-gradient(135deg, #2d3748, #4a5568);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
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
            transition: width 0.3s ease;
        }
        .sidebar .nav-link {
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            transition: background 0.3s ease, color 0.3s ease;
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
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 2rem;
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
        }
        .card-icon {
            font-size: 3rem;
            color: #f59e0b;
            opacity: 0.9;
        }
        .card-title {
            color: #ffffff;
            font-weight: 600;
            font-size: 1.25rem;
        }
        .card-value {
            color: #f59e0b;
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 0 0 8px rgba(245, 158, 11, 0.5);
        }
        .card-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        .header-title {
            color: #ffffff;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }
            .sidebar .nav-link span {
                display: none;
            }
            .content {
                margin-left: 80px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <h4 class="text-white text-center mb-4">Sistem Gudang</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="index.php"><i class="fas fa-home"></i><span>Home</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pesanan.php"><i class="fas fa-shopping-cart"></i><span>Pesanan</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="produk.php"><i class="fas fa-boxes"></i><span>Produk</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="penjualan.php"><i class="fas fa-money-bill-wave"></i><span>Penjualan</span></a>
            </li>
            <li class="nav-item mt-auto">
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </li>
        </ul>
    </div>

    <!-- Dashboard Content -->
    <div class="content">
        <div class="dashboard-container">
            <h1 class="header-title text-center mb-5 fw-bold fs-2">Dashboard</h1>
            <div class="row g-4">
                <!-- Total Penjualan -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-icon mb-3">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h5 class="card-title">Total Penjualan</h5>
                        <h3 class="card-value">Rp <?php echo number_format($totalPenjualan, 0, ',', '.'); ?></h3>
                        <p class="card-text">Penjualan hingga <?php echo date('F Y'); ?></p>
                    </div>
                </div>
                <!-- Total Pesanan -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-icon mb-3">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h5 class="card-title">Total Pesanan</h5>
                        <h3 class="card-value"><?php echo $totalPesanan; ?></h3>
                        <p class="card-text">Pesanan hingga <?php echo date('F Y'); ?></p>
                    </div>
                </div>
                <!-- Total Produk -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-icon mb-3">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h5 class="card-title">Total Produk Terdaftar</h5>
                        <h3 class="card-value"><?php echo $totalProduk; ?></h3>
                        <p class="card-text">Produk di inventaris</p>
                    </div>
                </div>
            </div>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>