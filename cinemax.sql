-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 13, 2025 at 12:43 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cinemax`
--

-- --------------------------------------------------------

--
-- Table structure for table `auditoriums`
--

CREATE TABLE `auditoriums` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `rows` int(11) NOT NULL,
  `cols` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auditoriums`
--

INSERT INTO `auditoriums` (`id`, `name`, `rows`, `cols`) VALUES
(1, 'Hall A', 10, 12);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `showtime_id`, `total_price`, `created_at`) VALUES
(4, 15, 70, 5.00, '2025-11-09 17:04:51'),
(5, 20, 52, 5.00, '2025-11-09 17:26:32'),
(6, 20, 70, 5.00, '2025-11-09 17:49:00'),
(7, 21, 54, 5.00, '2025-11-12 17:51:33');

-- --------------------------------------------------------

--
-- Table structure for table `booking_seats`
--

CREATE TABLE `booking_seats` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_seats`
--

INSERT INTO `booking_seats` (`id`, `booking_id`, `showtime_id`, `seat_id`) VALUES
(5, 4, 70, 31),
(6, 5, 52, 49),
(7, 6, 70, 21),
(8, 7, 54, 53);

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `poster_url` varchar(500) DEFAULT NULL,
  `rating` varchar(10) DEFAULT NULL,
  `duration_mins` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `poster_url`, `rating`, `duration_mins`, `description`, `created_at`) VALUES
(1, 'Avengers: Endgame', 'https://image.tmdb.org/t/p/w500/ulzhLuWrPK07P1YkdWQLZnQh1JL.jpg', 'PG-13', 181, 'After the devastating events of Infinity War, the Avengers assemble once more to restore balance.', '2025-11-06 18:48:26'),
(3, 'Interstellar', 'https://image.tmdb.org/t/p/w500/rAiYTfKGqDCRIIqo664sY9XZIvQ.jpg', 'PG-13', 169, 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity’s survival.', '2025-11-06 18:48:26'),
(4, 'The Batman', 'https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg', 'PG-13', 176, 'When the Riddler, a sadistic serial killer, begins murdering key political figures, Batman is forced to investigate the city’s hidden corruption.', '2025-11-06 18:48:26'),
(6, 'Avatar: The Way of Water', 'https://image.tmdb.org/t/p/w500/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg', 'PG-13', 192, 'Jake Sully lives with his newfound family on the planet of Pandora. But when a familiar threat returns, he must fight to protect his home.', '2025-11-06 18:48:26'),
(7, 'Spider-Man: No Way Home', 'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg', 'PG-13', 148, 'Peter Parker seeks Doctor Strange’s help to make people forget he is Spider-Man, but the spell goes horribly wrong.', '2025-11-06 18:48:26'),
(8, 'Frozen II', 'https://image.tmdb.org/t/p/w500/pjeMs3yqRmFL3giJy4PMXWZTTPa.jpg', 'PG', 103, 'Elsa and Anna embark on a dangerous journey far away from Arendelle to uncover the origins of Elsa’s powers.', '2025-11-06 18:48:26'),
(9, 'The Dark Knight', 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg', 'PG-13', 152, 'Batman faces the Joker, a criminal mastermind who plunges Gotham into anarchy and forces Batman closer to crossing the line between hero and vigilante.', '2025-11-06 18:48:26'),
(17, 'Annabelle', 'https://i.ytimg.com/vi/U-aSQnt7xZo/maxresdefault.jpg', 'r', 99, 'Several years after the tragic death of their daughter, a dollmaker and his wife welcome a nun and six orphaned girls into their home, where a possessed doll named Annabelle begins to wreak havoc.', '2025-11-09 16:56:35');

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` int(11) NOT NULL,
  `auditorium_id` int(11) NOT NULL,
  `row_label` char(1) NOT NULL,
  `seat_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`id`, `auditorium_id`, `row_label`, `seat_number`) VALUES
(1, 1, 'A', 1),
(11, 1, 'A', 2),
(21, 1, 'A', 3),
(31, 1, 'A', 4),
(41, 1, 'A', 5),
(51, 1, 'A', 6),
(61, 1, 'A', 7),
(71, 1, 'A', 8),
(81, 1, 'A', 9),
(91, 1, 'A', 10),
(101, 1, 'A', 11),
(111, 1, 'A', 12),
(2, 1, 'B', 1),
(12, 1, 'B', 2),
(22, 1, 'B', 3),
(32, 1, 'B', 4),
(42, 1, 'B', 5),
(52, 1, 'B', 6),
(62, 1, 'B', 7),
(72, 1, 'B', 8),
(82, 1, 'B', 9),
(92, 1, 'B', 10),
(102, 1, 'B', 11),
(112, 1, 'B', 12),
(3, 1, 'C', 1),
(13, 1, 'C', 2),
(23, 1, 'C', 3),
(33, 1, 'C', 4),
(43, 1, 'C', 5),
(53, 1, 'C', 6),
(63, 1, 'C', 7),
(73, 1, 'C', 8),
(83, 1, 'C', 9),
(93, 1, 'C', 10),
(103, 1, 'C', 11),
(113, 1, 'C', 12),
(4, 1, 'D', 1),
(14, 1, 'D', 2),
(24, 1, 'D', 3),
(34, 1, 'D', 4),
(44, 1, 'D', 5),
(54, 1, 'D', 6),
(64, 1, 'D', 7),
(74, 1, 'D', 8),
(84, 1, 'D', 9),
(94, 1, 'D', 10),
(104, 1, 'D', 11),
(114, 1, 'D', 12),
(5, 1, 'E', 1),
(15, 1, 'E', 2),
(25, 1, 'E', 3),
(35, 1, 'E', 4),
(45, 1, 'E', 5),
(55, 1, 'E', 6),
(65, 1, 'E', 7),
(75, 1, 'E', 8),
(85, 1, 'E', 9),
(95, 1, 'E', 10),
(105, 1, 'E', 11),
(115, 1, 'E', 12),
(6, 1, 'F', 1),
(16, 1, 'F', 2),
(26, 1, 'F', 3),
(36, 1, 'F', 4),
(46, 1, 'F', 5),
(56, 1, 'F', 6),
(66, 1, 'F', 7),
(76, 1, 'F', 8),
(86, 1, 'F', 9),
(96, 1, 'F', 10),
(106, 1, 'F', 11),
(116, 1, 'F', 12),
(7, 1, 'G', 1),
(17, 1, 'G', 2),
(27, 1, 'G', 3),
(37, 1, 'G', 4),
(47, 1, 'G', 5),
(57, 1, 'G', 6),
(67, 1, 'G', 7),
(77, 1, 'G', 8),
(87, 1, 'G', 9),
(97, 1, 'G', 10),
(107, 1, 'G', 11),
(117, 1, 'G', 12),
(8, 1, 'H', 1),
(18, 1, 'H', 2),
(28, 1, 'H', 3),
(38, 1, 'H', 4),
(48, 1, 'H', 5),
(58, 1, 'H', 6),
(68, 1, 'H', 7),
(78, 1, 'H', 8),
(88, 1, 'H', 9),
(98, 1, 'H', 10),
(108, 1, 'H', 11),
(118, 1, 'H', 12),
(9, 1, 'I', 1),
(19, 1, 'I', 2),
(29, 1, 'I', 3),
(39, 1, 'I', 4),
(49, 1, 'I', 5),
(59, 1, 'I', 6),
(69, 1, 'I', 7),
(79, 1, 'I', 8),
(89, 1, 'I', 9),
(99, 1, 'I', 10),
(109, 1, 'I', 11),
(119, 1, 'I', 12),
(10, 1, 'J', 1),
(20, 1, 'J', 2),
(30, 1, 'J', 3),
(40, 1, 'J', 4),
(50, 1, 'J', 5),
(60, 1, 'J', 6),
(70, 1, 'J', 7),
(80, 1, 'J', 8),
(90, 1, 'J', 9),
(100, 1, 'J', 10),
(110, 1, 'J', 11),
(120, 1, 'J', 12);

-- --------------------------------------------------------

--
-- Table structure for table `showtimes`
--

CREATE TABLE `showtimes` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `auditorium_id` int(11) NOT NULL,
  `show_datetime` datetime NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `showtimes`
--

INSERT INTO `showtimes` (`id`, `movie_id`, `auditorium_id`, `show_datetime`, `price`) VALUES
(17, 1, 1, '2025-11-08 18:00:00', 5.00),
(18, 1, 1, '2025-11-08 20:00:00', 5.00),
(51, 1, 1, '2025-11-05 18:30:00', 9.50),
(52, 6, 1, '2025-11-28 22:39:00', 5.00),
(53, 6, 1, '2025-11-29 13:43:00', 5.00),
(54, 6, 1, '2025-12-19 12:42:00', 5.00),
(55, 6, 1, '2025-12-24 16:42:00', 5.00),
(56, 1, 1, '2025-11-27 22:43:00', 5.00),
(57, 1, 1, '2025-12-25 12:45:00', 5.00),
(58, 1, 1, '2025-12-30 13:43:00', 5.00),
(59, 8, 1, '2025-11-27 12:44:00', 5.00),
(60, 8, 1, '2025-12-25 22:44:00', 5.00),
(61, 8, 1, '2025-12-31 22:44:00', 5.00),
(62, 3, 1, '2025-11-30 13:48:00', 5.00),
(63, 3, 1, '2025-12-26 22:45:00', 5.00),
(67, 7, 1, '2025-11-28 15:54:00', 5.00),
(68, 4, 1, '2025-12-18 22:54:00', 5.00),
(69, 9, 1, '2025-12-20 16:50:00', 5.00),
(70, 17, 1, '2025-11-29 23:29:00', 5.00),
(71, 17, 1, '2025-12-23 20:27:00', 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(15, 'Hsu', NULL, 'rimmlv20@gmail.com', '$2y$10$9TiWUrSipx0cKbt53QK9v./X1nUsvPocGjPM5UBZBSwpsmGHxUJBi', 'customer', '2025-11-09 15:50:32'),
(16, 'Admin', 'admin', 'admin@example.com', '$2y$10$cO7MM6bJO6zqPVT9keTt4O6iPu4Ai.WOrlxqfjTl3UM3Oh9eYbg1C', 'admin', '2025-11-09 15:51:03'),
(20, 'Rim', NULL, 'rimm123@gmail.com', '$2y$10$ps43IEeSM2KfLtrmcqQEbuo1Asnn0rmP4/sZqbNCDtv52VdnahtUe', 'customer', '2025-11-09 17:22:01'),
(21, 'Jay', NULL, 'jay2005@gmail.com', '$2y$10$s4KCQcvv1U2phtkz9P/HcuIgLPgGsiYXSZUzBTYTI/6FUw8mHAXe2', 'customer', '2025-11-12 17:50:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auditoriums`
--
ALTER TABLE `auditoriums`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `showtime_id` (`showtime_id`);

--
-- Indexes for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_showtime_seat` (`showtime_id`,`seat_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `seat_id` (`seat_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_seat` (`auditorium_id`,`row_label`,`seat_number`);

--
-- Indexes for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `auditorium_id` (`auditorium_id`),
  ADD KEY `show_datetime` (`show_datetime`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auditoriums`
--
ALTER TABLE `auditoriums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `booking_seats`
--
ALTER TABLE `booking_seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD CONSTRAINT `booking_seats_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_seats_ibfk_2` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_seats_ibfk_3` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`auditorium_id`) REFERENCES `auditoriums` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `showtimes_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `showtimes_ibfk_2` FOREIGN KEY (`auditorium_id`) REFERENCES `auditoriums` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
