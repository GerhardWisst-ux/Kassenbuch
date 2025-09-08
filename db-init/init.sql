CREATE TABLE `bestaende` (
  `id` int(11) NOT NULL,
  `kassennummer` int(151) NOT NULL,
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

INSERT INTO `bestaende` (`id`, `kassennummer`, `datum`, `monat`, `ausgaben`, `einlagen`, `bestand`, `userid`) VALUES
(38, 1, '2025-01-01', 1, 0.00, 0.00, 0.00, 10),
(39, 1, '2025-02-01', 2, 0.00, 0.00, 0.00, 10),
(40, 1, '2025-03-01', 3, 0.00, 0.00, 0.00, 10),
(41, 1, '2025-04-01', 4, 0.00, 0.00, 0.00, 10),
(42, 1, '2025-05-01', 5, 0.00, 0.00, 0.00, 10),
(43, 1, '2025-06-01', 6, 0.00, 0.00, 0.00, 10),
(44, 1, '2025-07-01', 7, 0.00, 0.00, 0.00, 10),
(45, 1, '2025-08-01', 8, 15.45, 350.00, 334.55, 10),
(46, 1, '2025-09-01', 9, 0.00, 0.00, 0.00, 10),
(47, 1, '2025-10-01', 10, 0.00, 0.00, 0.00, 10),
(48, 1, '2025-11-01', 11, 0.00, 0.00, 0.00, 10),
(49, 1, '2025-12-01', 12, 0.00, 0.00, 0.00, 10),
(50, 1, '2025-01-01', 1, 400.00, 400.00, 0.00, 1),
(51, 1, '2025-02-01', 2, 400.00, 400.00, 0.00, 1),
(52, 1, '2025-03-01', 3, 400.00, 400.00, 0.00, 1),
(53, 1, '2025-04-01', 4, 400.00, 400.00, 0.00, 1),
(54, 1, '2025-05-01', 5, 569.90, 600.00, 30.10, 1),
(55, 1, '2025-06-01', 6, 378.12, 400.00, 51.98, 1),
(56, 1, '2025-07-01', 7, 404.34, 400.00, 47.64, 1),
(57, 1, '2025-08-01', 8, 445.01, 400.00, 2.63, 1),
(58, 1, '2025-09-01', 9, 94.67, 400.00, 307.96, 1),
(59, 1, '2025-10-01', 10, 0.00, 0.00, 307.96, 1),
(60, 1, '2025-11-01', 11, 0.00, 0.00, 307.96, 1),
(61, 1, '2025-12-01', 12, 0.00, 0.00, 307.96, 1),
(62, 2, '2025-01-01', 1, 0.00, 0.00, 0.00, 1),
(63, 2, '2025-02-01', 2, 0.00, 0.00, 0.00, 1),
(64, 2, '2025-03-01', 3, 0.00, 0.00, 0.00, 1),
(65, 2, '2025-04-01', 4, 0.00, 0.00, 0.00, 1),
(66, 2, '2025-05-01', 5, 0.00, 0.00, 0.00, 1),
(67, 2, '2025-06-01', 6, 0.00, 0.00, 0.00, 1),
(68, 2, '2025-07-01', 7, 0.00, 0.00, 0.00, 1),
(69, 2, '2025-08-01', 8, 0.00, 0.00, 0.00, 1),
(70, 2, '2025-09-01', 9, 75.99, 400.00, 324.01, 1),
(71, 2, '2025-10-01', 10, 0.00, 0.00, 324.01, 1),
(72, 2, '2025-11-01', 11, 0.00, 0.00, 324.01, 1),
(73, 2, '2025-12-01', 12, 0.00, 0.00, 324.01, 1),
(74, 3, '2025-01-01', 1, 0.00, 0.00, 0.00, 1),
(75, 3, '2025-02-01', 2, 0.00, 0.00, 0.00, 1),
(76, 3, '2025-03-01', 3, 0.00, 0.00, 0.00, 1),
(77, 3, '2025-04-01', 4, 0.00, 0.00, 0.00, 1),
(78, 3, '2025-05-01', 5, 0.00, 0.00, 0.00, 1),
(79, 3, '2025-06-01', 6, 0.00, 0.00, 0.00, 1),
(80, 3, '2025-07-01', 7, 0.00, 0.00, 0.00, 1),
(81, 3, '2025-08-01', 8, 0.00, 0.00, 0.00, 1),
(82, 3, '2025-09-01', 9, 0.00, 0.00, 0.00, 1),
(83, 3, '2025-10-01', 10, 0.00, 0.00, 0.00, 1),
(84, 3, '2025-11-01', 11, 0.00, 0.00, 0.00, 1),
(85, 3, '2025-12-01', 12, 0.00, 0.00, 0.00, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buchungen`
--

CREATE TABLE `buchungen` (
  `id` int(11) NOT NULL,
  `kassennummer` int(11) NOT NULL,
  `barkasse` bit(1) DEFAULT NULL,
  `belegnr` longtext DEFAULT NULL,
  `datum` date NOT NULL,
  `vonan` longtext NOT NULL,
  `beschreibung` longtext NOT NULL,
  `betrag` decimal(10,2) NOT NULL,
  `typ` enum('Einlage','Ausgabe') NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `buchungsart` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `buchungen`
--

INSERT INTO `buchungen` (`id`, `kassennummer`, `barkasse`, `belegnr`, `datum`, `vonan`, `beschreibung`, `betrag`, `typ`, `userid`, `buchungsart`) VALUES
(1, 1, b'1', 'RE202521-0001', '2025-04-24', 'Einkauf Aldi', 'Tagesbedarf', 10.59, 'Ausgabe', 1, 'ALDI SUED'),
(2, 1, b'1', 'RE202521-0002', '2025-07-30', 'ALDI SUED', 'Tagesbedarf', 26.51, 'Ausgabe', 1, 'ALDI SUED'),
(9, 1, b'1', 'RE202521-0009', '2025-04-23', 'Einkauf Aldi', 'Tagesbedarf', 13.00, 'Ausgabe', 1, 'ALDI SUED'),
(244, 1, b'1', 'RE202521-244', '2025-04-28', 'Flohmarkt', ' Plochingen CDs und DVDs', 2.56, 'Ausgabe', 1, 'Flohmarkt'),
(245, 1, b'1', 'RE202521-245', '2025-04-27', 'Flohmarkt', ' Plochingen Verlängerungskabel', 4.00, 'Ausgabe', 1, 'Flohmarkt'),
(247, 1, b'1', 'RE202521-0247', '2025-04-27', 'Flohmarkt', ' Plochingen Socken', 10.00, 'Ausgabe', 1, 'Flohmarkt'),
(248, 1, b'1', 'RE202521-0248', '2025-04-29', 'ALDI SUED', 'Tagesbedarf', 5.67, 'Ausgabe', 1, 'ALDI SUED'),
(249, 1, b'1', 'RE202521-0249', '2025-04-30', 'Einkauf Aldi', 'Tagesbedarf', 14.25, 'Ausgabe', 1, 'ALDI SUED'),
(250, 1, b'1', 'RE202521-0250', '2025-04-30', 'Eintritte', 'Spohn 1 Stunde Tennis', 10.00, 'Ausgabe', 1, 'Eintritte'),
(251, 1, b'1', 'RE202521-0251', '2025-04-17', 'LIDL', 'Tagesbedarf', 26.57, 'Ausgabe', 1, 'LIDL'),
(252, 1, b'1', 'RE202521-0252', '2025-04-21', 'Flohmarkt', 'Süßen Verschiedenes', 14.00, 'Ausgabe', 1, 'Flohmarkt'),
(253, 1, b'1', 'RE202521-0253', '2025-03-29', 'Eintritte', 'Spohn Tennis 1 Stunde', 10.00, 'Ausgabe', 1, 'Eintritte'),
(255, 1, b'1', 'RE202521-0255', '2025-01-31', 'Einkäufe', 'Tagesbedarf', 400.00, 'Ausgabe', 1, 'Einkäufe'),
(256, 1, b'1', 'RE202521-0256', '2025-02-28', 'Sammelbuchung Supermarkt', 'Tagesbedarfe Februar', 400.00, 'Ausgabe', 1, 'Sammelbuchung Supermarkt'),
(257, 1, b'1', 'RE202521-0257', '2025-03-31', 'Sammelbuchung Supermarkt', 'Tagesbedarfe März', 390.00, 'Ausgabe', 1, 'Sammelbuchung Supermarkt'),
(258, 1, b'1', 'RE202521-0258', '2025-05-01', 'Essen', 'Inder Geburtstag Restaurant Ganseha', 62.50, 'Ausgabe', 1, 'Essen'),
(260, 1, b'1', 'RE202521-0260', '2025-05-02', 'Einkauf Aldi', 'Tagesbedarf', 12.51, 'Ausgabe', 1, 'ALDI SUED'),
(261, 1, b'1', 'RE202521-0261', '2025-05-02', 'Einlage', 'Kasse Einlage', 400.00, 'Einlage', 1, 'Einlage'),
(262, 1, b'1', 'RE202521-0262', '2025-05-05', 'CAP Markt ', 'Tagesbedarf', 1.74, 'Ausgabe', 1, 'CAP Markt '),
(263, 1, b'1', 'RE202521-0263', '2025-05-04', 'Flohmarkt', 'Fellbach kleine Heizung, Insektenfänger, DVD', 11.00, 'Ausgabe', 1, 'Flohmarkt'),
(264, 1, b'1', 'RE202521-0264', '2025-01-01', 'Einlage', 'Kasse Einlage', 400.00, 'Einlage', 1, 'Einlage'),
(265, 1, b'1', 'RE202521-0265', '2025-02-01', 'Einlage', 'Kasse Einlage', 400.00, 'Einlage', 1, 'Einlage'),
(266, 1, b'1', 'RE202521-0266', '2025-03-01', 'Einlage', 'Kasse Einlage', 400.00, 'Einlage', 1, 'Einlage'),
(267, 1, b'1', 'RE202521-0267', '2025-04-01', 'Einlage', 'Kasse Einlage', 400.00, 'Einlage', 1, 'Einlage'),
(268, 1, b'1', 'RE202521-0268', '2025-04-30', 'Sammelbuchung Supermarkt', 'Tagesbedarfe April', 289.36, 'Ausgabe', 1, 'Sammelbuchung Supermarkt'),
(270, 1, b'1', 'RE202521-0270', '2025-05-06', 'Essen', 'Essen', 33.00, 'Ausgabe', 1, 'Essen'),
(272, 1, b'1', 'RE202521-0272', '2025-05-06', 'Essen', '2 Latte Macciato', 10.00, 'Ausgabe', 1, 'Essen'),
(274, 1, b'1', 'RE202521-0274', '2025-05-10', 'LIDL', 'Tagesbedarf', 19.51, 'Ausgabe', 1, 'LIDL'),
(275, 1, b'1', 'RE202521-0275', '2025-05-09', 'Essen', 'Restaurant Ganseha Langenargen', 34.00, 'Ausgabe', 1, 'Essen'),
(276, 1, b'1', 'RE202521-0276', '2025-05-08', '2 Fahrräder', 'Leihe', 20.00, 'Ausgabe', 1, '2 Fahrräder'),
(277, 1, b'1', 'RE202521-0277', '2025-05-06', 'Einlage Urlaub', 'Einlage Urlaub', 200.00, 'Einlage', 1, 'Einlage Urlaub'),
(278, 1, b'1', 'RE202521-0278', '2025-05-08', 'Essen', 'Restaurant Nordsee Lindau', 30.40, 'Ausgabe', 1, 'Essen'),
(279, 1, b'1', 'RE202521-0279', '2025-05-07', 'Essen', 'Restaurant Vietnamese Meersburg', 26.00, 'Ausgabe', 1, 'Essen'),
(280, 1, b'1', 'RE202521-0280', '2025-05-09', 'CAP-Markt', 'Tagesbedarf', 3.50, 'Ausgabe', 1, 'CAP Markt'),
(282, 1, b'1', 'RE202521-0282', '2025-05-23', 'LIDL', 'Tagesbedarf', 11.56, 'Ausgabe', 1, 'LIDL'),
(283, 1, b'1', 'RE202521-0283', '2025-05-22', 'ALDI SUED', 'Tagesbedarf', 8.54, 'Ausgabe', 1, 'ALDI SUED'),
(284, 1, b'1', 'RE202521-0284', '2025-05-20', 'Drogeriemarkt', 'MH Muller Handels GmbH Schampoo und Duschgel', 5.40, 'Ausgabe', 1, 'Drogeriemarkt'),
(285, 1, b'1', 'RE202521-0285', '2025-05-20', 'Essen', 'Bärenschlössle Essen', 14.80, 'Ausgabe', 1, 'Essen'),
(286, 1, b'1', 'RE202521-0286', '2025-05-18', 'Flohmarkt', 'Darts Scheibe und Kleinigkeiten', 10.00, 'Ausgabe', 1, 'Flohmarkt'),
(287, 1, b'1', 'RE202521-0287', '2025-05-19', 'ALDI SUED', 'Tagesbedarf', 12.41, 'Ausgabe', 1, 'ALDI SUED'),
(288, 1, b'1', 'RE202521-0288', '2025-05-16', 'LIDL', 'Tagesbedarf', 15.41, 'Ausgabe', 1, 'LIDL'),
(289, 1, b'1', 'RE202521-0289', '2025-05-14', 'LIDL', 'Tagesbedarf', 13.57, 'Ausgabe', 1, 'LIDL'),
(290, 1, b'1', 'RE202521-0290', '2025-05-21', 'LIDL', 'Tagesbedarf', 9.74, 'Ausgabe', 1, 'LIDL'),
(291, 1, b'1', 'RE202521-0291', '2025-05-12', 'Essen', 'Ajran und Getränk', 5.00, 'Ausgabe', 1, 'Essen'),
(292, 1, b'1', 'RE202521-0292', '2025-05-10', 'LIDL', 'Tagesbedarf', 18.54, 'Ausgabe', 1, 'LIDL'),
(293, 1, b'1', 'RE202521-0293', '2025-05-26', 'ALDI SUED', 'Tagesbedarf', 13.77, 'Ausgabe', 1, 'ALDI SUED'),
(294, 1, b'1', 'RE202521-0294', '2025-05-31', 'Sammelbuchung Supermarkt', 'Tagesbedarfe Mai', 160.00, 'Ausgabe', 1, 'Sammelbuchung Supermarkt'),
(296, 1, b'1', 'RE202521-0296', '2025-06-03', 'CAP Markt ', 'Tagesbedarf', 2.51, 'Ausgabe', 1, 'CAP Markt '),
(297, 1, b'1', 'RE202521-0297', '2025-06-01', 'Einlage', 'Bareinlage Mai', 400.00, 'Einlage', 1, 'Einlage'),
(298, 1, b'1', 'RE202521-0298', '2025-06-03', 'LIDL', 'Tagesbedarf', 9.91, 'Ausgabe', 1, 'LIDL'),
(299, 1, b'1', 'RE202521-299', '2025-05-24', 'Eintritte', 'Spohn Tennis 1 Stunde', 5.00, 'Ausgabe', 1, 'Eintritte'),
(300, 1, b'1', 'RE202521-0300', '2025-05-31', 'Essen', 'Famlienzentrum Mettingen', 2.00, 'Ausgabe', 1, 'Essen'),
(301, 1, b'1', 'RE202521-0301', '2025-06-04', 'ALDI SUED', 'Tagesbedarf', 5.01, 'Ausgabe', 1, 'ALDI SUED'),
(303, 1, b'1', 'RE202521-0303', '2025-06-05', 'CAP Markt ', 'Tagesbedarf', 6.61, 'Ausgabe', 1, 'CAP Markt '),
(304, 1, b'1', 'RE202521-0304', '2025-06-05', 'Essen', 'Döner Obertürkheim', 8.00, 'Ausgabe', 1, 'Essen'),
(399, 1, b'1', 'RE202521-0288', '2025-07-01', 'Einlage', 'Kasse Einlage', 400.00, 'Einlage', 1, 'Einlage'),
(400, 1, b'1', 'RE202521-0400', '2025-07-29', 'CAP Markt ', 'Tagesbedarf', 6.64, 'Ausgabe', 1, 'CAP Markt '),
(401, 1, b'1', 'RE202521-0401', '2025-07-18', 'Eintritte', 'Inselbad', 5.00, 'Ausgabe', 1, 'Eintritte'),
(402, 1, b'1', 'RE202521-0402', '2025-07-19', 'LIDL', 'Tagesbedarf', 22.54, 'Ausgabe', 1, 'LIDL'),
(403, 1, b'1', 'RE202521-0403', '2025-07-11', 'Eintritte', 'Inselbad', 5.00, 'Ausgabe', 1, 'Eintritte'),
(404, 1, b'1', 'RE202521-0404', '2025-07-24', 'Eintritte', 'Leuze', 21.00, 'Ausgabe', 1, 'Eintritte'),
(405, 1, b'1', 'RE202521-0405', '2025-07-27', 'Essen', 'Deizisau Schnitzel, Pommes, Currywurst', 23.00, 'Ausgabe', 1, 'Essen'),
(406, 1, b'1', 'RE202521-0406', '2025-07-29', 'ALDI SUED', 'Tagesbedarf', 6.60, 'Ausgabe', 1, 'ALDI SUED'),
(407, 1, b'1', 'RE202521-0407', '2025-07-28', 'ALDI SUED', 'Tagesbedarf', 11.84, 'Ausgabe', 1, 'ALDI SUED'),
(408, 1, b'1', 'RE202521-0408', '2025-07-19', 'Flohmarkt', 'Kaltental', 2.00, 'Ausgabe', 1, 'Flohmarkt'),
(409, 1, b'1', 'RE202521-0409', '2025-06-29', 'Eintritte', 'Bad Öhningen', 6.00, 'Ausgabe', 1, 'Eintritte'),
(410, 1, b'1', 'RE202521-0410', '2025-06-29', 'Essen', 'Schnitzel und Currywurst Bad', 23.00, 'Ausgabe', 1, 'Essen'),
(411, 1, b'1', 'RE202521-0411', '2025-06-15', 'ALDI SUED', 'Tagesbedarf', 18.54, 'Ausgabe', 1, 'ALDI SUED'),
(412, 1, b'1', 'RE202521-0412', '2025-06-30', 'ALDI SUED', 'Tagesbedarf', 18.54, 'Ausgabe', 1, 'ALDI SUED'),
(413, 1, b'1', 'RE202521-0413', '2025-06-30', 'Sammelbuchung Supermarkt', 'Tagesbedarfe Juli 2025', 280.00, 'Ausgabe', 1, 'Sammelbuchung Supermarkt'),
(414, 1, b'1', 'RE202521-0414', '2025-07-12', 'LIDL', 'Tagesbedarf', 17.84, 'Ausgabe', 1, 'LIDL'),
(415, 1, b'1', 'RE202521-0415', '2025-07-05', 'ALDI SUED', 'Tagesbedarf', 15.78, 'Ausgabe', 1, 'ALDI SUED'),
(416, 1, b'1', 'RE202521-0416', '2025-07-31', 'ALDI SUED', 'Tagesbedarf', 9.18, 'Ausgabe', 1, 'ALDI SUED'),
(417, 1, b'1', 'RE202521-0417', '2025-07-07', 'ALDI SUED', 'Tagesbedarf', 12.54, 'Ausgabe', 1, 'ALDI SUED'),
(418, 1, b'1', 'RE202521-0418', '2025-07-09', 'LIDL', 'Tagesbedarf', 16.87, 'Ausgabe', 1, 'LIDL'),
(419, 1, b'1', 'RE202521-0419', '2025-07-31', 'Sammelbuchung Supermarkt', 'Tagesbedarf', 200.00, 'Ausgabe', 1, 'Sammelbuchung Supermarkt'),
(420, 1, b'1', 'RE202521-0420', '2025-08-11', 'ALDI SUED', 'Tagesbedarf', 10.02, 'Ausgabe', 1, 'ALDI SUED'),
(421, 1, b'1', 'RE202521-0421', '2025-08-01', 'Einlage', 'Kasse Einlage August 2025', 400.00, 'Einlage', 1, 'Einlage'),
(422, 1, b'1', 'RE202521-0422', '2025-08-11', 'Essen', 'Bäcker 2 Schnitten mit Pistazien', 4.60, 'Ausgabe', 1, 'Essen'),
(423, 1, b'1', 'RE202521-0423', '2025-08-11', 'REWE', 'Tagesbedarf Rewe', 3.50, 'Ausgabe', 1, 'REWE'),
(424, 1, b'1', 'RE202521-0424', '2025-08-09', 'LIDL', 'Tagesbedarf', 13.41, 'Ausgabe', 1, 'LIDL'),
(425, 1, b'1', 'RE202521-0425', '2025-08-05', 'Baumarkt', 'Pinsel Bauhaus', 7.99, 'Ausgabe', 1, 'Baumarkt'),
(426, 1, b'1', 'RE202521-0426', '2025-08-11', 'ALDI SUED', 'Tagesbedarf', 11.45, 'Ausgabe', 1, 'ALDI SUED'),
(427, 1, b'1', 'RE202521-0427', '2025-08-08', 'LIDL', 'Tagesbedarf', 13.40, 'Ausgabe', 1, 'LIDL'),
(428, 1, b'1', 'RE202521-0428', '2025-08-10', 'Badeeintritt', '2 x Deizisau', 9.80, 'Ausgabe', 1, 'Badeeintritt'),
(429, 1, b'1', 'RE202521-0429', '2025-08-10', 'Essen', 'Pommes Frittes Bad Deizisau', 4.00, 'Ausgabe', 1, 'Essen'),
(430, 1, b'1', 'RE202521-0430', '2025-08-02', 'ALDI SUED', 'Tagesbedarf', 16.85, 'Ausgabe', 1, 'ALDI SUED'),
(431, 1, b'1', 'RE202521-0431', '2025-08-13', 'Essen', 'Sindelfingen Türke Falafel und Getränk', 8.40, 'Ausgabe', 1, 'Essen'),
(432, 1, b'1', 'RE202521-0432', '2025-08-13', 'CAP Markt ', 'Tagesbedarf', 5.05, 'Ausgabe', 1, 'CAP Markt '),
(435, 1, b'1', 'RE202521-0435', '2025-08-14', 'ALDI SUED', 'Tagesbedarf', 13.08, 'Ausgabe', 1, 'ALDI SUED'),
(436, 1, b'1', 'RE202521-0436', '2025-08-16', 'Flohmarkt', 'Flohmarkt Winnenden Tastatur, Kaktus, DVD, 2 CD', 10.00, 'Ausgabe', 1, 'Flohmarkt'),
(437, 1, b'1', 'RE202521-0437', '2025-08-16', 'LIDL', 'Tagesbedarf', 13.41, 'Ausgabe', 1, 'LIDL'),
(438, 1, b'1', 'RE202521-0438', '2025-08-18', 'Essen', 'Fellbach Bäckerei', 4.45, 'Ausgabe', 1, 'Essen'),
(439, 1, b'1', 'RE202521-0439', '2025-08-19', 'CAP Markt ', 'Tagesbedarf', 6.68, 'Ausgabe', 1, 'CAP Markt '),
(440, 1, b'1', 'RE202521-0440', '2025-08-19', 'ALDI SUED', 'Tagesbedarf', 7.07, 'Ausgabe', 1, 'ALDI SUED'),
(441, 1, b'1', 'RE202521-0441', '2024-12-31', 'Essen', 'Silvestermenü', 75.00, 'Ausgabe', 1, 'Essen'),
(442, 1, b'1', 'RE202521-0442', '2025-08-20', 'ALDI SUED', 'Tagesbedarf', 11.81, 'Ausgabe', 1, 'ALDI SUED'),
(444, 0, b'1', 'RE202521-0444', '2025-08-21', 'Essen', 'Mc Donalds', 10.45, 'Ausgabe', 10, 'Essen'),
(445, 0, b'1', 'RE202521-0445', '2025-08-01', 'Einlage', 'Kasse Einlage', 350.00, 'Einlage', 10, 'Einlage'),
(446, 0, b'1', 'RE202521-0446', '2025-08-21', 'LIDL', 'Schokolade', 5.00, 'Ausgabe', 10, 'LIDL'),
(448, 1, b'1', 'RE202521-0448', '2025-08-21', 'ALDI SUED', 'Tagesbedarf', 9.73, 'Ausgabe', 1, 'ALDI SUED'),
(449, 1, b'1', 'RE202521-0449', '2025-07-26', 'Essen', 'Famlienzentrum Mettingen', 2.00, 'Ausgabe', 1, 'Essen'),
(451, 1, b'1', 'RE202521-0451', '2025-08-22', 'LIDL', 'Tagesbedarf', 15.58, 'Ausgabe', 1, 'LIDL'),
(452, 1, b'1', 'RE202521-0452', '2025-08-24', 'Essen', 'Cafe Flohmarkt', 7.60, 'Ausgabe', 1, 'Essen'),
(453, 1, b'1', 'RE202521-0453', '2025-08-24', 'Flohmarkt', 'Flohmarkt Starkholzburg Teleon, Buch, Pflanze', 11.00, 'Ausgabe', 1, 'Flohmarkt'),
(454, 1, b'1', 'RE202521-0454', '2025-08-23', 'Eintritte', 'Spohn Tennis 1 Stunde', 5.00, 'Ausgabe', 1, 'Eintritte'),
(455, 1, b'1', 'RE202521-0455', '2025-08-23', 'LIDL', 'Tagesbedarf', 15.28, 'Ausgabe', 1, 'LIDL'),
(456, 1, b'1', 'RE202521-0456', '2025-08-23', 'CAP Markt ', 'Tagesbedarf', 8.08, 'Ausgabe', 1, 'CAP Markt '),
(457, 1, b'1', 'RE202521-0457', '2025-08-25', 'ALDI SUED', 'Tagesbedarf', 11.87, 'Ausgabe', 1, 'ALDI SUED'),
(458, 1, b'1', 'RE202521-0458', '2025-08-26', 'CAP Markt ', 'Tagesbedarf', 3.12, 'Ausgabe', 1, 'CAP Markt '),
(459, 1, b'1', 'RE202521-0459', '2025-08-27', 'ALDI SUED', 'Tagesbedarf', 6.25, 'Ausgabe', 1, 'ALDI SUED'),
(460, 1, b'1', 'RE202521-0460', '2025-08-27', 'Essen', 'Sindelfingen Türke Chicken Burger vegetarisch', 4.00, 'Ausgabe', 1, 'Essen'),
(461, 1, b'1', 'RE202521-0461', '2025-08-28', 'ALDI SUED', 'Tagesbedarf', 13.45, 'Ausgabe', 1, 'ALDI SUED'),
(462, 1, b'1', 'RE202521-0462', '2025-08-30', 'Essen', 'Wöhrwag Rostbraten mit Kraut', 25.00, 'Ausgabe', 1, 'Essen'),
(463, 1, b'1', 'RE202521-0463', '2025-08-30', 'ALDI SUED', 'Tagesbedarf', 12.08, 'Ausgabe', 1, 'ALDI SUED'),
(464, 1, b'1', 'RE202521-0464', '2025-08-31', 'Essen', 'Inder Bad Cannstatt Schlemmerblock', 27.00, 'Ausgabe', 1, 'Essen'),
(465, 1, b'1', 'RE202521-0465', '2025-08-31', 'Sammelbuchung Supermarkt', 'Tagesbedarfe August 2025', 95.00, 'Ausgabe', 1, 'Sammelbuchung Supermarkt'),
(466, 1, b'1', 'RE202521-0466', '2025-09-01', 'Einlage', 'Kasse Einlage September 2025', 400.00, 'Einlage', 1, 'Einlage'),
(467, 1, b'1', 'RE202521-0467', '2025-09-03', 'ALDI SUED', 'Tagesbedarf', 6.20, 'Ausgabe', 1, 'ALDI SUED'),
(468, 1, b'1', 'RE202521-0468', '2025-09-02', 'ALDI SUED', 'Tagesbedarf', 11.20, 'Ausgabe', 1, 'ALDI SUED'),
(469, 1, b'1', 'RE202521-0469', '2025-09-03', 'CAP Markt ', 'Tagesbedarf', 5.24, 'Ausgabe', 1, 'CAP Markt '),
(470, 1, b'1', 'RE202521-0470', '2025-09-04', 'LIDL', 'Tagesbedarf', 11.64, 'Ausgabe', 1, 'LIDL'),
(471, 2, b'1', 'RE202521-0471', '2025-09-05', 'Einlage', 'Kasse Einlage Sepetmber 2025', 400.00, 'Einlage', 1, 'Einlage'),
(472, 2, b'1', 'RE202521-0472', '2025-09-05', 'ALDI SUED', 'Schnellkochtopf', 49.99, 'Ausgabe', 1, 'ALDI SUED'),
(473, 2, b'1', 'RE202521-0473', '2025-09-05', 'REWE', 'Tagesbedarf', 10.00, 'Ausgabe', 1, 'REWE'),
(474, 2, b'1', 'RE202521-0474', '2025-09-05', 'Eintritte', 'Spohn Tennis 1 Stunde', 16.00, 'Ausgabe', 1, 'Eintritte'),
(476, 1, b'1', 'RE202521-0476', '2025-09-05', 'Essen', 'Inder Steinstr. Schlemmerblock Peter Neugebauer', 17.00, 'Ausgabe', 1, 'Essen'),
(478, 1, b'1', 'RE202521-0478', '2025-09-06', 'CAP Markt ', 'Tagesbedarf', 3.26, 'Ausgabe', 1, 'CAP Markt '),
(479, 1, b'1', 'RE202521-0479', '2025-09-06', 'Flohmarkt', 'Winnenden Handy, 2 * DVD, Pflanze', 14.00, 'Ausgabe', 1, 'Flohmarkt'),
(480, 1, b'1', 'RE202521-0480', '2025-09-06', 'LIDL', 'Tagesbedarf Grillgut Besuch Weiß', 26.13, 'Ausgabe', 1, 'LIDL');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buchungsarten`
--

CREATE TABLE `buchungsarten` (
  `id` int(11) NOT NULL,
  `kassennummer` int(11) NOT NULL,
  `Dauerbuchung` bit(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `userid` int(11) NOT NULL,
  `buchungsart` varchar(255) NOT NULL,
  `mwst` float NOT NULL,
  `mwst_ermaessigt` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `buchungsarten`
--

INSERT INTO `buchungsarten` (`id`, `kassennummer`, `Dauerbuchung`, `created_at`, `updated_at`, `userid`, `buchungsart`, `mwst`, `mwst_ermaessigt`) VALUES
(17, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 09:50:16', 1, 'PayPal', 1.19, b'0'),
(18, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 09:50:16', 1, 'Tankstelle', 1.19, b'0'),
(24, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 19:30:59', 1, 'Essen', 1.07, b'1'),
(27, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 09:50:16', 1, 'CAP Markt ', 1.07, b'1'),
(29, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 09:50:16', 1, 'LIDL', 1.07, b'1'),
(35, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 09:50:16', 1, 'Badeeintritt', 1.19, b'0'),
(37, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 09:50:16', 1, 'Lindt Shop', 1.19, b'0'),
(44, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 09:50:16', 1, 'Kosmetikartikel', 1.19, b'0'),
(51, 0, b'0', '2025-04-25 20:56:45', '2025-09-04 08:38:32', 2, 'Restaurant', 1.19, b'0'),
(52, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 09:50:16', 1, 'Drogerieartikel', 1.19, b'0'),
(54, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 09:50:16', 1, 'EDEKA', 1.07, b'1'),
(56, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 09:50:16', 1, 'ALDI SUED', 1.07, b'1'),
(61, 1, b'0', '2025-04-25 20:56:45', '2025-09-05 09:50:16', 1, 'Eintritte', 1.19, b'0'),
(65, 1, b'0', '2025-05-14 09:08:58', '2025-09-05 09:50:16', 1, '_Diverses', 1.19, b'0'),
(66, 1, b'0', '2025-06-04 07:10:21', '2025-09-05 19:56:43', 1, 'Sammelbuchung Supermarkt', 1.07, b'1'),
(69, 1, b'0', '2025-06-04 07:55:26', '2025-09-05 09:50:16', 1, 'Flohmarkt', 1.19, b'0'),
(72, 0, b'0', '2025-06-11 12:23:39', '2025-09-04 08:38:32', 2, 'ALDI SUED ', 1.19, b'0'),
(73, 0, b'0', '2025-06-11 12:24:49', '2025-09-04 08:38:32', 2, 'Essen', 1.19, b'0'),
(74, 0, b'0', '2025-06-11 12:26:02', '2025-09-04 08:38:32', 2, 'Apotheke ', 1.19, b'0'),
(75, 1, b'1', '2025-07-31 09:48:37', '2025-09-05 09:50:16', 1, 'Einlage', 1.19, b'0'),
(83, 1, b'0', '2025-08-21 08:35:47', '2025-09-05 09:50:16', 1, 'REWE', 1.07, b'1'),
(98, 0, b'1', '2025-08-20 22:00:00', '2025-09-04 08:38:32', 10, 'Essen', 1.19, b'0'),
(99, 0, b'1', '2025-08-20 22:00:00', '2025-09-04 08:38:32', 10, 'LIDL', 1.19, b'0'),
(100, 0, b'1', '2025-08-20 22:00:00', '2025-09-04 08:38:32', 10, 'ALDI SUED', 1.19, b'0'),
(101, 0, b'1', '2025-08-20 22:00:00', '2025-09-04 08:38:32', 10, 'Einlage', 1.19, b'0'),
(102, 0, b'0', '2025-08-20 22:00:00', '2025-09-04 08:38:32', 10, 'Tierfutter', 1.19, b'0'),
(132, 1, b'1', '2025-09-04 22:00:00', '2025-09-04 22:00:00', 1, 'Grieb', 1, b'1'),
(133, 1, b'1', '2025-09-05 22:00:00', '2025-09-05 22:00:00', 1, 'Baumarkt', 0, b'1');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buchungsart_mapping`
--

CREATE TABLE `buchungsart_mapping` (
  `id` int(11) NOT NULL,
  `buchungsart` varchar(100) NOT NULL,
  `kontenrahmen` varchar(10) NOT NULL,
  `konto` varchar(10) NOT NULL,
  `gegenkonto` varchar(10) NOT NULL,
  `bu_schluessel` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `buchungsart_mapping`
--

INSERT INTO `buchungsart_mapping` (`id`, `buchungsart`, `kontenrahmen`, `konto`, `gegenkonto`, `bu_schluessel`) VALUES
(1, 'Wareneinkauf19', 'SKR03', '1000', '3400', '8'),
(2, 'Wareneinkauf7', 'SKR03', '1000', '3300', '9'),
(3, 'Erlöse19', 'SKR03', '1000', '8400', '81'),
(4, 'Erlöse7', 'SKR03', '1000', '8300', '86'),
(5, 'Bewirtung', 'SKR03', '1000', '4650', '8'),
(6, 'Bürobedarf', 'SKR03', '1000', '4930', '8'),
(7, 'Privatentnahme', 'SKR03', '1000', '1800', NULL),
(8, 'Wareneinkauf19', 'SKR04', '1600', '5400', '8'),
(9, 'Wareneinkauf7', 'SKR04', '1600', '5300', '9'),
(10, 'Erlöse19', 'SKR04', '1600', '4400', '81'),
(11, 'Erlöse7', 'SKR04', '1600', '4300', '86'),
(12, 'Bewirtung', 'SKR04', '1600', '6640', '8'),
(13, 'Bürobedarf', 'SKR04', '1600', '6815', '8'),
(14, 'Privatentnahme', 'SKR04', '1600', '2100', NULL),
(15, 'CAP Markt ', 'SKR03', '1000', '3300', '8'),
(16, 'ALDI SUED', 'SKR03', '1000', '3300', '8'),
(17, 'LIDL', 'SKR03', '1000', '3300', '8'),
(18, 'Badeeintritt', 'SKR03', '1000', '3400', '8'),
(19, 'REWE', 'SKR03', '1000', '3300', '8'),
(20, 'Flohmarkt', 'SKR03', '1000', '4980', '8'),
(21, 'Eintritte', 'SKR03', '1000', '4980', '8'),
(22, 'Baumarkt', 'SKR03', '1000', '4980', '8'),
(23, 'Essen', 'SKR03', '1000', '4650', '8'),
(24, 'Einlage', 'SKR03', '1000', '1890', '8'),
(25, 'Sammelbuchung Supermarkt', 'SKR03', '1000', '3300', '8'),
(26, 'Einlage Urlaub', 'SKR03', '1000', '1890', '8');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kasse`
--

CREATE TABLE `kasse` (
  `id` int(11) NOT NULL,
  `kasse` text NOT NULL,
  `anfangsbestand` float NOT NULL,
  `kontonummer` int(8) NOT NULL,
  `datumab` date NOT NULL,
  `checkminus` bit(1) NOT NULL,
  `userid` int(11) NOT NULL,
  `archiviert` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `kasse`
--

INSERT INTO `kasse` (`id`, `kasse`, `anfangsbestand`, `kontonummer`, `datumab`, `checkminus`, `userid`, `archiviert`) VALUES
(1, 'Kasse Gerhard Wißt', 0, 1600, '2024-12-01', b'0', 1, b'0'),
(2, 'Kasse Gerhard Wißt2', 50, 1602, '2025-01-01', b'0', 1, b'0'),
(3, 'Kasse Gerhard Wißt 3', 587, 1603, '2025-07-01', b'1', 1, b'1');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `twofactor_secret` varchar(64) DEFAULT NULL,
  `freigeschaltet` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `email`, `passwort`, `vorname`, `nachname`, `created_at`, `updated_at`, `twofactor_secret`, `freigeschaltet`) VALUES
(1, 'g.wisst@web.de', '$2y$10$cs05zWzGCRIhxRmKyyMabuUuIweqoEC.Lak0XL068ONuKLMAyHAmW', '', '', '2025-04-25 07:57:52', '2025-09-04 08:34:53', NULL, b'1'),
(10, 'anjalangjahr@web.de', '$2y$10$gbh3jiyKIQT3CeoCKenVDu44huE6l10rh1xCP1VvUP.Qc2SfwdS5m', '', '', '2025-08-21 10:40:55', '2025-09-04 08:34:53', NULL, b'1'),
(15, 'monterosa2@web.de', '$2y$10$owR3b9Nja861oUtTo2feGuoe7NUBY/PdqpsZQac5rgM2er/SXtmOi', '', '', '2025-08-21 18:00:46', '2025-09-04 08:34:53', '47TJA6WGYSWHIM7TKU2I73U3OXA5TRGF', b'1');

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
  ADD UNIQUE KEY `Buchungsart` (`buchungsart`,`userid`) USING BTREE;

--
-- Indizes für die Tabelle `buchungsart_mapping`
--
ALTER TABLE `buchungsart_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `kasse`
--
ALTER TABLE `kasse`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `bestaende`
--
ALTER TABLE `bestaende`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT für Tabelle `buchungen`
--
ALTER TABLE `buchungen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=481;

--
-- AUTO_INCREMENT für Tabelle `buchungsarten`
--
ALTER TABLE `buchungsarten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT für Tabelle `buchungsart_mapping`
--
ALTER TABLE `buchungsart_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT für Tabelle `kasse`
--
ALTER TABLE `kasse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
