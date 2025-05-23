<?php
require "config.php";
$admin_id = $_SESSION['admin_id'];

if(isset($admin_id)){
    // Fetch all orders with customer and product details
    $sql = "
    SELECT 
        o.idpesanan, 
        u.nama AS customer_name, 
        p.namaproduk, 
        o.jumlah, 
        o.status,
        o.alamatpengiriman,
        o.tanggalpengiriman
    FROM pesanan o
    JOIN produk p ON o.idproduk = p.idproduk
    JOIN pengguna u ON o.idpengguna = u.id
    ";
    $query = $conn->prepare($sql);
    $query->execute();
    $orders = $query->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan - Sistem Gudang</title>
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
        }
        .table-custom {
            background: linear-gradient(135deg, #34415b, #5c6a82);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }
        .table-custom th, .table-custom td {
            border-color: rgba(255, 255, 255, 0.2);
            padding: 1rem;
            text-align: center;
        }
        .table-custom th {
            background: rgba(245, 158, 11, 0.3);
            color: #f59e0b;
            font-weight: 600;
        }
        .table-custom tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .btn-custom {
            background: #f59e0b;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            transition: background 0.3s ease;
            margin: 0 0.2rem;
        }
        .btn-custom:hover {
            background: #d97706;
            color: #ffffff;
        }
        .btn-success {
            background: #28a745;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            transition: background 0.3s ease;
            margin: 0 0.2rem;
        }
        .btn-success:hover {
            background: #218838;
            color: #ffffff;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
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
            .table-custom th, .table-custom td {
                font-size: 0.9rem;
                padding: 0.5rem;
            }
            .btn-custom, .btn-success {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <h4 class="text-white text-center mb-4">Sistem Logistik</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboardadmin.php"><i class="fas fa-home"></i><span>Home</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="pesanan.php"><i class="fas fa-shopping-cart"></i><span>Pesanan</span></a>
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

    <!-- Content -->
    <div class="content">
        <div class="dashboard-container">
            <h2 class="header-title text-center mb-4 fw-bold">Daftar Pesanan</h2>
            <div class="table-responsive">
                <table class="table table-custom">
                    <tr>
                        <th>Nomor Pesanan</th>
                        <th>Nama Pelanggan</th>
                        <th>Nama Produk</th>
                        <th>Jumlah Pesanan</th>
                        <th>Alamat Pengiriman</th>
                        <th>Status Pesanan</th>
                        <th>Tanggal Pengiriman</th>
                        <th>Aksi</th>
                    </tr>
                <?php if (count($orders) > 0): 
                    $i = 1;
                ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['namaproduk']); ?></td>
                                <td><?php echo htmlspecialchars($order['jumlah']); ?></td>
                                <td><?php echo htmlspecialchars($order['alamatpengiriman']); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                                <td><?php echo htmlspecialchars($order['tanggalpengiriman'] ?? 'Belum diatur'); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="updatestatuspesanan.php?idpesanan=<?php echo $order['idpesanan']; ?>&status=Dikirim" class="btn btn-sm btn-custom">Update Status</a>
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#deliveryModal-<?php echo $order['idpesanan']; ?>">Atur Pengiriman</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada pesanan yang masuk.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <div class="modal fade" id="deliveryModal-<?php echo $order['idpesanan']; ?>" tabindex="-1" aria-labelledby="deliveryModalLabel-<?php echo $order['idpesanan']; ?>" aria-hidden="true">
                <div class="modal-dialog d-flex justify-content-center">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="deliveryModalLabel-<?php echo $order['idpesanan']; ?>">Atur Pengiriman Pesanan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="aturpengiriman.php" method="POST">
                                <input type="hidden" name="idpesanan" value="<?php echo $order['idpesanan']; ?>">
                                <div class="mb-3">
                                    <label for="deliveryDate-<?php echo $order['idpesanan']; ?>" class="form-label">Estimasi Pengiriman</label>
                                    <input type="date" class="form-control" id="deliveryDate-<?php echo $order['idpesanan']; ?>" name="tanggalpengiriman" required>
                                </div>
                                <button type="submit" class="btn btn-success">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>