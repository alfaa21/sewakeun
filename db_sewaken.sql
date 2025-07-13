-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2025 at 12:07 PM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sewaken`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT '1',
  `duration` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `qty`, `duration`, `created_at`, `updated_at`) VALUES
(20, 4, 14, 1, 1, '2025-07-09 16:01:34', '2025-07-09 16:01:34'),
(21, 4, 13, 2, 1, '2025-07-09 16:01:36', '2025-07-12 04:53:52'),
(23, 2, 13, 1, 1, '2025-07-12 14:41:31', '2025-07-12 14:41:31'),
(24, 2, 12, 1, 1, '2025-07-12 15:21:22', '2025-07-12 15:21:22');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `produk_id`, `user_id`, `sender`, `message`, `created_at`) VALUES
(1, 9, 2, 'user', 'halo min', '2025-07-09 00:18:02'),
(2, 9, 2, 'admin', 'haloo juga', '2025-07-09 12:46:34'),
(3, 9, 3, 'user', 'apakah ready stok?', '2025-07-09 12:54:03'),
(4, 9, 2, 'admin', 'oi', '2025-07-09 13:02:28'),
(5, 9, 2, 'admin', 'hai', '2025-07-09 13:11:50'),
(6, 9, 3, 'user', 'halo', '2025-07-09 13:22:26'),
(7, 9, 3, 'user', 'admin', '2025-07-09 13:22:34'),
(8, 9, 2, 'admin', 'apaan', '2025-07-09 13:23:33'),
(9, 9, 3, 'user', 'halo', '2025-07-09 13:27:22'),
(10, 9, 3, 'user', 'oi', '2025-07-09 13:27:27'),
(11, 9, 2, 'admin', 'halo', '2025-07-09 13:27:35'),
(12, 9, 3, 'user', 'assalamualaikum', '2025-07-09 13:31:44'),
(13, 9, 3, 'user', 'hei', '2025-07-09 14:06:53'),
(14, 9, 3, 'admin', 'waalaikumsalam', '2025-07-09 14:07:44'),
(15, 9, 3, 'admin', 'kenapa', '2025-07-09 14:08:14'),
(16, 9, 3, 'admin', 'woy', '2025-07-09 14:08:57'),
(17, 9, 3, 'user', 'min', '2025-07-09 14:18:29'),
(18, 9, 3, 'admin', 'gimana bos', '2025-07-09 14:18:45'),
(19, 7, 4, 'user', 'masih min?', '2025-07-09 20:29:58'),
(20, 7, 4, 'admin', 'masih om', '2025-07-09 20:30:34'),
(21, 8, 4, 'user', 'masih om?', '2025-07-09 20:31:10'),
(22, 8, 4, 'admin', 'hooh', '2025-07-09 20:31:50'),
(23, 13, 4, 'user', 'masih kah min?', '2025-07-10 08:09:09'),
(24, 15, 4, 'user', 'masih kah', '2025-07-10 08:57:07'),
(25, 7, 4, 'user', 'ada min?', '2025-07-11 16:21:09'),
(26, 15, 2, 'user', 'wow beautiful amazingg papa mau 5', '2025-07-11 17:06:27'),
(27, 7, 4, 'user', 'saya mau 5', '2025-07-11 17:34:48'),
(28, 7, 4, 'admin', 'jadi ga', '2025-07-12 15:19:36'),
(29, 7, 4, 'user', 'jadiii', '2025-07-12 15:20:43'),
(30, 7, 4, 'user', 'COD Kaliurang ya', '2025-07-12 15:24:13'),
(31, 7, 4, 'admin', 'oke gan', '2025-07-12 15:27:19'),
(32, 9, 2, 'user', 'ready?', '2025-07-12 16:24:19'),
(33, 13, 2, 'user', 'halo', '2025-07-12 16:26:30'),
(34, 8, 2, 'user', 'ini berapa om', '2025-07-12 16:33:19'),
(35, 8, 2, 'admin', 'buta kau? udah ada harganya liat baek baek', '2025-07-12 16:34:25'),
(36, 8, 2, 'user', 'galak amat om', '2025-07-12 16:41:25');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`) VALUES
(1, 'Elektronik'),
(2, 'Kamera'),
(3, 'Aksesoris'),
(5, 'Fashion'),
(6, 'Perabotan'),
(7, 'Outdoor');

