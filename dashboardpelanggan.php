<?php
require "config.php";

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';

if (!$user_id) {
    header("Location: index.php");
    exit();
}

// Ambil input pencarian nomor resi dari GET
$search_resi = isset($_GET['resi']) ? trim($_GET['resi']) : '';
$search_idpesanan = null;
$search_message = '';

if ($search_resi !== '') {
    // Ekstrak angka dari nomor resi (misalnya, "ORD001" -> "001" -> 1)
    if (preg_match('/^ORD(\d+)$/', strtoupper($search_resi), $matches)) {
        $search_idpesanan = (int)$matches[1];
    } elseif (is_numeric($search_resi)) {
        $search_idpesanan = (int)$search_resi;
    } else {
        $search_message = "Format nomor resi tidak valid. Gunakan format ORDxxx atau angka.";
    }
}

// Fetch orders only if a valid search is performed
$orders = [];
if ($search_idpesanan !== null) {
    try {
        $sql = "SELECT p.idpesanan, p.jumlah, p.status, p.totalharga, pr.namaproduk 
                FROM pesanan p 
                JOIN produk pr ON p.idproduk = pr.idproduk 
                WHERE p.idpengguna = :user_id 
                AND p.status NOT IN ('Diterima', 'Batal')
                AND p.idpesanan = :idpesanan";
        
        $query = $conn->prepare($sql);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->bindParam(':idpesanan', $search_idpesanan, PDO::PARAM_INT);
        $query->execute();
        $orders = $query->fetchAll(PDO::FETCH_ASSOC);

        // Jika pencarian dilakukan tetapi tidak ada hasil
        if (empty($orders) && empty($search_message)) {
            $search_message = "Tidak ada pesanan ditemukan dengan nomor resi tersebut.";
        }
    } catch (PDOException $e) {
        $search_message = "Terjadi kesalahan saat mengambil data pesanan.";
        $orders = [];
    }
} elseif ($search_resi === '') {
    // Pesan default saat halaman dimuat tanpa pencarian
    $search_message = "Masukkan nomor resi untuk melihat status pesanan.";
}

// Fetch notifications for the user
try {
    $sql = "SELECT idnotifikasi, pesan, statusdibaca 
            FROM notifikasi 
            WHERE idpengguna = :user_id 
            ORDER BY idnotifikasi DESC";
    $query = $conn->prepare($sql);
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    $notifications = $query->fetchAll(PDO::FETCH_ASSOC);

    // Count unread notifications
    $sql = "SELECT COUNT(*) 
            FROM notifikasi 
            WHERE idpengguna = :user_id AND statusdibaca = 'Belum Dibaca'";
    $query = $conn->prepare($sql);
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    $unread_count = $query->fetchColumn();
} catch (PDOException $e) {
    $notifications = [];
    $unread_count = 0;
}

// Mark notification as read when viewed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_read' && isset($_POST['notif_id'])) {
    $notif_id = $_POST['notif_id'];
    try {
        $sql = "UPDATE notifikasi SET statusdibaca = 'Dibaca' WHERE idnotifikasi = :notif_id AND idpengguna = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':notif_id', $notif_id, PDO::PARAM_INT);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();
        echo "<script>window.location.href='dashboardpelanggan.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Gagal menandai notifikasi sebagai dibaca.'); window.location.href='dashboardpelanggan.php';</script>";
        exit();
    }
}

// Proses konfirmasi penerimaan (update to "Diterima")
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    try {
        $sql = "SELECT status FROM pesanan WHERE idpesanan = :order_id AND idpengguna = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();
        $order = $query->fetch(PDO::FETCH_ASSOC);

        if ($order && $order['status'] === 'Dikirim') {
            $sql = "UPDATE pesanan SET status = 'Diterima' WHERE idpesanan = :order_id AND idpengguna = :user_id";
            $query = $conn->prepare($sql);
            $query->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $query->execute();

            echo "<script>alert('Terima kasih! Status pesanan telah diperbarui.'); window.location.href='dashboardpelanggan.php';</script>";
            exit();
        } else {
            echo "<script>alert('Gagal memperbarui status. Pesanan belum dikirim atau tidak valid.'); window.location.href='dashboardpelanggan.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "<script>alert('Terjadi kesalahan. Silakan coba lagi.'); window.location.href='dashboardpelanggan.php';</script>";
        exit();
    }
}

