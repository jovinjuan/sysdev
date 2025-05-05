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
      padding: 5rem 20px 20px; /* Adjusted to account for fixed navbar */
    }
    .container {
      max-width: 1200px;
      margin: 0 auto;
    }
    .welcome {
      text-align: center;
      color: #e9c46a;
      font-size: 24px;
      font-weight: 500;
      margin-bottom: 30px;
      letter-spacing: 1px;
    }
    .search-bar {
      width: 100%;
      padding: 12px;
      margin-bottom: 30px;
      border: none;
      border-radius: 25px;
      background-color: #2a3b4c;
      color: #e5e5e5;
      font-size: 14px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      outline: none;
      transition: background-color 0.3s ease;
    }
    .search-bar::placeholder {
      color: #a0a0a0;
    }
    .search-bar:focus {
      background-color: #334455;
    }
    .products {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 25px;
    }
    .product-card {
      background: rgba(44, 62, 80, 0.9);
      padding: 1.5rem;
      border-radius: 15px;
      text-align: center;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    .product-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(241, 196, 15, 0.1), transparent);
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: 1; /* Ensure pseudo-element is below interactive elements */
    }
    .product-card:hover::before {
      opacity: 1;
    }
    .product-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
    }
    .product-card p {
      margin: 0.5rem 0;
      font-size: 0.9rem;
      color: #bdc3c7;
      position: relative;
      z-index: 2; /* Ensure text is above pseudo-element */
    }
    .product-card p:first-child {
      font-weight: 600;
      font-size: 1.2rem;
      color: #f1c40f;
      text-transform: uppercase;
    }
    .quantity-controls {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
      margin: 1rem auto;
      width: fit-content;
      padding: 0.5rem;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 10px;
      position: relative;
      z-index: 3; /* Ensure quantity controls are above pseudo-element */
    }
    .quantity-controls button {
      background: linear-gradient(135deg, #e67e22, #d35400);
      border: none;
      padding: 8px;
      width: 32px;
      height: 32px;
      color: white;
      border-radius: 50%;
      cursor: pointer;
      font-size: 1rem;
      font-weight: bold;
      transition: background 0.3s ease, transform 0.2s ease;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .quantity-controls button:hover {
      background: linear-gradient(135deg, #f39c12, #e67e22);
      transform: scale(1.1);
    }
    .quantity-controls input {
      width: 50px;
      text-align: center;
      padding: 5px;
      border: 1px solid #f1c40f;
      border-radius: 8px;
      background-color: rgba(255, 255, 255, 0.05);
      color: #ecf0f1;
      font-size: 0.9rem;
    }
    .product-card button.order-btn {
      background: linear-gradient(135deg, #e74c3c, #c0392b);
      border: none;
      padding: 12px;
      color: white;
      border-radius: 25px;
      cursor: pointer;
      width: 100%;
      font-size: 0.9rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: background 0.3s ease, transform 0.2s ease;
      position: relative;
      z-index: 2; /* Ensure order button is above pseudo-element */
    }
    .product-card button.order-btn:hover {
      background: linear-gradient(135deg, #ff6b6b, #e74c3c);
      transform: scale(1.03);
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="navbar">
    <h2 class = "fw-bold">Sistem Gudang</h2>
    <div>
      <a href="dashboardpelanggan.php">Dashboard</a>
      <a href="pesanproduk.php" class="active">Pesan Produk</a>
      <a href="pantaupengiriman.php">Pantau Pengiriman</a>
      <a href="#">Logout</a>
    </div>
  </div>

  <div class="content">
    <div class="container">
      <div class="welcome fs-2 mt-3">Katalog Produk</div>
      
      <input type="text" class="search-bar" placeholder="Cari produk...">
      
      <div class="products">
        <div class="product-card">
          <p>Kardus 20x20</p>
          <p>Rp 50.000</p>
          <p>Stok: 150</p>
          <div class="quantity-controls">
            <button class="decrement-btn">-</button>
            <input type="number" class="quantity-input" value="1" min="1" max="150">
            <button class="increment-btn">+</button>
          </div>
          <button class="order-btn">Pesan Sekarang</button>
        </div>
        <div class="product-card">
          <p>Plastik Kemasan</p>
          <p>Rp 10.000</p>
          <p>Stok: 300</p>
          <div class="quantity-controls">
            <button class="decrement-btn">-</button>
            <input type="number" class="quantity-input" value="1" min="1" max="300">
            <button class="increment-btn">+</button>
          </div>
          <button class="order-btn">Pesan Sekarang</button>
        </div>
        <div class="product-card">
          <p>Botol 500ml</p>
          <p>Rp 20.000</p>
          <p>Stok: 500</p>
          <div class="quantity-controls">
            <button class="decrement-btn">-</button>
            <input type="number" class="quantity-input" value="1" min="1" max="500">
            <button class="increment-btn">+</button>
          </div>
          <button class="order-btn">Pesan Sekarang</button>
        </div>
        <div class="product-card">
          <p>Kardus Besar</p>
          <p>Rp 75.000</p>
          <p>Stok: 200</p>
          <div class="quantity-controls">
            <button class="decrement-btn">-</button>
            <input type="number" class="quantity-input" value="1" min="1" max="200">
            <button class="increment-btn">+</button>
          </div>
          <button class="order-btn">Pesan Sekarang</button>
        </div>
        <div class="product-card">
          <p>Plastik Tebal</p>
          <p>Rp 15.000</p>
          <p>Stok: 250</p>
          <div class="quantity-controls">
            <button class="decrement-btn">-</button>
            <input type="number" class="quantity-input" value="1" min="1" max="250">
            <button class="increment-btn">+</button>
          </div>
          <button class="order-btn">Pesan Sekarang</button>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
     document.querySelectorAll('.quantity-controls').forEach(control => {
      const decrementBtn = control.querySelector('.decrement-btn');
      const incrementBtn = control.querySelector('.increment-btn');
      const input = control.querySelector('.quantity-input');

      decrementBtn.addEventListener('click', () => {
        let value = parseInt(input.value);
        const min = parseInt(input.min);
        if (value > min) {
          input.value = value - 1;
        }
      });

      incrementBtn.addEventListener('click', () => {
        let value = parseInt(input.value);
        const max = parseInt(input.max);
        if (value < max) {
          input.value = value + 1;
        }
      });

      input.addEventListener('input', () => {
        let value = parseInt(input.value);
        const min = parseInt(input.min);
        const max = parseInt(input.max);
        if (value < min) input.value = min;
        if (value > max) input.value = max;
      });
    });
  </script>
</body>
</html>