-- --------------------------------------------------------

--
-- Table structure for table `kurir`
--

CREATE TABLE `kurir` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `biaya` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kurir`
--

INSERT INTO `kurir` (`id`, `nama`, `biaya`) VALUES
(1, 'J&T Express', 25000),
(2, 'Gojek Express', 20000),
(3, 'Grab Express', 30000),
(4, 'JNE', 22000);

-- --------------------------------------------------------

--
-- Table structure for table `lokasi`
--

CREATE TABLE `lokasi` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lokasi`
--

INSERT INTO `lokasi` (`id`, `nama`) VALUES
(1, 'Bantul'),
(2, 'Sewon'),
(3, 'Kaliurang'),
(4, 'Kalasan'),
(5, 'Sleman'),
(6, 'Kota baru');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `message_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_read_by_admin` tinyint(1) DEFAULT '0',
  `reply_message` text,
  `reply_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(15,2) NOT NULL,
  `status` enum('pending','processed','shipped','completed','cancelled') DEFAULT 'pending',
  `shipping_address` text,
  `payment_method` varchar(50) DEFAULT NULL,
  `rental_start_date` date DEFAULT NULL,
  `rental_end_date` date DEFAULT NULL,
  `proof_of_payment` varchar(255) DEFAULT NULL,
  `admin_notes` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_date`, `total_amount`, `status`, `shipping_address`, `payment_method`, `rental_start_date`, `rental_end_date`, `proof_of_payment`, `admin_notes`) VALUES
(1, 2, '2025-07-02 21:18:30', '150000.00', 'pending', 'Alamat Sementara', 'Transfer Bank', '2025-07-02', '2025-07-02', NULL, 'wewewe');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_unit` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `rental_duration` int(11) NOT NULL,
  `rental_duration_unit` enum('hari','minggu','bulan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_per_unit`, `subtotal`, `rental_duration`, `rental_duration_unit`) VALUES
(1, 1, 1, 1, '150000.00', '150000.00', 1, 'hari');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `amount` decimal(15,2) NOT NULL,
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `harga` decimal(15,2) NOT NULL,
  `stock` int(11) DEFAULT '0',
  `duration_unit` enum('hari','minggu','bulan') DEFAULT 'hari',
  `max_duration` int(11) NOT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lokasi_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT '3'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `kategori_id`, `gambar`, `deskripsi`, `harga`, `stock`, `duration_unit`, `max_duration`, `is_available`, `created_at`, `updated_at`, `lokasi_id`, `admin_id`) VALUES
