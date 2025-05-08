<?php
require "config.php";

// Cek apakah admin sudah login
$admin_id = $_SESSION['admin_id'] ?? null;
if (!isset($admin_id)) {
    header("Location: index.php");
    exit();
}

// Hitung total penjualan, pesanan, produk, dan produk stok nol
try {
    // Total Penjualan
    $sqlPenjualan = "SELECT SUM(grandtotal) as total_penjualan FROM penjualan";
    $queryPenjualan = $conn->prepare($sqlPenjualan);
    $queryPenjualan->execute();
    $resultPenjualan = $queryPenjualan->fetch(PDO::FETCH_ASSOC);
    $totalPenjualan = $resultPenjualan['total_penjualan'] ?? 0;

    // Total Pesanan (tidak termasuk yang dibatalkan)
    $sqlPesanan = "SELECT COUNT(idpesanan) as total_pesanan FROM pesanan WHERE status != 'Batal'";
    $queryPesanan = $conn->prepare($sqlPesanan);
    $queryPesanan->execute();
    $resultPesanan = $queryPesanan->fetch(PDO::FETCH_ASSOC);
    $totalPesanan = $resultPesanan['total_pesanan'] ?? 0;

    // Total Produk
    $sqlProduk = "SELECT COUNT(idproduk) as total_produk FROM produk";
    $queryProduk = $conn->prepare($sqlProduk);
    $queryProduk->execute();
    $resultProduk = $queryProduk->fetch(PDO::FETCH_ASSOC);
    $totalProduk = $resultProduk['total_produk'] ?? 0;

    // Produk dengan stok nol
    $sqlZeroStock = "SELECT p.namaproduk, g.namagudang 
                     FROM produk p 
                     JOIN gudang g ON p.idgudang = g.idgudang 
                     WHERE p.stok = 0";
    $queryZeroStock = $conn->prepare($sqlZeroStock);
    $queryZeroStock->execute();
    $zero_stock_products = $queryZeroStock->fetchAll(PDO::FETCH_ASSOC);

    // Fetch notifications for admin (semua notifikasi, tidak hanya pembatalan)
    $sqlNotifikasi = "SELECT idpengguna, pesan, statusdibaca
                      FROM notifikasi 
                      WHERE idpengguna = :admin_id AND statusdibaca = 'Belum Dibaca'
                      ORDER BY idnotifikasi DESC";
    $queryNotifikasi = $conn->prepare($sqlNotifikasi);
    $queryNotifikasi->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $queryNotifikasi->execute();
    $notifications = $queryNotifikasi->fetchAll(PDO::FETCH_ASSOC);

    // Count unread notifications
    $sqlUnread = "SELECT COUNT(*) 
                  FROM notifikasi 
                  WHERE idpengguna = :admin_id 
                  AND statusdibaca = 'Belum Dibaca'";
    $queryUnread = $conn->prepare($sqlUnread);
    $queryUnread->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $queryUnread->execute();
    $unread_count = $queryUnread->fetchColumn();
} catch (PDOException $e) {
    $totalPenjualan = 0;
    $totalPesanan = 0;
    $totalProduk = 0;
    $zero_stock_products = [];
    $notifications = [];
    $unread_count = 0;
    $error_message = "Terjadi kesalahan: " . $e->getMessage();
}

// Mark notification as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_read' && isset($_POST['notif_id'])) {
    $notif_id = $_POST['notif_id'];
    try {
        $sql = "UPDATE notifikasi SET statusdibaca = 'Dibaca' WHERE idnotifikasi = :notif_id AND idpengguna = :admin_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':notif_id', $notif_id, PDO::PARAM_INT);
        $query->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $query->execute();
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $error_message = "Gagal menandai notifikasi sebagai dibaca: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sistem Logistik</title>
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
        /* Notification styles */
        .notification-dropdown {
            position: relative;
            padding: 0.75rem 1.5rem;
        }
        .notification-bell {
            color: #ffffff;
            font-size: 1.2rem;
            cursor: pointer;
        }
        .notification-bell:hover {
            color: #f59e0b;
        }
        .notification-count {
            position: absolute;
            top: 5px;
            right: 10px;
            background-color: #e07a5f;
            color: #ffffff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
        }
        .dropdown-menu {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-height: 300px;
            overflow-y: auto;
        }
        .dropdown-item {
            color: #ffffff;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .dropdown-item.unread {
            background: rgba(245, 158, 11, 0.2);
            font-weight: 600;
        }
        .dropdown-item.read {
            background: transparent;
        }
        .dropdown-item:hover {
            background: rgba(245, 158, 11, 0.3);
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }
            .sidebar .nav-link span {
                display: none;
            }
            .sidebar .notification-dropdown span {
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
        <h4 class="text-white text-center mb-4">Sistem Logistik</h4>
        <!-- Navigation -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboardadmin.php"><i class="fas fa-home"></i><span>Home</span></a>
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
                        <p class="card-text">Pesanan aktif hingga <?php echo date('F Y'); ?></p>
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
            <!-- Display warning if there are products with zero stock -->
            <?php if (!empty($zero_stock_products)): ?>
                <div class="alert alert-warning mt-3 alert-dismissible fade show" role="alert">
                    <strong>Peringatan!</strong> Ada produk dengan stok kosong:
                    <ul>
                        <?php foreach ($zero_stock_products as $product): ?>
                            <li><?php echo htmlspecialchars($product['namaproduk']); ?> (Lokasi: <?php echo htmlspecialchars($product['namagudang']); ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>