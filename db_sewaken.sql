-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2025 at 10:14 AM
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
(24, 2, 12, 1, 1, '2025-07-12 15:21:22', '2025-07-12 15:21:22'),
(26, 5, 32, 1, 1, '2025-07-19 07:32:09', '2025-07-19 07:32:09');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `produk_id`, `id_transaksi`, `user_id`, `sender`, `message`, `created_at`) VALUES
(1, 9, NULL, 2, 'user', 'halo min', '2025-07-09 00:18:02'),
(2, 9, NULL, 2, 'admin', 'haloo juga', '2025-07-09 12:46:34'),
(3, 9, NULL, 3, 'user', 'apakah ready stok?', '2025-07-09 12:54:03'),
(4, 9, NULL, 2, 'admin', 'oi', '2025-07-09 13:02:28'),
(5, 9, NULL, 2, 'admin', 'hai', '2025-07-09 13:11:50'),
(6, 9, NULL, 3, 'user', 'halo', '2025-07-09 13:22:26'),
(7, 9, NULL, 3, 'user', 'admin', '2025-07-09 13:22:34'),
(8, 9, NULL, 2, 'admin', 'apaan', '2025-07-09 13:23:33'),
(9, 9, NULL, 3, 'user', 'halo', '2025-07-09 13:27:22'),
(10, 9, NULL, 3, 'user', 'oi', '2025-07-09 13:27:27'),
(11, 9, NULL, 2, 'admin', 'halo', '2025-07-09 13:27:35'),
(12, 9, NULL, 3, 'user', 'assalamualaikum', '2025-07-09 13:31:44'),
(13, 9, NULL, 3, 'user', 'hei', '2025-07-09 14:06:53'),
(14, 9, NULL, 3, 'admin', 'waalaikumsalam', '2025-07-09 14:07:44'),
(15, 9, NULL, 3, 'admin', 'kenapa', '2025-07-09 14:08:14'),
(16, 9, NULL, 3, 'admin', 'woy', '2025-07-09 14:08:57'),
(17, 9, NULL, 3, 'user', 'min', '2025-07-09 14:18:29'),
(18, 9, NULL, 3, 'admin', 'gimana bos', '2025-07-09 14:18:45'),
(19, 7, NULL, 4, 'user', 'masih min?', '2025-07-09 20:29:58'),
(20, 7, NULL, 4, 'admin', 'masih om', '2025-07-09 20:30:34'),
(21, 8, NULL, 4, 'user', 'masih om?', '2025-07-09 20:31:10'),
(22, 8, NULL, 4, 'admin', 'hooh', '2025-07-09 20:31:50'),
(23, 13, NULL, 4, 'user', 'masih kah min?', '2025-07-10 08:09:09'),
(24, 15, NULL, 4, 'user', 'masih kah', '2025-07-10 08:57:07'),
(25, 7, NULL, 4, 'user', 'ada min?', '2025-07-11 16:21:09'),
(26, 15, NULL, 2, 'user', 'wow beautiful amazingg papa mau 5', '2025-07-11 17:06:27'),
(27, 7, NULL, 4, 'user', 'saya mau 5', '2025-07-11 17:34:48'),
(28, 7, NULL, 4, 'admin', 'jadi ga', '2025-07-12 15:19:36'),
(29, 7, NULL, 4, 'user', 'jadiii', '2025-07-12 15:20:43'),
(30, 7, NULL, 4, 'user', 'COD Kaliurang ya', '2025-07-12 15:24:13'),
(31, 7, NULL, 4, 'admin', 'oke gan', '2025-07-12 15:27:19'),
(32, 9, NULL, 2, 'user', 'ready?', '2025-07-12 16:24:19'),
(33, 13, NULL, 2, 'user', 'halo', '2025-07-12 16:26:30'),
(34, 8, NULL, 2, 'user', 'ini berapa om', '2025-07-12 16:33:19'),
(35, 8, NULL, 2, 'admin', 'buta kau? udah ada harganya liat baek baek', '2025-07-12 16:34:25'),
(36, 8, NULL, 2, 'user', 'galak amat om', '2025-07-12 16:41:25'),
(37, 14, 44, 2, 'user', 'p', '2025-07-14 23:53:18'),
(38, 14, NULL, 2, 'user', 'p', '2025-07-14 23:53:38'),
(39, 14, NULL, 2, 'admin', 'pie', '2025-07-14 23:54:20'),
(40, 11, 43, 2, 'user', 'min', '2025-07-14 23:55:25'),
(41, 15, 42, 2, 'user', 'p', '2025-07-15 11:20:12'),
(42, 15, NULL, 2, 'admin', 'ada', '2025-07-18 20:48:20'),
(43, 18, NULL, 4, 'user', 'ready min?', '2025-07-18 23:16:00'),
(44, 12, NULL, 5, 'user', 'lagi sampe mana ya min', '2025-07-19 12:07:45'),
(45, 12, NULL, 5, 'admin', 'sampe mekkah bos\r\n', '2025-07-19 14:47:37');

-- --------------------------------------------------------

--
-- Table structure for table `claimed_promos`
--

CREATE TABLE `claimed_promos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `promo_id` int(11) NOT NULL,
  `claimed_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `claimed_promos`
--

INSERT INTO `claimed_promos` (`id`, `user_id`, `promo_id`, `claimed_at`) VALUES
(1, 5, 8, '2025-07-16 21:53:41'),
(2, 5, 1, '2025-07-16 22:04:18'),
(3, 2, 4, '2025-07-18 14:34:25'),
(4, 4, 2, '2025-07-18 22:02:33'),
(5, 2, 8, '2025-07-19 01:55:55'),
(6, 5, 6, '2025-07-19 12:01:31');

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
(6, 'Kota baru'),
(7, 'Condong Catur');

-- --------------------------------------------------------

--
-- Table structure for table `penarikan_admin`
--