(1, 'Kamera Sony A7III', 2, 'assets/images/th (2).jpeg', 'Kamera mirrorless profesional untuk fotografi dan videografi.', '150000.00', 4, 'hari', 1, 1, '2025-06-29 21:30:46', '2025-07-02 21:18:30', 1, 3),
(2, 'Drone DJI Mini 3', 1, 'assets/images/th (3).jpeg', 'Drone ringan dengan kamera 4K dan waktu terbang lama.', '200000.00', 4, 'hari', 1, 1, '2025-06-29 21:30:46', '2025-07-02 21:32:18', 2, 3),
(6, 'Kamera', 2, 'assets/images/images.jpeg', 'kamera', '20000.00', 5, 'hari', 3, 1, '2025-07-02 20:48:23', '2025-07-13 00:07:56', 1, 3),
(7, 'Playstation', 1, 'assets/images/playstation-5.jpg', 'Ps murah 2 hari diskom', '40000.00', 3, 'hari', 2, 1, '2025-07-02 22:17:35', '2025-07-07 13:25:45', 4, 3),
(8, 'Laptop', 1, 'assets/images/6449520_rd.avif', 'Laptop gaming mantab', '50000.00', 2, 'hari', 2, 1, '2025-07-02 22:18:09', '2025-07-08 22:27:48', 4, 3),
(9, 'Celana Outdoor Eiger', 3, 'assets/images/Celana outdoor.jpeg', 'Celana cocok untuk hiking dan aktivitas outdoor\r\nsize L-XXL', '30000.00', 3, 'hari', 4, 1, '2025-07-08 12:34:56', '2025-07-08 22:16:16', 3, 3),
(10, 'Kamera Leica', 2, 'assets/images/DSLR-LEICA-260x200.jpg', 'Kamera leica\r\ntipe AXX\r\nTahun 2020', '30000.00', 5, 'hari', 3, 1, '2025-07-09 20:40:01', '2025-07-12 23:57:42', 5, 3),
(11, 'Kamera Nikon AA', 2, 'assets/images/Kameranikon.png', 'Kamera Nikon\r\ntipe AA New arrival\r\nTahun 2020 4k', '45000.00', 4, 'hari', 2, 1, '2025-07-09 20:41:01', '2025-07-13 17:05:53', 6, 3),
(12, 'Kamera Samsung', 2, 'assets/images/Kamera samsung.jpeg', 'Kamera samsung\r\ntipe AXX\r\nMade in Korea', '25000.00', 3, 'hari', 5, 1, '2025-07-09 20:41:54', '2025-07-11 16:57:47', 5, 3),
(13, 'Kamera Lumix', 2, 'assets/images/Kamera lumix.jpeg', 'Lumix the best camera 2026\r\ncocok memfoto alien\r\n16k jernih ', '45000.00', 2, 'hari', 4, 1, '2025-07-09 21:32:44', '2025-07-12 23:50:23', 6, 3),
(14, 'Kamera sony', 2, 'assets/images/Kamera sony.jpg', 'alamakk harga termurah se-sleman woyy buruan', '50000.00', 3, 'hari', 4, 1, '2025-07-09 22:52:52', '2025-07-13 00:11:41', 5, 3),
(15, 'Kamera Nikon Gx7', 2, 'assets/images/Nikon GX 7.jpg', 'gacor ini mahal', '90000.00', 4, 'hari', 5, 1, '2025-07-09 23:15:40', '2025-07-13 01:05:11', 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text,
  `review_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `review_date`) VALUES
(1, 15, 2, 4, 'baguslah lumayan tapi buttonnya jepretnya agak kureng', '2025-07-13 16:54:19'),
(2, 14, 2, 5, 'joss bisa memfoto alien cuy', '2025-07-13 17:00:43'),
(3, 11, 2, 5, 'bagus kamera profesional josjis', '2025-07-13 17:06:57');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_saldo`
--

CREATE TABLE `riwayat_saldo` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tipe` enum('topup','sewa','denda','refund','lainnya') NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `tanggal` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `saldo_setelah` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `riwayat_saldo`
--

INSERT INTO `riwayat_saldo` (`id`, `user_id`, `tipe`, `nominal`, `keterangan`, `tanggal`, `saldo_setelah`) VALUES
(1, 2, 'topup', '50000.00', 'Top up via ShopeePay', '2025-07-02 11:27:23', '50000.00'),
(2, 2, 'topup', '50000.00', 'Top up via ShopeePay', '2025-07-02 11:29:57', '100000.00'),
(3, 2, 'topup', '60000.00', 'Top up via ShopeePay', '2025-07-02 11:30:17', '160000.00'),
(4, 2, 'topup', '70000.00', 'Top up via Transfer Bank', '2025-07-02 11:30:30', '230000.00'),
(5, 4, 'topup', '50000.00', 'Top up via GoPay', '2025-07-02 11:32:30', '50000.00'),
(6, 2, 'sewa', '150000.00', 'Pembayaran sewa produk ID 4', '2025-07-02 13:34:54', '80000.00'),
(7, 1, '', '150000.00', 'Penerimaan sewa dari user ID 2', '2025-07-02 13:34:54', '150000.00'),
(8, 2, 'sewa', '40000.00', 'Pembayaran sewa produk ID 5', '2025-07-02 13:52:52', '40000.00'),
(9, 2, 'topup', '700000.00', 'Top up via ShopeePay', '2025-07-02 14:14:08', '740000.00'),
(10, 2, 'sewa', '60000.00', 'Penyewaan produk Kamera', '2025-07-02 21:23:36', '680000.00'),
(11, 4, 'topup', '60000.00', 'Top up via Transfer Bank', '2025-07-07 13:40:08', '110000.00'),
(12, 4, 'topup', '80000.00', 'Top up via ShopeePay', '2025-07-07 13:40:21', '190000.00'),
(13, 4, 'topup', '10000.00', 'Top up via Transfer Bank', '2025-07-07 13:40:29', '200000.00'),
(14, 4, 'sewa', '100000.00', 'Pembayaran sewa produk ID 8', '2025-07-07 13:40:51', '100000.00'),
(15, 4, 'topup', '50000.00', 'Top up via Transfer Bank', '2025-07-08 13:20:52', '150000.00'),
(16, 2, 'sewa', '120000.00', 'Pembayaran sewa produk ID 9', '2025-07-08 14:59:26', '560000.00'),
(17, 2, 'sewa', '170000.00', 'Penyewaan produk ID 8', '2025-07-08 22:27:48', '390000.00'),
(18, 4, 'topup', '600000.00', 'Top up via Transfer Bank', '2025-07-10 08:53:44', '750000.00'),
(19, 4, 'sewa', '450000.00', 'Penyewaan produk ID 15', '2025-07-10 08:53:52', '300000.00'),
(20, 4, 'sewa', '125000.00', 'Penyewaan produk ID 12', '2025-07-11 16:57:47', '175000.00'),
(21, 2, 'sewa', '100000.00', 'Penyewaan produk ID 14', '2025-07-12 21:14:16', '290000.00'),
(22, 2, 'sewa', '270000.00', 'Penyewaan produk ID 15', '2025-07-12 21:18:50', '20000.00'),
(23, 2, 'topup', '5000000.00', 'Top up via Transfer Bank', '2025-07-12 21:23:05', '5020000.00'),
(24, 2, 'sewa', '75000.00', 'Penyewaan produk ID 14', '2025-07-12 21:23:40', '4945000.00'),
(25, 2, 'sewa', '45000.00', 'Penyewaan produk ID 13', '2025-07-12 22:41:38', '4900000.00'),
(26, 2, 'sewa', '40000.00', 'Penyewaan produk ID 6', '2025-07-12 23:00:30', '4860000.00'),
(27, 2, 'sewa', '45000.00', 'Penyewaan produk ID 6', '2025-07-12 23:02:20', '4815000.00'),
(28, 2, 'sewa', '45000.00', 'Penyewaan produk ID 13', '2025-07-12 23:05:31', '4770000.00'),
(29, 2, 'sewa', '20000.00', 'Penyewaan produk ID 6', '2025-07-12 23:47:22', '4750000.00'),
(30, 2, 'sewa', '45000.00', 'Penyewaan produk ID 13', '2025-07-12 23:50:23', '4705000.00'),
(31, 2, 'sewa', '45000.00', 'Penyewaan produk ID 6', '2025-07-12 23:57:02', '4660000.00'),
(32, 2, 'refund', '45000.00', 'Refund pembatalan transaksi ID 36', '2025-07-12 23:57:12', '4705000.00'),
(33, 2, 'sewa', '30000.00', 'Penyewaan produk ID 10', '2025-07-12 23:57:42', '4675000.00'),
(34, 2, 'sewa', '20000.00', 'Penyewaan produk ID 6', '2025-07-13 00:07:56', '4655000.00'),
(35, 2, 'sewa', '50000.00', 'Penyewaan produk ID 14', '2025-07-13 00:11:41', '4605000.00'),
(36, 2, 'sewa', '90000.00', 'Penyewaan produk ID 15', '2025-07-13 00:16:15', '4515000.00'),
(37, 2, 'sewa', '45000.00', 'Penyewaan produk ID 11', '2025-07-13 00:33:53', '4470000.00'),
(38, 2, 'sewa', '115000.00', 'Penyewaan produk ID 15', '2025-07-13 00:56:12', '4330000.00'),
(39, 3, 'sewa', '115000.00', 'Penerimaan sewa dari user ID 2', '2025-07-13 01:05:11', '115000.00'),
(40, 2, 'sewa', '70000.00', 'Penyewaan produk ID 11', '2025-07-13 17:01:20', '4215000.00'),
(41, 3, 'sewa', '70000.00', 'Penerimaan sewa dari user ID 2', '2025-07-13 17:05:53', '185000.00');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_topup`
--

CREATE TABLE `riwayat_topup` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `metode` varchar(50) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','berhasil','gagal') NOT NULL DEFAULT 'berhasil'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `riwayat_topup`
--

INSERT INTO `riwayat_topup` (`id`, `user_id`, `nominal`, `metode`, `tanggal`, `status`) VALUES
(1, 2, '50000.00', 'ShopeePay', '2025-07-02 11:27:23', 'berhasil'),
(2, 2, '50000.00', 'ShopeePay', '2025-07-02 11:29:57', 'berhasil'),
(3, 2, '60000.00', 'ShopeePay', '2025-07-02 11:30:17', 'berhasil'),
(4, 2, '70000.00', 'Transfer Bank', '2025-07-02 11:30:30', 'berhasil'),
(5, 4, '50000.00', 'GoPay', '2025-07-02 11:32:30', 'berhasil'),
(6, 2, '700000.00', 'ShopeePay', '2025-07-02 14:14:08', 'berhasil'),
(7, 4, '60000.00', 'Transfer Bank', '2025-07-07 13:40:08', 'berhasil'),
(8, 4, '80000.00', 'ShopeePay', '2025-07-07 13:40:21', 'berhasil'),
(9, 4, '10000.00', 'Transfer Bank', '2025-07-07 13:40:29', 'berhasil'),
(10, 4, '50000.00', 'Transfer Bank', '2025-07-08 13:20:52', 'berhasil'),
(11, 4, '600000.00', 'Transfer Bank', '2025-07-10 08:53:44', 'berhasil'),
(12, 2, '5000000.00', 'Transfer Bank', '2025-07-12 21:23:05', 'berhasil');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_name`, `setting_value`, `description`) VALUES
(1, 'app_name', 'Sewaken', 'Nama aplikasi'),
(2, 'email_support', 'support@sewakenn.com', 'Email dukungan pelanggan'),
(3, 'phone_support', '+6281234567890', 'Nomor telepon dukungan pelanggan'),
(4, 'address_support', 'Jl. Contoh No. 123, Kota Anda', 'Alamat kantor/dukungan'),
(5, 'currency_symbol', 'Rp', 'Simbol mata uang'),
(6, 'rental_agreement_terms', 'Syarat dan ketentuan penyewaan standar...', 'Ketentuan perjanjian sewa');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `lama_sewa` int(11) NOT NULL,
  `total_biaya` decimal(15,2) NOT NULL,
  `alamat_pengiriman` varchar(255) NOT NULL,
  `metode_pengiriman` varchar(50) DEFAULT NULL,
  `metode_pembayaran` varchar(100) NOT NULL,
  `bukti_pembayaran` varchar(255) NOT NULL,
  `status_transaksi` enum('pending_pembayaran','menunggu_verifikasi_admin','barang_dikemas','konfirmasi_pembayaran','pembayaran_berhasil','dikirim','dikirim_kembali','masa_sewa','selesai','dibatalkan','menunggu_pengembalian','telat_pengembalian','denda_dibayar') NOT NULL DEFAULT 'pending_pembayaran',
  `catatan` varchar(255) DEFAULT NULL,
  `tanggal_pesan` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `id_produk`, `tanggal_mulai`, `lama_sewa`, `total_biaya`, `alamat_pengiriman`, `metode_pengiriman`, `metode_pembayaran`, `bukti_pembayaran`, `status_transaksi`, `catatan`, `tanggal_pesan`) VALUES
(6, 2, 2, '2025-07-02', 1, '200000.00', 'jalan rudska', NULL, 'ShopeePay', '', 'selesai', NULL, '2025-07-02 16:32:18'),
(8, 2, 7, '2025-07-02', 2, '80000.00', 'jalan rudska', NULL, 'Saldo', 'assets/proofs/bukti_686552f9c0e32WhatsApp Image 2025-07-01 at 22.26.21_7281f3cd.jpg', 'selesai', NULL, '2025-07-02 17:35:56'),
(9, 2, 8, '2025-07-03', 2, '100000.00', 'askasjaksa', NULL, 'Saldo', 'assets/proofs/bukti_68656636dfb65images.jpeg', 'selesai', NULL, '2025-07-02 19:02:26'),
(10, 2, 8, '2025-07-03', 2, '100000.00', 'odowdjowd', NULL, 'ShopeePay', 'assets/proofs/bukti_68656ee03e0e7.jpg', 'selesai', NULL, '2025-07-02 19:29:22'),
(11, 2, 7, '2025-07-03', 2, '80000.00', 'askasjaksa', NULL, 'Saldo', 'assets/proofs/bukti_6865717db0484.jpeg', 'selesai', NULL, '2025-07-02 19:49:51'),
(12, 2, 6, '2025-07-03', 3, '60000.00', 'alsmasaks', NULL, 'Saldo', '', 'selesai', NULL, '2025-07-02 19:50:35'),
(14, 2, 8, '2025-07-03', 2, '100000.00', 'jalan rudskaas', NULL, 'Transfer Bank', '', 'dikirim', NULL, '2025-07-03 02:40:18'),
(15, 2, 7, '2025-07-03', 2, '80000.00', 'jalan rudska', NULL, 'Saldo', 'assets/proofs/bukti_6865d2298b8c6.jpg', 'selesai', NULL, '2025-07-03 02:40:37'),
(16, 2, 7, '2025-07-03', 2, '80000.00', 'jalan rudska', NULL, 'Saldo', '', 'dikirim', NULL, '2025-07-03 02:41:36'),
(17, 2, 6, '2025-07-03', 3, '60000.00', 'alsmasaks', NULL, 'Saldo', 'assets/proofs/bukti_6865d70d83c34.jpg', 'menunggu_verifikasi_admin', NULL, '2025-07-03 03:03:05'),
(18, 2, 7, '2025-07-07', 2, '80000.00', 'odowdjowd', NULL, 'Saldo', '', 'selesai', NULL, '2025-07-07 08:25:45'),
(19, 4, 8, '2025-07-07', 2, '100000.00', 'odowdjowd', NULL, 'Saldo', '', 'dikirim', NULL, '2025-07-07 08:40:51'),
(20, 2, 9, '2025-07-08', 4, '120000.00', 'jawa bagian selatan', NULL, 'Saldo', '', '', NULL, '2025-07-08 09:59:26'),
(21, 2, 9, '2025-07-08', 4, '120000.00', 'jawa bagian selatan', NULL, '', '', 'pending_pembayaran', NULL, '2025-07-08 17:14:20'),
(22, 2, 8, '2025-07-08', 2, '100000.00', 'jawa bagian selatan', NULL, '', '', 'pending_pembayaran', NULL, '2025-07-08 17:14:54'),
(23, 2, 9, '2025-07-08', 4, '120000.00', 'jawa bagian selatan', NULL, '', '', 'pending_pembayaran', NULL, '2025-07-08 17:16:16'),
(24, 2, 8, '2025-07-08', 3, '170000.00', 'jawa bagian selatan', NULL, 'Jemput Kurir', '', '', 'Kurir: Gojek Express', '2025-07-08 17:27:48'),
(25, 4, 15, '2025-07-10', 5, '450000.00', 'jawa', NULL, 'COD', '', '', '', '2025-07-10 03:53:52'),
(26, 4, 12, '2025-07-11', 5, '125000.00', 'jawa', NULL, 'COD', '', 'selesai', '', '2025-07-11 11:57:47'),
(27, 2, 14, '2025-07-12', 2, '100000.00', '', NULL, 'COD', '', '', 'alamskasaksj', '2025-07-12 16:14:16'),
(28, 2, 15, '2025-07-12', 3, '270000.00', '', NULL, 'COD', '', '', '', '2025-07-12 16:18:50'),
(29, 2, 14, '2025-07-12', 1, '75000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', NULL, 'Jemput Kurir', '', 'selesai', 'cepet | Kurir: J&T Express', '2025-07-12 16:23:40'),
(30, 2, 13, '2025-07-12', 1, '45000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', '', '', '', '', '2025-07-12 17:41:38'),
(31, 2, 6, '2025-07-12', 1, '40000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', '', '', '', 'Kurir: Gojek Express', '2025-07-12 18:00:30'),
(32, 2, 6, '2025-07-12', 1, '45000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', '', 'Kurir: J&T Express', '2025-07-12 18:02:20'),
(33, 2, 13, '2025-07-14', 1, '45000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 18:05:31'),
(34, 2, 6, '2025-07-12', 1, '20000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 18:47:22'),
(35, 2, 13, '2025-07-12', 1, '45000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', 'dibatalkan', '', '2025-07-12 18:50:23'),
(36, 2, 6, '2025-07-12', 1, '45000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-12 18:57:02'),
(37, 2, 10, '2025-07-12', 1, '30000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 18:57:42'),
(38, 2, 6, '2025-07-13', 1, '20000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 19:07:56'),
(39, 2, 14, '2025-07-13', 1, '50000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 19:11:41'),
(40, 2, 15, '2025-07-13', 1, '90000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 19:16:15'),
(41, 2, 11, '2025-07-13', 1, '45000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', 'dikirim', '', '2025-07-12 19:33:53'),
(42, 2, 15, '2025-07-13', 1, '115000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: J&T Express', '2025-07-12 19:56:12'),
(43, 2, 11, '2025-07-13', 1, '70000.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: J&T Express', '2025-07-13 12:01:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `saldo` decimal(15,2) NOT NULL DEFAULT '0.00',
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `nama_lengkap`, `email`, `no_hp`, `alamat`, `foto_profil`, `saldo`, `phone_number`, `address`, `profile_picture`, `created_at`, `password`, `role`) VALUES
(1, 'admin', NULL, NULL, NULL, NULL, NULL, '150000.00', NULL, NULL, NULL, '2025-06-29 21:37:43', '0192023a7bbd73250516f069df18b500', 'admin'),
(2, 'user1', 'user_Ganteng', 'john@12345', '081245268145', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'assets/images/profile_pictures/profile_6864b414685db.jpeg', '4193000.00', NULL, NULL, NULL, '2025-06-29 21:37:43', '6ad14ba9986e3615423dfca256d04e3f', 'user'),
(3, 'admin1', 'ADmin Ganteng', 'adm1@gmail.com', '0991219912912912', 'Jateng', 'assets/images/profile_pictures/profile_686d26149e4cd.jpeg', '185000.00', NULL, NULL, NULL, '2025-06-29 21:37:43', '0192023a7bbd73250516f069df18b500', 'admin'),
(4, 'user2', 'userrsewaken', 'user2@gmail.com', '029320930293', 'jawa', 'assets/images/profile_pictures/profile_6864a9726af86.jpg', '175000.00', NULL, NULL, NULL, '2025-07-02 10:37:22', '6ad14ba9986e3615423dfca256d04e3f', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kurir`
--
ALTER TABLE `kurir`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lokasi`
--
ALTER TABLE `lokasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `lokasi_id` (`lokasi_id`),
  ADD KEY `fk_produk_admin` (`admin_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `riwayat_saldo`
--
ALTER TABLE `riwayat_saldo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `riwayat_topup`
--
ALTER TABLE `riwayat_topup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kurir`
--
ALTER TABLE `kurir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lokasi`
--
ALTER TABLE `lokasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `riwayat_saldo`
--
ALTER TABLE `riwayat_saldo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `riwayat_topup`
--
ALTER TABLE `riwayat_topup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_produk_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `produk_ibfk_2` FOREIGN KEY (`lokasi_id`) REFERENCES `lokasi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_saldo`
--
ALTER TABLE `riwayat_saldo`
  ADD CONSTRAINT `riwayat_saldo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_topup`
--
ALTER TABLE `riwayat_topup`
  ADD CONSTRAINT `riwayat_topup_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
