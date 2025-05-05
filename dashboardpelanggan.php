<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Gudang Pelanggan</title>
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
            font-size : 1.48rem;
            padding : 2px;
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
        .product-section {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .product-card {
            background-color: #4a5568;
            border-radius: 10px;
            padding: 1rem;
            width: 200px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #66708a;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.2);
        }
        .product-card h5 {
            margin: 0.5rem 0;
            font-size: 1.1rem;
            color: #ffffff;
        }
        .product-card p {
            margin: 0.25rem 0;
            font-size: 0.9rem;
            color: #d1d5db;
        }
        .btn-custom {
            background-color: #f59e0b;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
        }
        .btn-custom:hover {
            background-color: #d97706;
            color: #ffffff;
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
        }
        .tracker-step i {
            font-size: 1.5rem;
            color: #f59e0b;
        }
        .tracker-step p {
            margin: 0.25rem 0 0;
            font-size: 0.8rem;
            color: #d1d5db;
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
        @media (max-width: 768px) {
            .content {
                padding: 5rem 1rem 1rem;
            }
            .product-card {
                width: 150px;
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
        }
    </style>
</head>
<body>
    <div class="navbar">
    <h2 class = "fw-bold">Sistem Gudang</h2>
        <div>
        <a href="dashboardpelanggan.php" class="active">Dashboard</a>
        <a href="pesanproduk.php">Pesan Produk</a>
        <a href="pantaupengiriman.php">Pantau Pengiriman</a>
        <a href="#">Logout</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="dashboard-container">
            <h2 class="header-title mb-5">Selamat Datang, John Doe!</h2>
            <div class="order-section">
                <h4>Pesanan Terbaru</h4>
                <div class="order-card">
                    <p>ORD001 - Kardus 20x20 (50) - Rp 2.500.000</p>
                    <p>Status: Dikirim</p>
                </div>
                <div class="tracker">
                    <div class="tracker-step">
                        <i class="fas fa-box"></i>
                        <p>Diproses</p>
                    </div>
                    <div class="tracker-step">
                        <i class="fas fa-truck"></i>
                        <p>Dikirim</p>
                    </div>
                    <div class="tracker-step">
                        <i class="fas fa-check-circle"></i>
                        <p>Selesai</p>
                    </div>
                    <div class="tracker-line"></div>
                </div>
                <a href="pantau_pengiriman.php" class="btn btn-custom mt-3">Lihat Detail Pengiriman</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>