CREATE TABLE `penarikan_admin` (
  `id` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `penarikan_admin`
--

INSERT INTO `penarikan_admin` (`id`, `nominal`, `tanggal`, `admin_id`, `keterangan`) VALUES
(1, 20000, '2025-07-18 17:39:10', 3, 'wewee'),
(2, 20000, '2025-07-18 17:42:14', 3, 'wewee'),
(3, 50000, '2025-07-18 17:42:29', 3, '4t4t'),
(4, 50000, '2025-07-18 17:42:53', 3, '4t4t'),
(5, 50000, '2025-07-18 17:45:12', 3, '4t4t'),
(6, 20000, '2025-07-18 17:45:21', 3, '22d');

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
(2, 'Drone DJI Mini 3', 1, 'assets/images/th (3).jpeg', 'Drone ringan dengan kamera 4K dan waktu terbang lama.', '200000.00', 4, 'hari', 1, 1, '2025-06-29 21:30:46', '2025-07-02 21:32:18', 2, 3),
(6, 'Kamera Nikon', 2, 'assets/images/images.jpeg', 'kamera Murah aja', '20000.00', 3, 'hari', 3, 1, '2025-07-02 20:48:23', '2025-07-19 12:36:59', 5, 3),
(7, 'Playstation', 1, 'assets/images/playstation-5.jpg', 'Ps murah 2 hari diskom', '40000.00', 3, 'hari', 2, 1, '2025-07-02 22:17:35', '2025-07-07 13:25:45', 4, 3),
(8, 'Laptop', 1, 'assets/images/6449520_rd.avif', 'Laptop gaming mantab', '50000.00', 2, 'hari', 2, 1, '2025-07-02 22:18:09', '2025-07-08 22:27:48', 4, 3),
(9, 'Celana Outdoor Eiger', 3, 'assets/images/Celana outdoor.jpeg', 'Celana cocok untuk hiking dan aktivitas outdoor\r\nsize L-XXL', '30000.00', 3, 'hari', 4, 1, '2025-07-08 12:34:56', '2025-07-16 21:44:07', 3, 3),
(10, 'Kamera Leica', 2, 'assets/images/DSLR-LEICA-260x200.jpg', 'Kamera leica\r\ntipe AXX\r\nTahun 2020', '30000.00', 5, 'hari', 3, 1, '2025-07-09 20:40:01', '2025-07-19 00:26:13', 5, 3),
(11, 'Kamera Nikon AA', 2, 'assets/images/Kameranikon.png', 'Kamera Nikon\r\ntipe AA New arrival\r\nTahun 2020 4k', '45000.00', 4, 'hari', 2, 1, '2025-07-09 20:41:01', '2025-07-19 02:08:39', 6, 3),
(12, 'Kamera Samsung', 2, 'assets/images/Kamera samsung.jpeg', 'Kamera samsung\r\ntipe AXX\r\nMade in Korea', '25000.00', 2, 'hari', 5, 1, '2025-07-09 20:41:54', '2025-07-19 12:01:45', 5, 3),
(13, 'Kamera Lumix', 2, 'assets/images/Kamera lumix.jpeg', 'Lumix the best camera 2026\r\ncocok memfoto alien\r\n16k jernih ', '45000.00', 4, 'hari', 4, 1, '2025-07-09 21:32:44', '2025-07-19 15:07:39', 6, 3),
(14, 'Kamera sony', 2, 'assets/images/Kamera sony.jpg', 'alamakk harga termurah se-sleman woyy buruan', '50000.00', 3, 'hari', 4, 1, '2025-07-09 22:52:52', '2025-07-19 00:41:23', 5, 3),
(15, 'Kamera Nikon Gx7', 2, 'assets/images/Nikon GX 7.jpg', 'gacor ini mahal', '90000.00', 5, 'hari', 5, 1, '2025-07-09 23:15:40', '2025-07-19 15:07:11', 5, 3),
(18, 'Tas Eiger 60L', 3, 'assets/images/tas.jpeg', 'Tas eiger cocok untuk mendaki Everest cocok untuk pemula gas', '15000.00', 4, 'hari', 7, 1, '2025-07-18 18:00:38', '2025-07-19 10:54:00', 1, 3),
(19, 'Laptop Dell Latitude 7470', 1, 'assets/images/Lenovo.jpeg', 'Laptop profesional, RAMâ€¯8GB, SSDâ€¯256GB; ideal untuk kerja proyek/event; penyewaan per hari/minggu', '30000.00', 5, 'hari', 7, 1, '2025-07-19 12:40:33', '2025-07-19 14:21:52', 3, 3),
(20, 'Monis â€“ Projector 4K', 1, 'assets/images/Proyektor.jpeg', 'Proyektor 4K, cocok untuk presentasi/event; sewa per minggu', '25000.00', 6, 'hari', 5, 1, '2025-07-19 12:42:10', '2025-07-19 14:22:08', 1, 3),
(21, 'Mesin Kopi Nespresso', 1, 'assets/images/OIP (11).jpeg', 'Praktis, sewa per minggu; cocok event ', '50000.00', 4, 'hari', 7, 1, '2025-07-19 12:43:34', '2025-07-19 13:54:31', 1, 3),
(22, 'Lenovo ThinkPad X260', 1, 'assets/images/Thinkpad.jpeg', 'Kuat, ringkas, penyewaan fleksibel; cocok untuk kebutuhan IT temporer', '35000.00', 3, 'hari', 5, 1, '2025-07-19 12:45:12', '2025-07-19 14:22:32', 3, 3),
(23, 'Mesin Kopi Dolce Gusto', 1, 'assets/images/OIP (5).jpeg', 'Compact, sewa mingguan; cocok coffee corner sederhana', '40000.00', 3, 'hari', 6, 1, '2025-07-19 12:47:09', '2025-07-19 12:47:09', 7, 3),
(24, 'Tas ransel daypack', 3, 'assets/images/OIP.jpeg', 'Daypack Payoda ringan 20â€‘30â€¯L untuk hiking ringan; ideal disewa per trip dan tektok santuy', '12000.00', 5, 'hari', 8, 1, '2025-07-19 12:50:57', '2025-07-19 12:50:57', 5, 3),
(25, 'Nilon powerbank 10000â€¯mAh', 1, 'assets/images/OIP (1).jpeg', 'Nilon powerbank 10000â€¯mAh Powerbank handal untuk device mobile selama event/outdoor', '20000.00', 5, 'hari', 17, 1, '2025-07-19 12:59:28', '2025-07-19 12:59:28', 1, 3),
(26, 'Headlamp LED tahan air', 3, 'assets/images/OIP (2).jpeg', 'Headlamp tahan air, ideal malam hari hiking/camping', '13000.00', 6, 'hari', 12, 1, '2025-07-19 13:01:55', '2025-07-19 13:01:55', 2, 3),
(27, 'Trekking poles (sepasang)', 3, 'assets/images/OIP (3).jpeg', 'Penopang kaki Eiger, cocok untuk trekking kasar; disewa per trip', '15000.00', 12, 'hari', 14, 1, '2025-07-19 13:04:30', '2025-07-19 13:04:30', 4, 3),
(28, 'Matras yoga/Camp', 3, 'assets/images/Matyog.jpeg', 'Matras serbaguna untuk olahraga, camping, atau event', '10000.00', 15, 'hari', 14, 1, '2025-07-19 13:32:00', '2025-07-19 14:23:57', 5, 3),
(29, 'Speaker portabel mini', 1, 'assets/images/OIP (6).jpeg', 'Speaker portabel mini Speaker kecil Bluetooth, cocok diaksesori event kecil', '12000.00', 19, 'hari', 7, 1, '2025-07-19 13:35:37', '2025-07-19 13:35:37', 4, 3),
(30, 'Meja lipat aluminium', 6, 'assets/images/id-11134207-7r98u-m09o7et14gvyf5.jpeg', 'Meja lipat aluminium', '20000.00', 3, 'hari', 14, 1, '2025-07-19 13:41:46', '2025-07-19 13:41:46', 7, 3),
(31, 'Naturehike Ultralight Camp', 7, 'assets/images/OIP (7).jpeg', 'Tenda ultralight, waterproof, cocok solo atau couple camping.', '20000.00', 10, 'hari', 7, 1, '2025-07-19 13:44:30', '2025-07-19 13:44:30', 3, 3),
(32, 'Tenda Camping 5â€‘8â€¯Orang', 7, 'assets/images/OIP (8).jpeg', 'Tenda family, waterproof; cocok camping grup kecil.', '20000.00', 20, 'hari', 7, 1, '2025-07-19 13:45:38', '2025-07-19 13:45:38', 3, 3),
(33, 'Sleeping bag â€“ winter grade (-5Â°C)', 7, 'assets/images/OIP (9).jpeg', 'Nyaman untuk malam dingin cocok disewa per trip', '12000.00', 22, 'hari', 7, 1, '2025-07-19 13:49:29', '2025-07-19 14:30:26', 7, 3),
(34, 'Kompor camping portable', 7, 'assets/images/OIP (10).jpeg', 'Kompor gas mini; cocok memasak saat camping', '5000.00', 30, 'hari', 14, 1, '2025-07-19 13:53:32', '2025-07-19 13:53:32', 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `promos`
--

CREATE TABLE `promos` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text,
  `tipe` enum('percentage','fixed','free_shipping') NOT NULL,
  `nilai` decimal(10,2) NOT NULL,
  `kode_promo` varchar(50) NOT NULL,
  `min_transaksi` decimal(15,2) DEFAULT '0.00',
  `max_diskon` decimal(15,2) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_berakhir` date NOT NULL,
  `limit_penggunaan` int(11) DEFAULT NULL,
  `penggunaan_sekarang` int(11) DEFAULT '0',
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `promos`
--

INSERT INTO `promos` (`id`, `nama`, `deskripsi`, `tipe`, `nilai`, `kode_promo`, `min_transaksi`, `max_diskon`, `kategori_id`, `produk_id`, `tanggal_mulai`, `tanggal_berakhir`, `limit_penggunaan`, `penggunaan_sekarang`, `gambar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Diskon Akhir Tahun 10%', 'Dapatkan diskon 10% untuk semua produk!', 'percentage', '10.00', 'AKHIRTAHUN10', '100000.00', '50000.00', NULL, NULL, '2025-07-01', '2025-07-31', 100, 1, NULL, 'aktif', '2025-07-16 21:50:21', '2025-07-16 22:31:46'),
(2, 'Diskon Spesial 20%', 'Diskon spesial 20% untuk pelanggan baru.', 'percentage', '20.00', 'BARU20', '150000.00', '60000.00', NULL, NULL, '2025-07-01', '2025-07-31', 50, 0, NULL, 'aktif', '2025-07-16 21:50:21', '2025-07-16 21:50:21'),
(3, 'Diskon Member 15%', 'Khusus member, diskon 15%!', 'percentage', '15.00', 'MEMBER15', '200000.00', '75000.00', NULL, NULL, '2025-07-01', '2025-07-31', 30, 0, NULL, 'aktif', '2025-07-16 21:50:21', '2025-07-16 21:50:21'),
(4, 'Potongan 25 Ribu', 'Langsung potong Rp 25.000 untuk transaksi minimal 200 ribu.', 'fixed', '25000.00', 'POTONG25K', '200000.00', NULL, NULL, NULL, '2025-07-01', '2025-07-31', 100, 1, NULL, 'aktif', '2025-07-16 21:50:21', '2025-07-19 02:01:50'),
(5, 'Potongan 50 Ribu', 'Potongan Rp 50.000 untuk transaksi di atas 400 ribu.', 'fixed', '50000.00', 'POTONG50K', '400000.00', NULL, NULL, NULL, '2025-07-01', '2025-07-31', 50, 0, NULL, 'aktif', '2025-07-16 21:50:21', '2025-07-16 21:50:21'),
(6, 'Potongan 10 Ribu', 'Potongan langsung Rp 10.000 tanpa syarat!', 'fixed', '10000.00', 'POTONG10K', '0.00', NULL, NULL, NULL, '2025-07-01', '2025-07-31', 200, 1, NULL, 'aktif', '2025-07-16 21:50:21', '2025-07-19 12:03:12'),
(7, 'Gratis Ongkir Jawa', 'Gratis ongkir untuk pengiriman wilayah Jawa.', 'free_shipping', '0.00', 'ONGKIRJAWA', '100000.00', NULL, NULL, NULL, '2025-07-01', '2025-07-31', 100, 0, NULL, 'aktif', '2025-07-16 21:50:21', '2025-07-16 21:50:21'),
(8, 'Gratis Ongkir Semua', 'Gratis ongkir tanpa minimal belanja!', 'free_shipping', '0.00', 'ONGKIRSEMUA', '0.00', NULL, NULL, NULL, '2025-07-01', '2025-07-31', 50, 2, NULL, 'aktif', '2025-07-16 21:50:21', '2025-07-19 15:07:39'),
(9, 'Gratis Ongkir 50K', 'Gratis ongkir hingga Rp 50.000 untuk transaksi di atas 300 ribu.', 'free_shipping', '0.00', 'ONGKIR50K', '300000.00', NULL, NULL, NULL, '2025-07-01', '2025-07-31', 30, 0, NULL, 'aktif', '2025-07-16 21:50:21', '2025-07-16 21:50:21');

-- --------------------------------------------------------

--
-- Table structure for table `promo_usage`
--

CREATE TABLE `promo_usage` (
  `id` int(11) NOT NULL,
  `promo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `diskon_didapat` decimal(15,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `promo_usage`
--

INSERT INTO `promo_usage` (`id`, `promo_id`, `user_id`, `transaksi_id`, `diskon_didapat`, `created_at`) VALUES
(1, 1, 5, 50, '15000.00', '2025-07-16 22:31:46'),
(0, 8, 2, 79, '0.00', '2025-07-19 01:56:24'),
(0, 4, 2, 80, '25000.00', '2025-07-19 02:01:50'),
(0, 6, 5, 87, '10000.00', '2025-07-19 12:03:12'),
(0, 8, 5, 91, '0.00', '2025-07-19 15:07:39');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text,
  `review_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `id_transaksi`, `product_id`, `user_id`, `rating`, `comment`, `review_date`) VALUES
(1, 0, 15, 2, 4, 'baguslah lumayan tapi buttonnya jepretnya agak kureng', '2025-07-13 16:54:19'),
(2, 0, 14, 2, 5, 'joss bisa memfoto alien cuy', '2025-07-13 17:00:43'),
(3, 0, 11, 2, 5, 'bagus kamera profesional josjis', '2025-07-13 17:06:57'),
(4, 42, 15, 2, 5, 'kedua kalinya menyewa disini manteb', '2025-07-14 23:17:49'),
(5, 44, 14, 2, 4, 'manteb josjis bolo', '2025-07-14 23:18:28'),
(6, 46, 11, 2, 4, 'bagus', '2025-07-15 12:12:27'),
(7, 47, 10, 2, 5, 'josjis', '2025-07-15 12:12:41'),
(8, 65, 18, 4, 5, 'bagus', '2025-07-18 22:50:11'),
(9, 62, 18, 4, 4, 'cocok untuk mendaki', '2025-07-18 23:04:48'),
(10, 60, 11, 4, 5, 'WORTH IT PARAH', '2025-07-18 23:11:01'),
(11, 89, 33, 5, 5, 'lumayan bagus dan tebal harga affordable', '2025-07-19 14:31:29'),
(12, 88, 6, 5, 5, 'Joss mantab', '2025-07-19 14:50:00'),
(13, 87, 6, 5, 5, 'bagus dapet foto alien', '2025-07-19 14:50:15');

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
(41, 3, 'sewa', '70000.00', 'Penerimaan sewa dari user ID 2', '2025-07-13 17:05:53', '185000.00'),
(42, 2, 'sewa', '50000.00', 'Penyewaan produk ID 14', '2025-07-14 09:44:25', '4143000.00'),
(43, 3, 'sewa', '50000.00', 'Penerimaan sewa dari user ID 2', '2025-07-14 23:04:58', '235000.00'),
(44, 2, 'sewa', '112000.00', 'Penyewaan produk ID 15', '2025-07-15 00:33:43', '4011000.00'),
(45, 2, 'sewa', '70000.00', 'Penyewaan produk ID 11', '2025-07-15 11:38:09', '3941000.00'),
(46, 2, 'sewa', '50000.00', 'Penyewaan produk ID 10', '2025-07-15 11:40:55', '3891000.00'),
(47, 3, 'sewa', '50000.00', 'Penerimaan sewa dari user ID 2', '2025-07-15 11:54:23', '285000.00'),
(48, 3, 'sewa', '70000.00', 'Penerimaan sewa dari user ID 2', '2025-07-15 11:54:25', '355000.00'),
(49, 2, 'sewa', '115000.00', 'Penyewaan produk ID 15', '2025-07-15 12:15:51', '3731000.00'),
(50, 2, 'refund', '115000.00', 'Refund pembatalan transaksi ID 48', '2025-07-15 12:16:30', '3846000.00'),
(51, 2, 'sewa', '55000.00', 'Penyewaan produk ID 9', '2025-07-15 14:49:08', '3791000.00'),
(52, 2, 'topup', '20000.00', 'Top up via Transfer Bank', '2025-07-16 21:10:41', '3811000.00'),
(53, 2, 'sewa', '70000.00', 'Penyewaan produk ID 11', '2025-07-16 21:23:58', '3741000.00'),
(54, 4, 'sewa', '45000.00', 'Penyewaan produk ID 12', '2025-07-16 21:34:31', '130000.00'),
(55, 4, 'denda', '3750.00', 'Pembayaran denda keterlambatan transaksi ID 51', '2025-07-16 21:43:22', '126250.00'),
(56, 3, 'denda', '3750.00', 'Penerimaan denda keterlambatan dari user ID 4 transaksi ID 51', '2025-07-16 21:43:22', '358750.00'),
(57, 3, 'sewa', '55000.00', 'Penerimaan sewa dari user ID 2', '2025-07-16 21:44:07', '413750.00'),
(58, 2, 'refund', '70000.00', 'Refund pembatalan transaksi ID 50', '2025-07-18 14:36:01', '3811000.00'),
(59, 2, 'sewa', '70000.00', 'Penyewaan produk ID 11', '2025-07-18 14:37:22', '3741000.00'),
(60, 2, 'refund', '70000.00', 'Refund pembatalan transaksi ID 52', '2025-07-18 14:38:08', '3811000.00'),
(61, 2, 'sewa', '70000.00', 'Penyewaan produk ID 11', '2025-07-18 14:50:04', '3741000.00'),
(62, 2, 'refund', '70000.00', 'Refund pembatalan transaksi ID 53', '2025-07-18 14:52:21', '3811000.00'),
(63, 2, 'sewa', '70000.00', 'Penyewaan produk ID 11', '2025-07-18 14:52:39', '3741000.00'),
(64, 2, 'refund', '70000.00', 'Refund pembatalan transaksi ID 54', '2025-07-18 14:52:56', '3811000.00'),
(65, 2, 'sewa', '110000.00', 'Penyewaan produk ID 11', '2025-07-18 14:57:34', '3701000.00'),
(66, 2, 'refund', '110000.00', 'Refund pembatalan transaksi ID 55', '2025-07-18 15:07:47', '3811000.00'),
(67, 2, 'sewa', '115000.00', 'Penyewaan produk ID 11', '2025-07-18 15:15:12', '3696000.00'),
(68, 2, 'refund', '115000.00', 'Refund pembatalan transaksi ID 56', '2025-07-18 15:17:34', '3811000.00'),
(69, 2, 'sewa', '115000.00', 'Penyewaan produk ID 11', '2025-07-18 15:35:44', '3696000.00'),
(70, 2, 'refund', '115000.00', 'Refund pembatalan transaksi ID 57', '2025-07-18 15:40:08', '3811000.00'),
(71, 2, 'sewa', '115000.00', 'Penyewaan produk ID 11', '2025-07-18 15:40:39', '3696000.00'),
(72, 2, 'refund', '115000.00', 'Refund pembatalan transaksi ID 58', '2025-07-18 15:40:55', '3811000.00'),
(73, 2, 'sewa', '160000.00', 'Penyewaan produk ID 11', '2025-07-18 15:44:04', '3651000.00'),
(74, 2, 'refund', '160000.00', 'Refund pembatalan transaksi ID 59', '2025-07-18 15:44:20', '3811000.00'),
(75, 4, 'sewa', '110000.00', 'Penyewaan produk ID 11', '2025-07-18 17:16:09', '16250.00'),
(76, 4, 'topup', '300000.00', 'Top up via ShopeePay', '2025-07-18 17:17:39', '316250.00'),
(77, 3, 'sewa', '110000.00', 'Penerimaan sewa dari user ID 4', '2025-07-18 17:18:22', '523750.00'),
(78, 4, 'sewa', '55000.00', 'Penyewaan produk ID 18', '2025-07-18 18:03:21', '241250.00'),
(79, 4, 'denda', '2250.00', 'Pembayaran denda keterlambatan transaksi ID 61', '2025-07-18 18:08:14', '239000.00'),
(80, 3, 'denda', '2250.00', 'Penerimaan denda keterlambatan dari user ID 4 transaksi ID 61', '2025-07-18 18:08:14', '296000.00'),
(81, 4, 'sewa', '35000.00', 'Penyewaan produk ID 18', '2025-07-18 18:16:28', '204000.00'),
(82, 4, 'sewa', '15000.00', 'Penyewaan produk ID 18', '2025-07-18 18:26:09', '189000.00'),
(83, 4, 'denda', '2250.00', 'Pembayaran denda keterlambatan transaksi ID 63', '2025-07-18 18:28:30', '186750.00'),
(84, 3, 'denda', '2250.00', 'Penerimaan denda keterlambatan dari user ID 4 transaksi ID 63', '2025-07-18 18:28:30', '298250.00'),
(85, 4, 'sewa', '15000.00', 'Penyewaan produk ID 18', '2025-07-18 18:42:29', '171750.00'),
(86, 4, 'denda', '4500.00', 'Pembayaran denda keterlambatan transaksi ID 64', '2025-07-18 19:28:58', '167250.00'),
(87, 3, 'denda', '4500.00', 'Penerimaan denda keterlambatan dari user ID 4 transaksi ID 64', '2025-07-18 19:28:58', '302750.00'),
(88, 4, 'sewa', '40000.00', 'Penyewaan produk ID 18', '2025-07-18 19:43:06', '127250.00'),
(89, 3, 'sewa', '40000.00', 'Penerimaan sewa dari user ID 4', '2025-07-18 19:46:53', '342750.00'),
(90, 2, 'sewa', '55000.00', 'Penyewaan produk ID 18', '2025-07-18 19:47:38', '3756000.00'),
(91, 3, 'sewa', '55000.00', 'Penerimaan sewa dari user ID 2', '2025-07-18 19:55:33', '397750.00'),
(92, 3, 'sewa', '35000.00', 'Penerimaan sewa dari user ID 4', '2025-07-18 19:55:37', '432750.00'),
(93, 4, 'sewa', '55000.00', 'Penyewaan produk ID 18', '2025-07-18 22:25:05', '67750.00'),
(94, 4, 'topup', '2000000.00', 'Top up via ShopeePay', '2025-07-18 22:25:35', '2067750.00'),
(95, 4, 'sewa', '80000.00', 'Penyewaan produk ID 14', '2025-07-18 22:26:20', '1987750.00'),
(96, 4, 'sewa', '70000.00', 'Penyewaan produk ID 11', '2025-07-18 22:30:44', '1917750.00'),
(97, 4, 'refund', '70000.00', 'Refund pembatalan transaksi ID 69', '2025-07-18 22:35:41', '1987750.00'),
(98, 4, 'refund', '80000.00', 'Refund pembatalan transaksi ID 68', '2025-07-18 22:35:47', '2067750.00'),
(99, 4, 'refund', '55000.00', 'Refund pembatalan transaksi ID 67', '2025-07-18 22:35:55', '2122750.00'),
(100, 4, 'sewa', '55000.00', 'Penyewaan produk ID 10', '2025-07-19 00:12:22', '2067750.00'),
(101, 3, 'sewa', '55000.00', 'Penerimaan sewa dari user ID 4', '2025-07-19 00:26:13', '487750.00'),
(102, 4, 'sewa', '75000.00', 'Penyewaan produk ID 14', '2025-07-19 00:29:46', '1983750.00'),
(103, 4, 'sewa', '40000.00', 'Penyewaan produk ID 18', '2025-07-19 00:38:19', '1943750.00'),
(104, 4, 'sewa', '65000.00', 'Penyewaan produk ID 11', '2025-07-19 00:38:48', '1878750.00'),
(105, 4, 'refund', '65000.00', 'Refund pembatalan transaksi ID 73', '2025-07-19 00:41:09', '1943750.00'),
(106, 4, 'refund', '40000.00', 'Refund pembatalan transaksi ID 72', '2025-07-19 00:41:15', '1983750.00'),
(107, 4, 'refund', '75000.00', 'Refund pembatalan transaksi ID 71', '2025-07-19 00:41:23', '2058750.00'),
(108, 4, 'sewa', '37000.00', 'Penyewaan produk ID 18', '2025-07-19 00:44:03', '2021750.00'),
(109, 2, 'sewa', '45000.00', 'Penyewaan produk ID 18', '2025-07-19 00:44:51', '3686000.00'),
(110, 2, 'sewa', '40000.00', 'Penyewaan produk ID 18', '2025-07-19 00:47:32', '3646000.00'),
(111, 2, 'sewa', '70000.00', 'Penyewaan produk ID 11', '2025-07-19 00:48:34', '3576000.00'),
(112, 2, 'sewa', '50000.00', 'Penyewaan produk ID 10', '2025-07-19 01:53:47', '3526000.00'),
(113, 2, 'sewa', '65000.00', 'Penyewaan produk ID 11', '2025-07-19 01:56:24', '3461000.00'),
(114, 2, 'sewa', '455000.00', 'Penyewaan produk ID 15', '2025-07-19 02:01:50', '3006000.00'),
(115, 2, 'refund', '70000.00', 'Refund pembatalan transaksi ID 77', '2025-07-19 02:04:08', '3076000.00'),
(116, 2, 'refund', '40000.00', 'Refund pembatalan transaksi ID 76', '2025-07-19 02:04:19', '3116000.00'),
(117, 2, 'refund', '45000.00', 'Refund pembatalan transaksi ID 75', '2025-07-19 02:04:25', '3161000.00'),
(118, 2, 'sewa', '90000.00', 'Penyewaan produk ID 11', '2025-07-19 02:05:18', '3071000.00'),
(119, 2, 'sewa', '390000.00', 'Penyewaan produk ID 15', '2025-07-19 02:07:32', '2681000.00'),
(120, 2, 'sewa', '70000.00', 'Penyewaan produk ID 11', '2025-07-19 02:08:39', '2611000.00'),
(121, 4, 'sewa', '130000.00', 'Penyewaan produk ID 6', '2025-07-19 11:50:26', '1891750.00'),
(122, 4, 'refund', '130000.00', 'Refund pembatalan transaksi ID 84', '2025-07-19 11:50:43', '2021750.00'),
(123, 5, 'topup', '500000.00', 'Top up via Transfer Bank', '2025-07-19 11:55:07', '500000.00'),
(124, 5, 'sewa', '130000.00', 'Penyewaan produk ID 6', '2025-07-19 11:55:32', '370000.00'),
(125, 5, 'sewa', '155000.00', 'Penyewaan produk ID 12', '2025-07-19 11:57:09', '215000.00'),
(126, 5, 'topup', '500000.00', 'Top up via Transfer Bank', '2025-07-19 11:57:27', '715000.00'),
(127, 5, 'refund', '155000.00', 'Refund pembatalan transaksi ID 86', '2025-07-19 12:01:45', '870000.00'),
(128, 5, 'refund', '130000.00', 'Refund pembatalan transaksi ID 85', '2025-07-19 12:01:51', '1000000.00'),
(129, 5, 'sewa', '140000.00', 'Penyewaan produk ID 6', '2025-07-19 12:03:12', '860000.00'),
(130, 5, 'sewa', '110000.00', 'Penyewaan produk ID 6', '2025-07-19 12:03:45', '750000.00'),
(131, 3, 'sewa', '110000.00', 'Penerimaan sewa dari user ID 5', '2025-07-19 12:32:44', '597750.00'),
(132, 3, 'sewa', '140000.00', 'Penerimaan sewa dari user ID 5', '2025-07-19 12:36:59', '737750.00'),
(133, 5, 'sewa', '78000.00', 'Penyewaan produk ID 33', '2025-07-19 14:25:39', '630000.00'),
(134, 3, 'sewa', '78000.00', 'Penerimaan sewa dari user ID 5', '2025-07-19 14:30:26', '815750.00'),
(135, 5, 'sewa', '112000.00', 'Penyewaan produk ID 15', '2025-07-19 15:07:11', '514400.00'),
(136, 5, 'sewa', '120000.00', 'Penyewaan produk ID 13', '2025-07-19 15:07:39', '394400.00');

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
(12, 2, '5000000.00', 'Transfer Bank', '2025-07-12 21:23:05', 'berhasil'),
(13, 2, '20000.00', 'Transfer Bank', '2025-07-16 21:10:41', 'berhasil'),
(14, 4, '300000.00', 'ShopeePay', '2025-07-18 17:17:39', 'berhasil'),
(15, 4, '2000000.00', 'ShopeePay', '2025-07-18 22:25:35', 'berhasil'),
(16, 5, '500000.00', 'Transfer Bank', '2025-07-19 11:55:07', 'berhasil'),
(17, 5, '500000.00', 'Transfer Bank', '2025-07-19 11:57:27', 'berhasil');

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
  `jumlah` int(11) NOT NULL DEFAULT '1',
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `lama_sewa` int(11) NOT NULL,
  `total_biaya` decimal(15,2) NOT NULL,
  `denda` decimal(15,2) DEFAULT '0.00',
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

INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `id_produk`, `jumlah`, `tanggal_mulai`, `tanggal_selesai`, `lama_sewa`, `total_biaya`, `denda`, `alamat_pengiriman`, `metode_pengiriman`, `metode_pembayaran`, `bukti_pembayaran`, `status_transaksi`, `catatan`, `tanggal_pesan`) VALUES
(6, 2, 2, 1, '2025-07-02', NULL, 1, '200000.00', '0.00', 'jalan rudska', NULL, 'ShopeePay', '', 'selesai', NULL, '2025-07-02 16:32:18'),
(8, 2, 7, 1, '2025-07-02', NULL, 2, '80000.00', '0.00', 'jalan rudska', NULL, 'Saldo', 'assets/proofs/bukti_686552f9c0e32WhatsApp Image 2025-07-01 at 22.26.21_7281f3cd.jpg', 'selesai', NULL, '2025-07-02 17:35:56'),
(9, 2, 8, 1, '2025-07-03', NULL, 2, '100000.00', '0.00', 'askasjaksa', NULL, 'Saldo', 'assets/proofs/bukti_68656636dfb65images.jpeg', 'selesai', NULL, '2025-07-02 19:02:26'),
(10, 2, 8, 1, '2025-07-03', NULL, 2, '100000.00', '0.00', 'odowdjowd', NULL, 'ShopeePay', 'assets/proofs/bukti_68656ee03e0e7.jpg', 'selesai', NULL, '2025-07-02 19:29:22'),
(11, 2, 7, 1, '2025-07-03', NULL, 2, '80000.00', '0.00', 'askasjaksa', NULL, 'Saldo', 'assets/proofs/bukti_6865717db0484.jpeg', 'selesai', NULL, '2025-07-02 19:49:51'),
(12, 2, 6, 1, '2025-07-03', NULL, 3, '60000.00', '0.00', 'alsmasaks', NULL, 'Saldo', '', 'selesai', NULL, '2025-07-02 19:50:35'),
(14, 2, 8, 1, '2025-07-03', NULL, 2, '100000.00', '0.00', 'jalan rudskaas', NULL, 'Transfer Bank', '', 'dikirim', NULL, '2025-07-03 02:40:18'),
(15, 2, 7, 1, '2025-07-03', NULL, 2, '80000.00', '0.00', 'jalan rudska', NULL, 'Saldo', 'assets/proofs/bukti_6865d2298b8c6.jpg', 'selesai', NULL, '2025-07-03 02:40:37'),
(16, 2, 7, 1, '2025-07-03', NULL, 2, '80000.00', '0.00', 'jalan rudska', NULL, 'Saldo', '', 'dikirim', NULL, '2025-07-03 02:41:36'),
(17, 2, 6, 1, '2025-07-03', NULL, 3, '60000.00', '0.00', 'alsmasaks', NULL, 'Saldo', 'assets/proofs/bukti_6865d70d83c34.jpg', 'menunggu_verifikasi_admin', NULL, '2025-07-03 03:03:05'),
(18, 2, 7, 1, '2025-07-07', NULL, 2, '80000.00', '0.00', 'odowdjowd', NULL, 'Saldo', '', 'selesai', NULL, '2025-07-07 08:25:45'),
(19, 4, 8, 1, '2025-07-07', NULL, 2, '100000.00', '0.00', 'odowdjowd', NULL, 'Saldo', '', 'dikirim', NULL, '2025-07-07 08:40:51'),
(20, 2, 9, 1, '2025-07-08', NULL, 4, '120000.00', '0.00', 'jawa bagian selatan', NULL, 'Saldo', '', '', NULL, '2025-07-08 09:59:26'),
(21, 2, 9, 1, '2025-07-08', NULL, 4, '120000.00', '0.00', 'jawa bagian selatan', NULL, '', '', 'pending_pembayaran', NULL, '2025-07-08 17:14:20'),
(22, 2, 8, 1, '2025-07-08', NULL, 2, '100000.00', '0.00', 'jawa bagian selatan', NULL, '', '', 'pending_pembayaran', NULL, '2025-07-08 17:14:54'),
(23, 2, 9, 1, '2025-07-08', NULL, 4, '120000.00', '0.00', 'jawa bagian selatan', NULL, '', '', 'pending_pembayaran', NULL, '2025-07-08 17:16:16'),
(24, 2, 8, 1, '2025-07-08', NULL, 3, '170000.00', '0.00', 'jawa bagian selatan', NULL, 'Jemput Kurir', '', '', 'Kurir: Gojek Express', '2025-07-08 17:27:48'),
(25, 4, 15, 1, '2025-07-10', NULL, 5, '450000.00', '0.00', 'jawa', NULL, 'COD', '', '', '', '2025-07-10 03:53:52'),
(26, 4, 12, 1, '2025-07-11', NULL, 5, '125000.00', '0.00', 'jawa', NULL, 'COD', '', 'selesai', '', '2025-07-11 11:57:47'),
(27, 2, 14, 1, '2025-07-12', NULL, 2, '100000.00', '0.00', '', NULL, 'COD', '', '', 'alamskasaksj', '2025-07-12 16:14:16'),
(28, 2, 15, 1, '2025-07-12', NULL, 3, '270000.00', '0.00', '', NULL, 'COD', '', '', '', '2025-07-12 16:18:50'),
(29, 2, 14, 1, '2025-07-12', NULL, 1, '75000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', NULL, 'Jemput Kurir', '', 'selesai', 'cepet | Kurir: J&T Express', '2025-07-12 16:23:40'),
(30, 2, 13, 1, '2025-07-12', NULL, 1, '45000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', '', '', '', '', '2025-07-12 17:41:38'),
(31, 2, 6, 1, '2025-07-12', NULL, 1, '40000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', '', '', '', 'Kurir: Gojek Express', '2025-07-12 18:00:30'),
(32, 2, 6, 1, '2025-07-12', NULL, 1, '45000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', '', 'Kurir: J&T Express', '2025-07-12 18:02:20'),
(33, 2, 13, 1, '2025-07-14', NULL, 1, '45000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 18:05:31'),
(34, 2, 6, 1, '2025-07-12', NULL, 1, '20000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 18:47:22'),
(35, 2, 13, 1, '2025-07-12', NULL, 1, '45000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', 'dibatalkan', '', '2025-07-12 18:50:23'),
(36, 2, 6, 1, '2025-07-12', NULL, 1, '45000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-12 18:57:02'),
(37, 2, 10, 1, '2025-07-12', NULL, 1, '30000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 18:57:42'),
(38, 2, 6, 1, '2025-07-13', NULL, 1, '20000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 19:07:56'),
(39, 2, 14, 1, '2025-07-13', NULL, 1, '50000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 19:11:41'),
(40, 2, 15, 1, '2025-07-13', NULL, 1, '90000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '2025-07-12 19:16:15'),
(41, 2, 11, 1, '2025-07-13', NULL, 1, '45000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', 'dikirim', '', '2025-07-12 19:33:53'),
(42, 2, 15, 1, '2025-07-13', NULL, 1, '115000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: J&T Express', '2025-07-12 19:56:12'),
(43, 2, 11, 1, '2025-07-13', NULL, 1, '70000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: J&T Express', '2025-07-13 12:01:20'),
(44, 2, 14, 1, '2025-07-14', NULL, 1, '50000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', 'selesai', '', '2025-07-14 04:44:25'),
(45, 2, 15, 1, '2025-07-15', NULL, 1, '112000.00', '273820500.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'telat_pengembalian', 'Kurir: JNE', '2025-07-14 19:33:43'),
(46, 2, 11, 1, '2025-07-15', '2025-07-16', 1, '70000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: J&T Express', '2025-07-15 06:38:09'),
(47, 2, 10, 1, '2025-07-15', '2025-07-16', 1, '50000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: Gojek Express', '2025-07-15 06:40:55'),
(48, 2, 15, 1, '2025-07-15', '2025-07-16', 1, '115000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-15 07:15:51'),
(49, 2, 9, 1, '2025-07-15', '2025-07-16', 1, '55000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: J&T Express', '2025-07-15 09:49:08'),
(50, 2, 11, 1, '2025-07-16', '2025-07-17', 1, '70000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-16 16:23:58'),
(51, 4, 12, 1, '2025-07-14', '2025-07-15', 1, '45000.00', '3750.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'denda_dibayar', 'Kurir: Gojek Express', '2025-07-14 16:34:31'),
(52, 2, 11, 1, '2025-07-18', '2025-07-19', 1, '70000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 09:37:22'),
(53, 2, 11, 1, '2025-07-18', '2025-07-19', 1, '70000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 09:50:04'),
(54, 2, 11, 1, '2025-07-18', '2025-07-19', 1, '70000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 09:52:39'),
(55, 2, 11, 1, '2025-07-18', '2025-07-19', 1, '110000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kemas yang bener bos | Kurir: Gojek Express', '2025-07-18 09:57:34'),
(56, 2, 11, 1, '2025-07-18', '2025-07-19', 1, '115000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 10:15:12'),
(57, 2, 11, 2, '2025-07-18', '2025-07-19', 1, '115000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 10:35:44'),
(58, 2, 11, 2, '2025-07-18', '2025-07-19', 1, '115000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 10:40:39'),
(59, 2, 11, 3, '2025-07-18', '2025-07-19', 1, '160000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 10:44:04'),
(60, 4, 11, 2, '2025-07-18', '2025-07-19', 1, '110000.00', '0.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'selesai', 'pengemasan yang rapi | Kurir: Gojek Express', '2025-07-18 12:16:09'),
(61, 4, 18, 2, '2025-07-16', '2025-07-17', 1, '55000.00', '2250.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'denda_dibayar', 'dikemas yg baik | Kurir: J&T Express', '2025-07-18 13:03:21'),
(62, 4, 18, 1, '2025-07-18', '2025-07-19', 1, '35000.00', '0.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: Gojek Express', '2025-07-18 13:16:28'),
(63, 4, 18, 1, '2025-07-14', '2025-07-15', 1, '15000.00', '2250.00', 'jawa', 'COD', 'Saldo', '', 'denda_dibayar', '', '2025-07-18 13:26:09'),
(64, 4, 18, 1, '2025-07-15', '2025-07-16', 1, '15000.00', '4500.00', 'jawa', 'COD', 'Saldo', '', 'denda_dibayar', '', '2025-07-18 13:42:29'),
(65, 4, 18, 1, '2025-07-15', '2025-07-16', 1, '40000.00', '4500.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: J&T Express', '2025-07-18 14:43:06'),
(66, 2, 18, 2, '2025-07-18', '2025-07-19', 1, '55000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: J&T Express', '2025-07-18 14:47:38'),
(67, 4, 18, 2, '2025-07-18', '2025-07-19', 1, '55000.00', '0.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 17:25:05'),
(68, 4, 14, 1, '2025-07-18', '2025-07-19', 1, '80000.00', '0.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: Grab Express', '2025-07-18 17:26:20'),
(69, 4, 11, 1, '2025-07-18', '2025-07-19', 1, '70000.00', '0.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 17:30:44'),
(70, 4, 10, 1, '2025-07-16', '2025-07-17', 1, '55000.00', '9000.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: J&T Express', '2025-07-18 19:12:22'),
(71, 4, 14, 1, '2025-07-19', '2025-07-20', 1, '75000.00', '0.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 19:29:46'),
(72, 4, 18, 1, '2025-07-19', '2025-07-20', 1, '40000.00', '0.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 19:38:19'),
(73, 4, 11, 1, '2025-07-19', '2025-07-20', 1, '65000.00', '0.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: Gojek Express', '2025-07-18 19:38:48'),
(74, 4, 18, 1, '2025-07-19', '2025-07-20', 1, '37000.00', '0.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'menunggu_verifikasi_admin', 'Kurir: JNE', '2025-07-18 19:44:03'),
(75, 2, 18, 1, '2025-07-19', '2025-07-20', 1, '45000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: Grab Express', '2025-07-18 19:44:51'),
(76, 2, 18, 1, '2025-07-19', '2025-07-20', 1, '40000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 19:47:32'),
(77, 2, 11, 1, '2025-07-19', '2025-07-20', 1, '70000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: J&T Express', '2025-07-18 19:48:34'),
(78, 2, 10, 1, '2025-07-19', '0000-00-00', 1, '50000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', '', '', '0000-00-00 00:00:00'),
(79, 2, 11, 1, '2025-07-19', '0000-00-00', 1, '65000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', '', '', '0000-00-00 00:00:00'),
(80, 2, 15, 1, '2025-07-19', '0000-00-00', 5, '455000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', '', '', '0000-00-00 00:00:00'),
(81, 2, 11, 1, '2025-07-19', '0000-00-00', 2, '90000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'COD', 'Saldo', '', '', '', '0000-00-00 00:00:00'),
(82, 2, 15, 1, '2025-07-19', '0000-00-00', 4, '390000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', '', '', '0000-00-00 00:00:00'),
(83, 2, 11, 1, '2025-07-19', '2025-07-20', 1, '70000.00', '0.00', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'Jemput Kurir', 'Saldo', '', 'menunggu_verifikasi_admin', 'Kurir: J&T Express', '2025-07-18 21:08:39'),
(84, 4, 6, 5, '2025-07-19', '2025-07-20', 1, '130000.00', '0.00', 'jawa', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: Grab Express', '2025-07-19 06:50:26'),
(85, 5, 6, 5, '2025-07-19', '2025-07-20', 1, '130000.00', '0.00', 'Jl Ahmad yani Kecamatan Sleman RT 4 RW 5', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: Grab Express', '2025-07-19 06:55:32'),
(86, 5, 12, 1, '2025-07-19', '2025-07-24', 5, '155000.00', '0.00', 'Jl Ahmad yani Kecamatan Sleman RT 4 RW 5', 'Jemput Kurir', 'Saldo', '', 'dibatalkan', 'Kurir: Grab Express', '2025-07-19 06:57:09'),
(87, 5, 6, 2, '2025-07-19', '2025-07-22', 3, '140000.00', '0.00', 'Jl Ahmad yani Kecamatan Sleman RT 4 RW 5', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: Grab Express', '2025-07-19 07:03:12'),
(88, 5, 6, 2, '2025-07-19', '2025-07-21', 2, '110000.00', '0.00', 'Jl Ahmad yani Kecamatan Sleman RT 4 RW 5', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Kurir: Grab Express', '2025-07-19 07:03:45'),
(89, 5, 33, 2, '2025-07-14', '2025-07-18', 2, '78000.00', '3600.00', 'Jl Ahmad yani Kecamatan Sleman RT 4 RW 5', 'Jemput Kurir', 'Saldo', '', 'selesai', 'Cek | Kurir: Grab Express', '2025-07-19 09:25:39'),
(90, 5, 15, 1, '2025-07-16', '2025-07-17', 1, '112000.00', '54000.00', 'Jl Ahmad yani Kecamatan Sleman RT 4 RW 5', 'Jemput Kurir', 'Saldo', '', 'telat_pengembalian', 'Kurir: JNE', '2025-07-19 10:07:11'),
(91, 5, 13, 2, '2025-07-16', '2025-07-17', 1, '120000.00', '27000.00', 'Jl Ahmad yani Kecamatan Sleman RT 4 RW 5', 'Jemput Kurir', 'Saldo', '', 'telat_pengembalian', 'Kurir: Grab Express', '2025-07-19 10:07:39');

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
  `ktp_image` varchar(255) DEFAULT NULL,
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

INSERT INTO `users` (`id`, `username`, `nama_lengkap`, `email`, `no_hp`, `alamat`, `foto_profil`, `ktp_image`, `saldo`, `phone_number`, `address`, `profile_picture`, `created_at`, `password`, `role`) VALUES
(1, 'admin', NULL, NULL, NULL, NULL, NULL, NULL, '150000.00', NULL, NULL, NULL, '2025-06-29 21:37:43', '0192023a7bbd73250516f069df18b500', 'admin'),
(2, 'user1', 'user_Ganteng', 'bahlil@12345', '081245268145', 'JL parangtritis no 10 KEC bantul DIY RT 60 RW 90', 'assets/images/profile_pictures/profile_687a1c4a9937b.jpeg', NULL, '2611000.00', NULL, NULL, NULL, '2025-06-29 21:37:43', '6ad14ba9986e3615423dfca256d04e3f', 'user'),
(3, 'admin1', 'ADmin Ganteng', 'adm1@gmail.com', '0991219912912912', 'Jateng', 'assets/images/profile_pictures/profile_686d26149e4cd.jpeg', NULL, '815750.00', NULL, NULL, NULL, '2025-06-29 21:37:43', '0192023a7bbd73250516f069df18b500', 'admin'),
(4, 'user2', 'userrsewaken', 'user2@gmail.com', '029320930293', 'jawa', 'assets/images/profile_pictures/profile_6864a9726af86.jpg', NULL, '2021750.00', NULL, NULL, NULL, '2025-07-02 10:37:22', '6ad14ba9986e3615423dfca256d04e3f', 'user'),
(5, 'user3', 'Habibi', 'user3@gmail.com', '081245268145', 'Jl Ahmad yani Kecamatan Sleman RT 4 RW 5', 'assets/images/profile_pictures/profile_687b17bf8c180.jpeg', 'assets/images/ktp/ktp_6877db76a4da1.jpg', '394400.00', NULL, NULL, NULL, '2025-07-17 00:03:50', '6ad14ba9986e3615423dfca256d04e3f', 'user'),
(6, 'admin2', 'Prabowo ganteng', 'budi99_oyehaha@gmail.com', '019201920192', 'JL sirothol mustaqim', 'assets/images/default_profile.png', 'assets/images/ktp/ktp_6877dc0629ab3.jpg', '0.00', NULL, NULL, NULL, '2025-07-17 00:06:14', '0192023a7bbd73250516f069df18b500', 'admin');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `claimed_promos`
--
ALTER TABLE `claimed_promos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_promo_unique` (`user_id`,`promo_id`);

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
-- Indexes for table `penarikan_admin`
--
ALTER TABLE `penarikan_admin`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `user_id` (`user_id`),
  ADD KEY `id_transaksi` (`id_transaksi`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `claimed_promos`
--
ALTER TABLE `claimed_promos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `penarikan_admin`
--
ALTER TABLE `penarikan_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `riwayat_saldo`
--
ALTER TABLE `riwayat_saldo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `riwayat_topup`
--
ALTER TABLE `riwayat_topup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
