-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Gegenereerd op: 18 jun 2025 om 08:52
-- Serverversie: 5.7.24
-- PHP-versie: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lego_webshop`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `gebruikers`
--

CREATE TABLE `gebruikers` (
  `id` int(11) NOT NULL,
  `naam` varchar(100) NOT NULL,
  `achternaam` varchar(100) NOT NULL,
  `wachtwoord` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `aangemaakt_op` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `gebruikers`
--

INSERT INTO `gebruikers` (`id`, `naam`, `achternaam`, `wachtwoord`, `email`, `aangemaakt_op`) VALUES
(1, 'Test', 'Gebruiker', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'test@example.com', '2025-06-05 10:44:43'),
(2, 'deamien', 'ruedisueli', '$2y$10$/x4H1NXjCpupMMgHZozFv.bMtLLyWM9uCRAY.MVOn5JiH/RGDphPa', 'deamienruedisueli@gmail.com', '2025-06-06 10:28:27'),
(3, 'beap', 'beap', '$2y$10$H9RH8zssa13bsMd2z.fu3OXBEXoXBs10t0n1uB9S3MYpv0.5pxqEG', 'beap@gmail.com', '2025-06-09 18:08:25'),
(4, 'a', 'a', '$2y$10$Bpubw1qVa.0o2Fb.dV1OqOLp7PA97rdvXP6Rh1Mcju8XsTMvxW45.', 'adenago@gobmfb.com', '2025-06-11 07:45:43'),
(5, 'naan', 'eldenriing', '$2y$10$chtdUP8XNCi3X/RDqnf24.P97rDd53tMtmUfoFcmsVRCmatytyngy', '40212771@roctilburg.nl', '2025-06-11 07:51:48');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `producten`
--

CREATE TABLE `producten` (
  `id` int(11) NOT NULL,
  `naam` varchar(200) NOT NULL,
  `beschrijving` text,
  `prijs` decimal(8,2) NOT NULL,
  `voorraad` int(11) DEFAULT '0',
  `afbeelding` varchar(255) DEFAULT NULL,
  `in_aanbieding` tinyint(1) DEFAULT '0',
  `aanbieding_prijs` decimal(8,2) DEFAULT NULL,
  `leeftijd` varchar(50) DEFAULT NULL,
  `aantal_stukjes` int(11) DEFAULT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `aangemaakt_op` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `producten`
--

INSERT INTO `producten` (`id`, `naam`, `beschrijving`, `prijs`, `voorraad`, `afbeelding`, `in_aanbieding`, `aanbieding_prijs`, `leeftijd`, `aantal_stukjes`, `categorie`, `aangemaakt_op`) VALUES
(1, 'LEGO Creator Expert Taj Mahal', 'Prachtige replica van het beroemde Taj Mahal monument', '369.99', 4999, '1.jpg', 1, '299.99', '16+', 5923, 'Creator Expert', '2025-06-05 10:44:43'),
(2, 'LEGO City Politiebureau', 'Groot politiebureau met gevangenis en voertuigen', '99.99', 16666, '2.jpg', 0, NULL, '6+', 743, 'City', '2025-06-05 10:44:43'),
(3, 'LEGO Friends Heartlake City Resort', 'Luxe resort met zwembad en activiteiten', '79.99', 1787654, '3.jpg', 1, '59.99', '7+', 1017, 'Friends', '2025-06-05 10:44:43'),
(4, 'LEGO Technic Bugatti Chiron', 'Gedetailleerde supercar met werkende functies', '349.99', 8565546, '4.jpg', 0, NULL, '16+', 3599, 'Technic', '2025-06-05 10:44:43'),
(5, 'LEGO Harry Potter Zweinstein Kasteel', 'Het iconische kasteel uit de films', '399.99', 1264466, '5.jpg', 1, '349.99', '16+', 6020, 'Harry Potter', '2025-06-05 10:44:43'),
(6, 'LEGO Star Wars Millennium Falcon', 'Legendaire ruimteschip uit Star Wars', '159.99', 2066787578, '6.jpg', 0, NULL, '9+', 1351, 'Star Wars', '2025-06-05 10:44:43'),
(7, 'PAC-MAN arcade', 'Waka Waka Waka ', '69.00', 690, '7.jpg\r\n', 0, NULL, '16+', 2651, 'pacman', '2025-06-13 11:32:09'),
(8, 'Keith Haring – Dansende figuren', 'door een gozer gemaakt die meestal geen shirt aan heeft', '119.99', 568, '8.jpg', 1, '99.99', '18+', 1773, 'art', '2025-06-18 07:43:18');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `gebruikers`
--
ALTER TABLE `gebruikers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexen voor tabel `producten`
--
ALTER TABLE `producten`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `gebruikers`
--
ALTER TABLE `gebruikers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT voor een tabel `producten`
--
ALTER TABLE `producten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
