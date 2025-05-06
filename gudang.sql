-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2025 at 02:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gudang`
--

-- --------------------------------------------------------

--
-- Table structure for table `gudang`
--

CREATE TABLE `gudang` (
  `idgudang` int(11) NOT NULL,
  `namagudang` text NOT NULL,
  `deskripsi` text NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `idkeranjang` int(11) NOT NULL,
  `idpengguna` int(11) NOT NULL,
  `idproduk` int(11) NOT NULL,
  `jumlah` int(255) NOT NULL,
  `totalharga` int(255) NOT NULL,
  `biayapengiriman` int(255) NOT NULL,
  `grandtotal` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keranjang`
--

INSERT INTO `keranjang` (`idkeranjang`, `idpengguna`, `idproduk`, `jumlah`, `totalharga`, `biayapengiriman`, `grandtotal`) VALUES
(7, 2, 3, 5, 925000, 50000, 975000);

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `telepon` varchar(12) NOT NULL,
  `alamat` text NOT NULL,
  `level` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama`, `email`, `password`, `telepon`, `alamat`, `level`) VALUES
(1, 'Admin', 'admin123@gmail.com', 'admin', '1234567890', 'Jln. xxx No.xx', 'Admin'),
(2, 'User', 'user123@gmail.com', 'user', '0987654321', 'Jln yyy No. yy', 'User');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `idpenjualan` int(11) NOT NULL,
  `notajual` text NOT NULL,
  `kodenota` text NOT NULL,
  `namabarang` text NOT NULL,
  `harga` text NOT NULL,
  `jumlah` text NOT NULL,
  `total` text NOT NULL,
  `grandtotal` text NOT NULL,
  `uangpembeli` text NOT NULL,
  `kembalian` text NOT NULL,
  `tanggalpenjualan` date NOT NULL,
  `waktuinputjual` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `idpesanan` int(11) NOT NULL,
  `idpengguna` int(11) NOT NULL,
  `idproduk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `status` enum('Diproses','Dikirim','Selesai') NOT NULL,
  `totalharga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`idpesanan`, `idpengguna`, `idproduk`, `jumlah`, `status`, `totalharga`) VALUES
(1, 2, 1, 2, 'Diproses', 150000),
(2, 2, 2, 5, 'Diproses', 3500),
(3, 2, 4, 10, 'Diproses', 250000),
(4, 2, 5, 5, 'Diproses', 750000),
(5, 2, 7, 5, 'Diproses', 125000),
(6, 2, 1, 8, 'Diproses', 600000),
(7, 2, 8, 2, 'Diproses', 600),
(8, 2, 7, 2, 'Diproses', 50000);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `idproduk` int(11) NOT NULL,
  `namaproduk` text NOT NULL,
  `hargajual` int(11) NOT NULL,
  `stok` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`idproduk`, `namaproduk`, `hargajual`, `stok`) VALUES
(1, 'Semen Tiga Roda 50kg', 75000, '90'),
(2, 'Batu Bata Merah', 700, '4995'),
(3, 'Cat Tembok Dulux 5 liter', 185000, '200'),
(4, 'Paku Beton 5cm', 25000, '990'),
(5, 'Besi Beton Ulir 10mm 12m', 150000, '295'),
(6, 'Keramik Roman 40x40', 75000, '150'),
(7, 'Papan Kayu Jati 2m', 25000, '73'),
(8, 'Pasir Bangunan 1 Kubik', 300, '48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gudang`
--
ALTER TABLE `gudang`
  ADD PRIMARY KEY (`idgudang`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`idkeranjang`),
  ADD KEY `fk_id_pengguna` (`idpengguna`),
  ADD KEY `fk_id_produk` (`idproduk`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`idpenjualan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`idpesanan`),
  ADD KEY `fk_id_pengguna` (`idpengguna`),
  ADD KEY `fk_id_produk` (`idproduk`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`idproduk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gudang`
--
ALTER TABLE `gudang`
  MODIFY `idgudang` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `idkeranjang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `idpenjualan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `idpesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `idproduk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
