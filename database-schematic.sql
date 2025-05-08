-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 02:37 PM
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
(11, 'Rak 1-A');

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

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `idnotifikasi` int(11) NOT NULL,
  `idpengguna` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `statusdibaca` enum('Belum Dibaca','Dibaca','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`idnotifikasi`, `idpengguna`, `pesan`, `statusdibaca`) VALUES
(28, 3, 'Pesanan Anda (ORD043 - Pipa PVC Rucika) telah berhasil dibuat. Status: Diproses', 'Dibaca'),
(29, 1, 'Pesanan (ORD043 - Pipa PVC Rucika) telah dibatalkan oleh pelanggan.', 'Belum Dibaca'),
(30, 3, 'Pesanan Anda (ORD044 - Pipa PVC Rucika) telah berhasil dibuat. Status: Diproses', 'Belum Dibaca'),
(31, 3, 'Pesanan Anda (ORD044 - Pipa PVC Rucika) sedang dalam pengiriman! Status: Dikirim', 'Belum Dibaca'),
(32, 3, 'Pesanan Anda (ORD045 - Pipa PVC Rucika) telah berhasil dibuat. Status: Diproses', 'Belum Dibaca');

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
  `status` enum('Diproses','Dikirim','Diterima','Batal') NOT NULL,
  `totalharga` int(11) NOT NULL,
  `tanggalpengiriman` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`idpesanan`, `idpengguna`, `idproduk`, `jumlah`, `alamatpengiriman`, `status`, `totalharga`, `tanggalpengiriman`) VALUES
(45, 3, 20, 1, 'Jalan Madu No. 65', 'Diproses', 5000, '2025-05-21');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `idproduk` int(11) NOT NULL,
  `idgudang` int(11) NOT NULL,
  `namaproduk` text NOT NULL,
  `hargajual` int(11) NOT NULL,
  `stok` text NOT NULL,
  `berat` int(50) NOT NULL,
  `waktuperubahan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`idproduk`, `idgudang`, `namaproduk`, `hargajual`, `stok`, `berat`, `waktuperubahan`) VALUES
(20, 11, 'Pipa PVC Rucika', 5000, '55', 5, '2025-05-07');

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
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`idnotifikasi`),
  ADD KEY `fk_id_pengguna` (`idpengguna`);

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
  MODIFY `idgudang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `idkeranjang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `idnotifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
  MODIFY `idpesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `idproduk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