// Proses pembatalan pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    try {
        // Mulai transaksi
        $conn->beginTransaction();

        // Ambil status dan produk untuk notifikasi
        $sql = "SELECT p.status, pr.namaproduk 
                FROM pesanan p 
                JOIN produk pr ON p.idproduk = pr.idproduk 
                WHERE p.idpesanan = :order_id AND p.idpengguna = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();
        $order = $query->fetch(PDO::FETCH_ASSOC);

        if ($order && $order['status'] === 'Diproses') {
            // Update status pesanan menjadi "Batal"
            $sql = "UPDATE pesanan SET status = 'Batal' WHERE idpesanan = :order_id AND idpengguna = :user_id";
            $query = $conn->prepare($sql);
            $query->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $query->execute();

            // Kirim notifikasi ke admin (misalnya idpengguna admin = 1)
            $admin_id = 1; // Ganti dengan ID admin yang sesuai di database Anda
            $namaproduk = $order['namaproduk'];
            $pesan = "Pesanan (ORD" . str_pad($order_id, 3, '0', STR_PAD_LEFT) . " - " . htmlspecialchars($namaproduk) . ") telah dibatalkan oleh pelanggan.";
            $status_dibaca = 'Belum Dibaca';
            $sql = "INSERT INTO notifikasi (idpengguna, pesan, statusdibaca) VALUES (:idpengguna, :pesan, :statusdibaca)";
            $query = $conn->prepare($sql);
            $query->bindParam(':idpengguna', $admin_id, PDO::PARAM_INT);
            $query->bindParam(':pesan', $pesan, PDO::PARAM_STR);
            $query->bindParam(':statusdibaca', $status_dibaca, PDO::PARAM_STR);
            $query->execute();

            // Commit transaksi
            $conn->commit();

            echo "<script>alert('Pesanan telah dibatalkan.'); window.location.href='dashboardpelanggan.php';</script>";
            exit();
        } else {
            $conn->rollBack();
            echo "<script>alert('Gagal membatalkan pesanan. Pesanan sudah dikirim atau tidak valid.'); window.location.href='dashboardpelanggan.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "<script>alert('Terjadi kesalahan. Silakan coba lagi.'); window.location.href='dashboardpelanggan.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Logistik Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #1a2b3c;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #ffffff;
            margin: 0;
        }
        .navbar {
            background-color: #3c4a5e;
            padding: 1rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
        }
        .navbar h2 {
            margin: 0;
            color: #ffffff;
            font-size: 1.48rem;
            padding: 2px;
        }
        .navbar a {
            color: #ffffff;
            margin-left: 1rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .navbar a:hover, .navbar a.active {
            color: #f59e0b;
        }
        .content {
            padding: 6rem 2rem 2rem;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #f59e0b;
            text-shadow: 0 0 5px rgba(245, 158, 11, 0.3);
        }
        .order-section {
            background-color: #4a5568;
            border-radius: 10px;
            padding: 1.5rem;
            border: 1px solid #66708a;
        }
        .order-section h4 {
            color: #f59e0b;
            margin-bottom: 1rem;
        }
        .order-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background-color: #5a6276;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }
        .order-card p {
            margin: 0;
            font-size: 0.9rem;
        }
        .tracker {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            position: relative;
        }
        .tracker-step {
            text-align: center;
            z-index: 1;
            position: relative;
        }
        .tracker-step i {
            font-size: 1.5rem;
            color: #f59e0b;
            transition: transform 0.3s ease;
        }
        .tracker-step.current i {
            transform: scale(1.1);
        }
        .tracker-step .circle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -85%);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(136, 129, 123, 0.4);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }
        .tracker-step.current .circle {
            opacity: 1;
            border-color: #e07a5f;
        }
        .tracker-step p {
            margin: 0.25rem 0 0;
            font-size: 0.8rem;
            color: #d1d5db;
        }
        .tracker-step.current p {
            color: #e07a5f;
            font-weight: 700;
        }
        .tracker-line {
            position: absolute;
            top: 12px;
            left: 10%;
            width: 80%;
            height: 2px;
            background-color: #f59e0b;
            opacity: 0.3;
            z-index: 0;
        }
        /* Notification styles */
        .notification-dropdown {
            position: relative;
            display: inline-block;
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
            top: -5px;
            right: -10px;
            background-color: #e07a5f;
            color: #ffffff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
        }
        .dropdown-menu {
            background-color: #4a5568;
            border: 1px solid #66708a;
            max-height: 300px;
            overflow-y: auto;
        }
        .dropdown-item {
            color: #ffffff;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .dropdown-item.unread {
            background-color: #5a6276;
            font-weight: 600;
        }
        .dropdown-item.read {
            background-color: #4a5568;
        }
        .dropdown-item:hover {
            background-color: #6b7280;
        }
        /* Search bar styles */
        .search-container {
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .search-container .input-group {
            background-color: #5a6276;
            border-radius: 5px;
            overflow: hidden;
        }
        .search-container .form-control {
            background-color: transparent;
            border: none;
            color: #ffffff;
        }
        .search-container .form-control:focus {
            box-shadow: none;
            background-color: transparent;
            color: #ffffff;
        }
        .search-container .form-control::placeholder {
            color: #d1d5db;
        }
        .search-container .btn-search {
            background-color: #f59e0b;
            border: none;
            color: #ffffff;
            transition: background-color 0.3s ease;
        }
        .search-container .btn-search:hover {
            background-color: #e07a5f;
        }
        .search-container .btn-reset {
            background-color: #6b7280;
            border: none;
            color: #ffffff;
            transition: background-color 0.3s ease;
        }
        .search-container .btn-reset:hover {
            background-color: #e07a5f;
        }
        .default-message {
            text-align: center;
            color: #d1d5db;
            font-size: 1rem;
        }
        @media (max-width: 768px) {
            .content {
                padding: 5rem 1rem 1rem;
            }
            .order-card {
                flex-direction: column;
                gap: 0.5rem;
                text-align: center;
            }
            .tracker-step i {
                font-size: 1.2rem;
            }
            .tracker-step p {
                font-size: 0.7rem;
            }
            .search-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h2 class="fw-bold">Sistem Logistik</h2>
        <div>
            <a href="dashboardpelanggan.php" class="active">Dashboard</a>
            <a href="pesanproduk.php">Pesan Produk</a>
            <a href="keranjang.php"><i class="fa-solid fa-cart-shopping" style="color: #ffffff;"></i></a>
            <div class="notification-dropdown ms-3">
                <i class="fa-solid fa-bell notification-bell" data-bs-toggle="dropdown"></i>
                <?php if ($unread_count > 0): ?>
                    <span class="notification-count"><?php echo $unread_count; ?></span>
                <?php endif; ?>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php if ($notifications): ?>
                        <?php foreach ($notifications as $notif): ?>
                            <li>
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="notif_id" value="<?php echo htmlspecialchars($notif['idnotifikasi']); ?>">
                                    <button type="submit" class="dropdown-item <?php echo $notif['statusdibaca'] === 'Belum Dibaca' ? 'unread' : 'read'; ?>">
                                        <?php echo htmlspecialchars($notif['pesan']); ?>
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a class="dropdown-item">Tidak ada notifikasi.</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="dashboard-container">
            <h2 class="header-title mb-5">Selamat Datang, <?php echo htmlspecialchars($username); ?></h2>

            <!-- Search Bar -->
            <div class="search-container">
                <form method="GET" action="" class="input-group">
                    <input type="text" name="resi" class="form-control" placeholder="Cari nomor resi (contoh: ORD001)" value="<?php echo htmlspecialchars($search_resi); ?>">
                    <button type="submit" class="btn btn-search"><i class="fas fa-search"></i> Cari</button>
                    <?php if ($search_resi !== ''): ?>
                        <a href="dashboardpelanggan.php" class="btn btn-reset"><i class="fas fa-times"></i> Reset</a>
                    <?php endif; ?>
                </form>
                <?php if ($search_message): ?>
                    <div class="alert alert-warning mt-2" role="alert">
                        <?php echo htmlspecialchars($search_message); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tampilan Pesanan (Hanya Muncul Jika Pencarian Valid) -->
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-section my-5">
                        <h4>Pesanan</h4>
                        <div class="order-card mb-5">
                            <p>ORD<?php echo str_pad($order['idpesanan'], 3, '0', STR_PAD_LEFT); ?> - <?php echo htmlspecialchars($order['namaproduk']); ?> (<?php echo htmlspecialchars($order['jumlah']); ?>) - Rp <?php echo number_format($order['totalharga'], 0, ',', '.'); ?></p>
                            <p class="status">Status: <?php echo htmlspecialchars($order['status']); ?></p>
                        </div>
                        <div class="tracker">
                            <?php
                            $steps = ['Diproses', 'Dikirim', 'Diterima'];
                            $currentIndex = array_search($order['status'], $steps);
                            ?>
                            <div class="tracker-step <?php echo $currentIndex === 0 ? 'current' : ''; ?>">
                                <i class="fas fa-box mb-3"></i>
                                <div class="circle"></div>
                                <p>Diproses</p>
                            </div>
                            <div class="tracker-step <?php echo $currentIndex === 1 ? 'current' : ''; ?>">
                                <i class="fas fa-truck mb-3"></i>
                                <div class="circle"></div>
                                <p>Dikirim</p>
                            </div>
                            <div class="tracker-step <?php echo $currentIndex === 2 ? 'current' : ''; ?>">
                                <i class="fas fa-check-circle mb-3"></i>
                                <div class="circle"></div>
                                <p>Diterima</p>
                            </div>
                            <div class="tracker-line"></div>
                        </div>
                        <!-- Form untuk konfirmasi penerimaan -->
                        <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin telah menerima pesanan ini?');" class="d-inline">
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['idpesanan']); ?>">
                            <input type="hidden" name="action" value="confirm">
                            <button type="submit" class="btn btn-success mt-3" 
                                    <?php echo $order['status'] !== 'Dikirim' ? 'disabled' : ''; ?>>Terima</button>
                        </form>
                        <!-- Form untuk pembatalan pesanan -->
                        <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');" class="d-inline">
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['idpesanan']); ?>">
                            <input type="hidden" name="action" value="cancel">
                            <button type="submit" class="btn btn-danger mt-3 ms-2" 
                                    <?php echo $order['status'] !== 'Diproses' ? 'disabled' : ''; ?>>Batal</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>