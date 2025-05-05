<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
      color: #ffffff;
      margin: 0;
      font-size : 1.49rem;
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
      padding: 5rem 1rem 2rem;
    }
    .container {
      max-width: 900px;
      margin: 0 auto;
    }
    .welcome {
      text-align: center;
      color: #f1c40f;
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
      text-transform: uppercase;
    }
    .search-bar {
      width: 100%;
      max-width: 400px;
      padding: 10px 15px;
      border: 1px solid #f1c40f;
      border-radius: 20px;
      background-color: rgba(255, 255, 255, 0.05);
      color: #ecf0f1;
      font-size: 0.9rem;
      outline: none;
      display: block;
      margin: 0 auto 2rem;
    }
    .search-bar::placeholder {
      color: #95a5a6;
    }
    .search-bar:focus {
      border-color: #e67e22;
      box-shadow: 0 0 8px rgba(241, 196, 15, 0.3);
    }
    .order-cards {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    .order-card {
      background: rgba(44, 62, 80, 0.95);
      padding: 1.2rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .order-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
    }
    .order-card p {
      margin: 0.3rem 0;
      font-size: 0.9rem;
      color: #bdc3c7;
    }
    .order-card p strong {
      color: #ecf0f1;
    }
    .status-processing {
      color: #f1c40f;
    }
    .status-shipped {
      color: #3498db;
    }
    .status-delivered {
      color: #2ecc71;
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="navbar">
    <h2 class="fw-bold">Sistem Gudang</h2>
    <div>
      <a href="dashboardpelanggan.php">Dashboard</a>
      <a href="pesanproduk.php">Pesan Produk</a>
      <a href="pantaupengiriman.php" class="active">Pantau Pengiriman</a>
      <a href="#">Logout</a>
    </div>
  </div>

  <div class="content">
    <div class="container">
      <div class="welcome">Pantau Pengiriman</div>
      
      <input type="text" id="search-bar" class="search-bar" placeholder="Cari order ID atau produk...">

      <div id="order-cards" class="order-cards">
        <div class="order-card">
          <p><strong>Order ID:</strong> #001</p>
          <p><strong>Produk:</strong> Kardus 20x20</p>
          <p><strong>Jumlah:</strong> 2</p>
          <p><strong>Status:</strong> <span class="status-delivered">Terkirim</span></p>
          <p><strong>Estimasi Pengiriman:</strong> 05 Mei 2025</p>
        </div>
        <div class="order-card">
          <p><strong>Order ID:</strong> #002</p>
          <p><strong>Produk:</strong> Plastik Kemasan</p>
          <p><strong>Jumlah:</strong> 5</p>
          <p><strong>Status:</strong> <span class="status-shipped">Dikirim</span></p>
          <p><strong>Estimasi Pengiriman:</strong> 08 Mei 2025</p>
        </div>
        <div class="order-card">
          <p><strong>Order ID:</strong> #003</p>
          <p><strong>Produk:</strong> Botol 500ml</p>
          <p><strong>Jumlah:</strong> 3</p>
          <p><strong>Status:</strong> <span class="status-processing">Diproses</span></p>
          <p><strong>Estimasi Pengiriman:</strong> 10 Mei 2025</p>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
    const searchBar = document.getElementById('search-bar');
    const orderCards = document.getElementById('order-cards');
    const cards = Array.from(orderCards.getElementsByClassName('order-card'));

    searchBar.addEventListener('input', () => {
      const searchText = searchBar.value.toLowerCase();
      cards.forEach(card => {
        const orderId = card.querySelector('p:nth-child(1)').textContent.toLowerCase();
        const product = card.querySelector('p:nth-child(2)').textContent.toLowerCase();
        const matchesSearch = orderId.includes(searchText) || product.includes(searchText);
        card.style.display = matchesSearch ? '' : 'none';
      });
    });
  </script>
</body>
</html>