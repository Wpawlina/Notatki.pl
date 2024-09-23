-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Wrz 23, 2024 at 03:11 PM
-- Wersja serwera: 8.1.0
-- Wersja PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `notes2`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `notes`
--

CREATE TABLE `notes` (
  `note_id` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`note_id`, `title`, `description`, `created`, `user_id`) VALUES
(43, 'test111', 'dupa 01.03\r\n', '2024-03-01 15:26:26', 13),
(44, 'adssdaqsad', 'sdaads', '2024-03-03 19:11:55', 10),
(48, 'test1', 'asfadsafs', '2024-09-23 10:59:28', 23);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `activated` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `first_name`, `last_name`, `password`, `activated`) VALUES
(8, 'test3@gmail.com', 'test222', 'testowy', '$2y$10$ig1iQgnWaKDcwLrWAxVhjuc96c2gt.mdbsTBP/9UxM3NH6GDVh7nO', 1),
(10, 'test2@gmail.com', 'Wojciech', 'Pawlina', '$2y$10$aqkoGf4Z33OcCqhhdkZrK.Wqw2nz9faqQDWSta3K/1qs0pfzadOA6', 1),
(13, 'test02@gmail.com', 'Wojciech', 'Pawlina', '$2y$10$Hg1dN8oGmu/j8mSL9RpFfe3knofVIpohjE6nN5jz0Eyo1GhQOGaMq', 1),
(19, 'asddsaad@gmail.com', 'sads', 'asdsa', '$2y$10$xGsSnhVAkRrATMGGWoqMjeeNtj1T6c4fnzZZj5HAMgmoOfgVrwmN.', 0),
(21, 'testowanies@gmail.com', 'dssad', 'adsads', '$2y$10$rRQFp5ahl/2WFluqjEql.ewyaPGAUrsEXG5LNruinQxBmrEEDxoAS', 0),
(22, 'test@gmail.com', 'Tester', '1', '$2y$10$Ef5GXOwng06gv5ZlOgXssudghXY16CzLlpxmHAnhhbBIWl8/6HGEO', 0),
(23, 'testowe@me.com', 'me', 'me2', '$2y$10$7a2TRXiBZ9C3Y7Kbi5WZkeSRAEQMBnh0KZLVKXU0V99ratC/cc77K', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_codes`
--

CREATE TABLE `user_codes` (
  `user_code_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `code` char(64) DEFAULT NULL,
  `expiry` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_codes`
--

INSERT INTO `user_codes` (`user_code_id`, `user_id`, `code`, `expiry`) VALUES
(13, 21, 'islJq6vuXAC85Zk7jyQGNBmNnhjPuEsVYCW1Nw0GoFVGFNUvezf0CxqqKjMyA1wW', '2024-04-20 17:38:40'),
(14, 10, 'YkeGUJzLzg31RKbphC5IwKh0DrOIv6nHEBFglS53BT7pHAl59mQxnYImjTa1UNFJ', '2024-04-20 14:36:38'),
(15, 22, 'QZZNkHtxOvUG8rTnsJSYS1CGnHMLtF4wqAY86wZxv8kk8OBfo7omjptTalhmVbpU', '2024-09-24 07:50:11'),
(18, 23, 'dH8MHijAWR45oEyPFjCTnKGXynjqkm60mzowEMsHpMyOvLXtQu7tBDAGNVNNpqiy', '2024-09-24 10:58:48');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `users_notes` (`user_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indeksy dla tabeli `user_codes`
--
ALTER TABLE `user_codes`
  ADD PRIMARY KEY (`user_code_id`),
  ADD KEY `users_users_codes` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `note_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user_codes`
--
ALTER TABLE `user_codes`
  MODIFY `user_code_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `users_notes` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `user_codes`
--
ALTER TABLE `user_codes`
  ADD CONSTRAINT `users_users_codes` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
