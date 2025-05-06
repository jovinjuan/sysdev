<?php
require "config.php";
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
      font-size: 1.49rem;
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
      padding: 5rem 20px 20px;
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
    .search-container {
      display: flex;
      align-items: center;
      width: 100%;
      margin-bottom: 30px;
    }
    .search-bar {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 25px 0 0 25px;
      background-color: #2a3b4c;
      color: #e5e5e5;
      font-size: 14px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      outline: none;
      transition: background-color 0.3s ease;
      flex-grow: 1;
    }
    .search-bar::placeholder {
      color: #a0a0a0;
    }
    .search-bar:focus {
      background-color: #334455;
    }
    .search-button {
      padding: 12px 15px;
      border: none;
      border-radius: 0 25px 25px 0;
      background-color: #e67e22;
      color: white;
      font-size: 14px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .search-button:hover {
      background-color: #f39c12;
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
      z-index: 1;
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
      z-index: 2;
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
      z-index: 3;
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
      z-index: 2;
    }
    .product-card button.order-btn:hover {
      background: linear-gradient(135deg, #ff6b6b, #e74c3c);
      transform: scale(1.03);
    }
    .hidden {
      display: none;
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="navbar">
    <h2 class="fw-bold">Sistem Gudang</h2>
    <div>
      <a href="dashboardpelanggan.php">Dashboard</a>
      <a href="pesanproduk.php" class="active">Pesan Produk</a>
      <a href="keranjang.php"><i class="fa-solid fa-cart-shopping" style="color: #ffffff;"></i></a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <div class="content">
    <div class="container">
      <div class="welcome fs-2 mt-3">Katalog Produk</div>
      
      <div class="search-container">
        <input type="text" class="search-bar" placeholder="Cari produk..." id="searchBar">
        <button class="search-button" id="searchButton"><i class="bi bi-search"></i></button>
      </div>
      
      <div class="products" id="productsContainer">
      <?php
        $sql = "SELECT * FROM produk"; 
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            foreach ($result as $row) {
                ?>
                <div class="product-card" data-name="<?php echo htmlspecialchars(strtolower($row['namaproduk'])); ?>">
                    <p><?php echo htmlspecialchars($row['namaproduk']); ?></p>
                    <p>Rp <?php echo htmlspecialchars($row['hargajual']); ?></p>
                    <p>Stok: <?php echo $row['stok']; ?></p>
                    <form action="tambahkeranjang.php" method="POST">
                    <div class="quantity-controls">
                        <input type="hidden" name="idproduct" value="<?php echo htmlspecialchars($row['idproduk']); ?>">
                        <input type="hidden" name="hargajual" value="<?php echo htmlspecialchars($row['hargajual']); ?>">
                        <button class="decrement-btn" type="button">-</button>
                        <input type="number" name="jumlah" class="quantity-input" value="1" min="1" max="<?php echo $row['stok']; ?>">
                        <button class="increment-btn" type="button">+</button>
                    </div>
                    <button class="order-btn" type="submit" name="tambah">Tambahkan ke keranjang</button>
                    </form>
                </div>
                <?php
            }
        } else {
            echo "<p>Tidak ada produk tersedia.</p>";
        }
      ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
    // Quantity controls functionality
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

    // Search functionality
    const searchBar = document.getElementById('searchBar');
    const searchButton = document.getElementById('searchButton');
    const productsContainer = document.getElementById('productsContainer');
    const productCards = productsContainer.querySelectorAll('.product-card');

    searchButton.addEventListener('click', () => {
      const searchQuery = searchBar.value.toLowerCase().trim();

      productCards.forEach(card => {
        const productName = card.getAttribute('data-name');
        if (productName.includes(searchQuery)) {
          card.classList.remove('hidden');
        } else {
          card.classList.add('hidden');
        }
      });

      // Show "No products" message if no matches found
      const visibleCards = productsContainer.querySelectorAll('.product-card:not(.hidden)');
      if (visibleCards.length === 0) {
        if (!productsContainer.querySelector('.no-products')) {
          const noProductsMessage = document.createElement('p');
          noProductsMessage.className = 'no-products';
          noProductsMessage.textContent = 'Tidak ada produk yang cocok.';
          productsContainer.appendChild(noProductsMessage);
        }
      } else {
        const noProductsMessage = productsContainer.querySelector('.no-products');
        if (noProductsMessage) {
          noProductsMessage.remove();
        }
      }
    });
  </script>
</body>
</html>