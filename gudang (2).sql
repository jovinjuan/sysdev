-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2025 at 08:28 AM
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
  `namagudang` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gudang`
--

INSERT INTO `gudang` (`idgudang`, `namagudang`) VALUES
(2, 'Rak 1-A'),
(3, 'Rak 1-B'),
(4, 'Rak 1-C'),
(5, 'Rak 1-A'),
(6, 'Rak 1-B'),
(7, 'Rak 1-C'),
(8, 'Rak 1-A'),
(9, 'Rak 1-C');

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
(17, 3, 13, 1, 185000, 50000, 235000),
(18, 3, 18, 1, 300, 50000, 50300);

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `telepon` varchar(12) NOT NULL,
  `level` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama`, `password`, `telepon`, `level`) VALUES
(3, 'Jovin Juanlie', '$2y$10$3Q16aHJsGbNZm1DzRnOzCe3/vvxd3dvLXrdcQgdyDCzN023X7Py3K', '12345678901', 'User'),
(5, 'Admin', '$2y$10$DZizhMcOuIirQfropntN.uZTQu0XIhAfd4R0Lu92WLa4HHUpA9fPm', '12345678901', 'Admin'),
(6, 'tes', '$2y$10$/old2G4pQQRlSRoH8fjQ2OyhRIU7n8FGDu6ctHk4AAlvqj0rQsyj.', '5678904321', 'User');

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
  `tanggalpenjualan` date NOT NULL,
  `waktuinputjual` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`idpenjualan`, `notajual`, `kodenota`, `namabarang`, `harga`, `jumlah`, `total`, `grandtotal`, `tanggalpenjualan`, `waktuinputjual`) VALUES
(2, 'INV-001', '', 'Semen Tiga Roda 50kg', '75000', '5', '375000', '425000', '2025-05-06', '2025-05-06 23:02:25');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `idpesanan` int(11) NOT NULL,
  `idpengguna` int(11) NOT NULL,
  `idproduk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `alamatpengiriman` text NOT NULL,
  `status` enum('Diproses','Dikirim','Selesai') NOT NULL,
  `totalharga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`idpesanan`, `idpengguna`, `idproduk`, `jumlah`, `alamatpengiriman`, `status`, `totalharga`) VALUES
(9, 3, 2, 1, 'Jln. Imam Bonjol No. 77 ', 'Selesai', 700),
(10, 3, 7, 1, 'Jln. Imam Bonjol No. 77', 'Selesai', 25000),
(11, 3, 4, 5, 'Jalan Merdeka No. 7', 'Selesai', 125000),
(12, 3, 13, 5, 'Jalan Merdeka No.7', 'Selesai', 925000),
(13, 3, 12, 10, 'Jalan Merdeka No.7', 'Diproses', 7000),
(14, 3, 16, 5, 'Jalan Merdeka No.7', 'Diproses', 375000);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `idproduk` int(11) NOT NULL,
  `idgudang` int(11) NOT NULL,
  `namaproduk` text NOT NULL,
  `hargajual` int(11) NOT NULL,
  `stok` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`idproduk`, `idgudang`, `namaproduk`, `hargajual`, `stok`) VALUES
(11, 2, 'Semen Tiga Roda 50kg', 75000, '200'),
(12, 3, 'Batu Bata Merah', 700, '4990'),
(13, 4, 'Cat Tembok Dulux 5 Liter', 185000, '195'),
(14, 5, 'Paku Beton 5cm', 25000, '1000'),
(15, 6, 'Besi Beton Ulir 10mm 12m', 150000, '300'),
(16, 7, 'Keramik Roman 40x40', 75000, '195'),
(17, 8, 'Papan Kayu Jati 2m', 25000, '90'),
(18, 9, 'Pasir Bangunan 1 Kubik', 300, '10000');

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
  ADD PRIMARY KEY (`idproduk`),
  ADD KEY `fk_id_gudang` (`idgudang`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gudang`
--
ALTER TABLE `gudang`
  MODIFY `idgudang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `idkeranjang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `idpenjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `idpesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `idproduk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
