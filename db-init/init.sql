SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `cash`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bestaende`
--

CREATE TABLE `bestaende` (
  `id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `monat` int(11) DEFAULT NULL,
  `ausgaben` decimal(10,2) DEFAULT NULL,
  `einlagen` decimal(10,2) DEFAULT NULL,
  `bestand` decimal(10,2) NOT NULL,
  `userid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `bestaende`
--

INSERT INTO `bestaende` (`id`, `datum`, `monat`, `ausgaben`, `einlagen`, `bestand`, `userid`) VALUES
(1, '2025-10-01', 1, 0.00, 0.00, 0.00, 1),
(2, '2025-10-01', 2, 0.00, 0.00, 0.00, 1),
(3, '2025-10-01', 3, 0.00, 0.00, 0.00, 1),
(4, '2025-10-01', 4, 0.00, 0.00, 0.00, 1),
(5, '2025-10-01', 5, 0.00, 0.00, 0.00, 1),
(6, '2025-10-01', 6, 0.00, 0.00, 0.00, 1),
(7, '2025-07-01', 7, 0.00, 0.00, 0.00, 1),
(8, '2025-08-01', 8, 0.00, 0.00, 0.00, 1),
(9, '2025-09-01', 9, 0.00, 0.00, 0.00, 1),
(10, '2025-10-01', 10, 0.00, 0.00, 0.00, 1),
(11, '2025-11-01', 11, 0.00, 0.00, 0.00, 1),
(12, '2025-12-01', 12, 0.00, 0.00, 0.00, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buchungen`
--

CREATE TABLE `buchungen` (
  `id` int(11) NOT NULL,
  `barkasse` bit(1) DEFAULT NULL,
  `belegnr` longtext DEFAULT NULL,
  `datum` date NOT NULL,
  `vonan` longtext NOT NULL,
  `beschreibung` longtext NOT NULL,
  `betrag` decimal(10,2) NOT NULL,
  `typ` enum('Einlage','Ausgabe') NOT NULL,
  `userid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `buchungen`
--

INSERT INTO `buchungen` (`id`, `barkasse`, `belegnr`, `datum`, `vonan`, `beschreibung`, `betrag`, `typ`, `userid`) VALUES
(1, b'1', 'RE202521-0001', '2025-04-24', 'Einkauf Aldi', 'Tagesbedarf', 16.59, 'Ausgabe', 1),
(2, b'1', 'RE202521-0001', '2025-04-24', 'Kasse Einlage', 'Tagesbedarf', 500.00, 'Einlage', 1),
(9, b'1', 'RE202521-0009', '2025-04-23', 'Einkauf Aldi', 'Tagesbedarf', 33.81, 'Ausgabe', 1),
(13, b'1', 'RE202521-0013', '2025-04-25', 'CAP-Markt', 'Kaffee und Süßigkeiten', 6.44, 'Ausgabe', 1),
(321, b'1', 'RE202521-0321', '2025-06-09', 'Essen', 'Inder Stuttgart', 67.50, 'Ausgabe', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buchungsarten`
--

CREATE TABLE `buchungsarten` (
  `id` int(11) NOT NULL,
  `Buchungsart` varchar(255) NOT NULL DEFAULT '',
  `Dauerbuchung` bit(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `buchungsarten`
--

INSERT INTO `buchungsarten` (`id`, `Buchungsart`, `Dauerbuchung`, `created_at`, `updated_at`, `userid`) VALUES
(17, 'PayPal', b'0', '2025-04-25 20:56:45', '2025-06-04 07:54:16', 1),
(18, 'Tankstelle', b'0', '2025-04-25 20:56:45', '2025-04-25 20:58:48', 1),
(24, 'Essen', b'0', '2025-04-25 20:56:45', '2025-06-04 07:55:54', 1),
(27, 'CAP Markt ', b'0', '2025-04-25 20:56:45', '2025-06-04 06:44:40', 1),
(29, 'LIDL', b'0', '2025-04-25 20:56:45', '2025-05-05 12:52:33', 1),
(35, 'Badeeintritt', b'0', '2025-04-25 20:56:45', '2025-06-04 07:54:52', 1),
(37, 'Lindt Shop', b'0', '2025-04-25 20:56:45', '2025-06-04 07:09:11', 1),
(44, 'Kosmetikartikel', b'0', '2025-04-25 20:56:45', '2025-06-04 07:07:26', 1),
(51, 'Restaurant', b'0', '2025-04-25 20:56:45', '2025-06-11 12:27:14', 2),
(52, 'Drogerieartikel', b'0', '2025-04-25 20:56:45', '2025-06-04 07:08:53', 1),
(54, 'EDEKA', b'0', '2025-04-25 20:56:45', '2025-05-05 12:55:55', 1),
(56, 'ALDI SUED', b'0', '2025-04-25 20:56:45', '2025-05-05 12:56:28', 1),
(61, 'Eintritte', b'0', '2025-04-25 20:56:45', '2025-06-04 07:07:05', 1),
(65, '_Diverses', b'1', '2025-05-14 09:08:58', '2025-06-04 13:33:36', 1),
(66, 'Sammelbuchung Supermarkt', b'0', '2025-06-04 07:10:21', '2025-06-11 12:24:20', 1),
(69, 'Flohmarkt', b'0', '2025-06-04 07:55:26', '2025-06-11 12:24:28', 1),
(72, 'ALDI SUED ', b'0', '2025-06-11 12:23:39', '2025-06-11 12:24:10', 2),
(73, 'Essen', b'0', '2025-06-11 12:24:49', '2025-06-11 12:26:21', 2),
(74, 'Apotheke ', b'0', '2025-06-11 12:26:02', '2025-06-11 12:26:41', 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `vorname` varchar(255) NOT NULL DEFAULT '',
  `nachname` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `email`, `passwort`, `vorname`, `nachname`, `created_at`, `updated_at`) VALUES
(1, 'tester@web.de', '$2y$10$cs05zWzGCRIhxRmKyyMabuUuIweqoEC.Lak0XL068ONuKLMAyHAmW', '', '', '2025-04-25 07:57:52', '2025-04-25 07:57:52');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `bestaende`
--
ALTER TABLE `bestaende`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `buchungen`
--
ALTER TABLE `buchungen`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `buchungsarten`
--
ALTER TABLE `buchungsarten`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Buchungsart` (`Buchungsart`,`userid`) USING BTREE;

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `bestaende`
--
ALTER TABLE `bestaende`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `buchungen`
--
ALTER TABLE `buchungen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=322;

--
-- AUTO_INCREMENT für Tabelle `buchungsarten`
--
ALTER TABLE `buchungsarten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
