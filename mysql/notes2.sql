-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 20 Kwi 2024, 12:18
-- Wersja serwera: 8.0.36-0ubuntu0.20.04.1
-- Wersja PHP: 8.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `notes2`
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
-- Zrzut danych tabeli `notes`
--

INSERT INTO `notes` (`note_id`, `title`, `description`, `created`, `user_id`) VALUES
(5, 'das', 'asdsad', '2023-06-11 12:00:58', 7),
(6, 'fghf', 'ffhg', '2023-06-11 12:26:25', 7),
(7, 'nowa1', 'dsaasdasd', '2023-06-11 12:27:21', 7),
(8, 'nowa2', 'asdasdsad', '2023-06-11 12:34:36', 7),
(9, 'nowa3', 'sdadsa', '2023-06-11 15:28:40', 7),
(10, 'nowa4', 'asddassadsd', '2023-06-11 15:29:46', 7),
(11, 'nowa5', 'asjh67tkde3', '2023-06-11 15:38:57', 7),
(25, 'test', 'test', '2023-06-11 17:01:09', 7),
(27, 'nowa666', 'weweqweqweq', '2023-06-13 11:30:15', 7),
(28, 'aaa', 'asdasdsdasad', '2023-06-13 11:30:54', 7),
(41, 'wrzesien2', 'asddas', '2023-09-06 15:24:40', 7),
(43, 'test111', 'dupa 01.03\r\n', '2024-03-01 15:26:26', 13),
(44, 'adssdaqsad', 'sdaads', '2024-03-03 19:11:55', 10);

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
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`user_id`, `email`, `first_name`, `last_name`, `password`, `activated`) VALUES
(7, 'test@dupa.pl', 'John', 'Doe', '$2y$10$ig1iQgnWaKDcwLrWAxVhjuc96c2gt.mdbsTBP/9UxM3NH6GDVh7nO', 1),
(8, 'test3@gmail.com', 'test222', 'testowy', '$2y$10$ig1iQgnWaKDcwLrWAxVhjuc96c2gt.mdbsTBP/9UxM3NH6GDVh7nO', 1),
(10, 'test2@gmail.com', 'Wojciech', 'Pawlina', '$2y$10$aqkoGf4Z33OcCqhhdkZrK.Wqw2nz9faqQDWSta3K/1qs0pfzadOA6', 1),
(13, 'test02@gmail.com', 'Wojciech', 'Pawlina', '$2y$10$Hg1dN8oGmu/j8mSL9RpFfe3knofVIpohjE6nN5jz0Eyo1GhQOGaMq', 1),
(19, 'asddsaad@gmail.com', 'sads', 'asdsa', '$2y$10$xGsSnhVAkRrATMGGWoqMjeeNtj1T6c4fnzZZj5HAMgmoOfgVrwmN.', 0),
(20, 'wojtek.pawlina3@gmail.com', 'wwww', 'asddas', '$2y$10$O5sUPoWBQZL/eLYjU7uzgOR3Fks/2jbaMQm6sm7KnxWvqvyhEMqhq', 1),
(21, 'testowanies@gmail.com', 'dssad', 'adsads', '$2y$10$rRQFp5ahl/2WFluqjEql.ewyaPGAUrsEXG5LNruinQxBmrEEDxoAS', 0);

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
-- Zrzut danych tabeli `user_codes`
--

INSERT INTO `user_codes` (`user_code_id`, `user_id`, `code`, `expiry`) VALUES
(13, 21, 'islJq6vuXAC85Zk7jyQGNBmNnhjPuEsVYCW1Nw0GoFVGFNUvezf0CxqqKjMyA1wW', '2024-04-20 17:38:40');

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
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `notes`
--
ALTER TABLE `notes`
  MODIFY `note_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT dla tabeli `user_codes`
--
ALTER TABLE `user_codes`
  MODIFY `user_code_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `users_notes` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Ograniczenia dla tabeli `user_codes`
--
ALTER TABLE `user_codes`
  ADD CONSTRAINT `users_users_codes` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
