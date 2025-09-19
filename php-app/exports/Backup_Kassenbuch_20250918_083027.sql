-- Datenbank Kassenbuch --
-- Backup vom 2025-09-18 08:30:27

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mandantennummer` int(11) NOT NULL DEFAULT 1,
  `email` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `vorname` varchar(255) NOT NULL DEFAULT '',
  `strasse` text NOT NULL,
  `plz` text NOT NULL,
  `ort` text NOT NULL,
  `nachname` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `twofactor_secret` varchar(64) DEFAULT NULL,
  `freigeschaltet` bit(1) NOT NULL,
  `is_admin` bit(1) NOT NULL,
  `gesperrt` bit(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`id`,`mandantennummer`,`email`,`passwort`,`vorname`,`strasse`,`plz`,`ort`,`nachname`,`created_at`,`updated_at`,`twofactor_secret`,`freigeschaltet`,`is_admin`,`gesperrt`) VALUES ('1','1','g.wisst@web.de','$2y$10$cs05zWzGCRIhxRmKyyMabuUuIweqoEC.Lak0XL068ONuKLMAyHAmW','Gerhard','Augsburger Str. 717','70329','Stuttgart','Wißt','2025-04-25 09:57:52','2025-09-16 12:45:31',NULL,'1','1','1');
INSERT INTO `users` (`id`,`mandantennummer`,`email`,`passwort`,`vorname`,`strasse`,`plz`,`ort`,`nachname`,`created_at`,`updated_at`,`twofactor_secret`,`freigeschaltet`,`is_admin`,`gesperrt`) VALUES ('32','1','anjalangjahr@web.de','$2y$10$1gQ3wf9f6ayVzfPLbu.0Du6.kdjgBC.HH8qI7xGD6ayQowUko0cU6','Anja','Augsburger Str. 717','70329','Stuttgart','Langjahr','2025-09-17 09:42:15','2025-09-17 09:42:15','YBX3TA3RNK6DL5QHJC2WAAIPC3IY66OU','1','0','0');
INSERT INTO `users` (`id`,`mandantennummer`,`email`,`passwort`,`vorname`,`strasse`,`plz`,`ort`,`nachname`,`created_at`,`updated_at`,`twofactor_secret`,`freigeschaltet`,`is_admin`,`gesperrt`) VALUES ('35','1','tester@web.de','$2y$10$j6ee4i6Fuf/INXRudpNLwO32BnZuj09HRdbu2Rr/fhLYnl5mlErn.','Bernhard','Heimgartenstr. 61','70329','Stuttgart','Richter','2025-09-17 15:42:43','2025-09-17 15:42:43','XBOSG3W7NJIWLMPVDKNRIX23FH6M3IGD','1','0','0');


DROP TABLE IF EXISTS `kasse`;
CREATE TABLE `kasse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kasse` text NOT NULL,
  `typ` text DEFAULT NULL,
  `anfangsbestand` float NOT NULL,
  `kontonummer` int(8) NOT NULL,
  `datumab` date NOT NULL,
  `checkminus` bit(1) NOT NULL,
  `userid` int(11) NOT NULL,
  `mandantennummer` int(11) DEFAULT NULL,
  `archiviert` bit(1) DEFAULT NULL,
  `vorname` text DEFAULT NULL,
  `nachname` text DEFAULT NULL,
  `firma` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kasse_userid` (`userid`),
  CONSTRAINT `fk_kasse_user` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_kasse_userid` FOREIGN KEY (`userid`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `kasse` (`id`,`kasse`,`typ`,`anfangsbestand`,`kontonummer`,`datumab`,`checkminus`,`userid`,`mandantennummer`,`archiviert`,`vorname`,`nachname`,`firma`) VALUES ('1','Kasse Gerhard Wißt','privat','0','1000','2024-12-01','1','1','1','0','Gerhard','Wißt',NULL);
INSERT INTO `kasse` (`id`,`kasse`,`typ`,`anfangsbestand`,`kontonummer`,`datumab`,`checkminus`,`userid`,`mandantennummer`,`archiviert`,`vorname`,`nachname`,`firma`) VALUES ('66','Kasse Anja','privat','48','1000','2025-09-17','1','32','1','0','Anja','Langjahr',NULL);
INSERT INTO `kasse` (`id`,`kasse`,`typ`,`anfangsbestand`,`kontonummer`,`datumab`,`checkminus`,`userid`,`mandantennummer`,`archiviert`,`vorname`,`nachname`,`firma`) VALUES ('71','Kasse Bernie','privat','99','1000','2025-08-01','1','35','1','0','Bernhard','Richter',NULL);


DROP TABLE IF EXISTS `bestaende`;
CREATE TABLE `bestaende` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kassennummer` int(151) NOT NULL,
  `datum` date NOT NULL,
  `monat` int(11) DEFAULT NULL,
  `ausgaben` decimal(10,2) DEFAULT NULL,
  `einlagen` decimal(10,2) DEFAULT NULL,
  `bestand` decimal(10,2) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `mandantennummer` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_bestaende_user` (`userid`),
  KEY `fk_bestaende_kasse` (`kassennummer`,`mandantennummer`) USING BTREE,
  CONSTRAINT `fk_bestaende_kasse` FOREIGN KEY (`kassennummer`) REFERENCES `kasse` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bestaende_user` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=375 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('50','1','2025-01-01','1','400.00','400.00','0.00','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('51','1','2025-02-01','2','400.00','400.00','0.00','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('52','1','2025-03-01','3','400.00','400.00','0.00','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('53','1','2025-04-01','4','400.00','400.00','0.00','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('54','1','2025-05-01','5','569.90','600.00','30.10','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('55','1','2025-06-01','6','378.12','400.00','51.98','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('56','1','2025-07-01','7','404.34','400.00','47.64','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('57','1','2025-08-01','8','445.01','400.00','2.63','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('58','1','2025-09-01','9','210.16','400.00','192.47','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('59','1','2025-10-01','10','0.00','0.00','192.47','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('60','1','2025-11-01','11','0.00','0.00','192.47','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('61','1','2025-12-01','12','0.00','0.00','192.47','1','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('346','66','2025-09-01','9','0.00','0.00','48.00','32','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('347','66','2025-10-01','10','0.00','0.00','48.00','32','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('348','66','2025-11-01','11','0.00','0.00','48.00','32','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('349','66','2025-12-01','12','0.00','0.00','48.00','32','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('370','71','2025-08-01','8','0.00','0.00','99.00','35','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('371','71','2025-09-01','9','5.00','0.00','94.00','35','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('372','71','2025-10-01','10','0.00','0.00','94.00','35','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('373','71','2025-11-01','11','0.00','0.00','94.00','35','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`,`mandantennummer`) VALUES ('374','71','2025-12-01','12','0.00','0.00','94.00','35','1');


DROP TABLE IF EXISTS `buchungen`;
CREATE TABLE `buchungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kassennummer` int(11) NOT NULL,
  `barkasse` bit(1) DEFAULT NULL,
  `belegnr` longtext DEFAULT NULL,
  `datum` date NOT NULL,
  `vonan` longtext NOT NULL,
  `beschreibung` longtext NOT NULL,
  `betrag` decimal(10,2) NOT NULL,
  `typ` enum('Einlage','Ausgabe') NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `mandantennummer` int(11) NOT NULL,
  `buchungsart` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_barkasse_user` (`kassennummer`,`mandantennummer`) USING BTREE,
  KEY `fk_buchungen_userid` (`userid`,`mandantennummer`) USING BTREE,
  CONSTRAINT `fk_barkasse_user` FOREIGN KEY (`kassennummer`) REFERENCES `kasse` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_buchungen_userid` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=512 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('1','1','1','RE202521-0001','2025-04-24','Einkauf Aldi','Tagesbedarf','10.59','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('2','1','1','RE202521-0002','2025-07-30','ALDI SUED','Tagesbedarf','26.51','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('9','1','1','RE202521-0009','2025-04-23','Einkauf Aldi','Tagesbedarf','13.00','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('244','1','1','RE202521-244','2025-04-28','Flohmarkt',' Plochingen CDs und DVDs','2.56','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('245','1','1','RE202521-245','2025-04-27','Flohmarkt',' Plochingen Verlängerungskabel','4.00','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('247','1','1','RE202521-0247','2025-04-27','Flohmarkt',' Plochingen Socken','10.00','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('248','1','1','RE202521-0248','2025-04-29','ALDI SUED','Tagesbedarf','5.67','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('249','1','1','RE202521-0249','2025-04-30','Einkauf Aldi','Tagesbedarf','14.25','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('250','1','1','RE202521-0250','2025-04-30','Eintritte','Spohn 1 Stunde Tennis','10.00','Ausgabe','1','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('251','1','1','RE202521-0251','2025-04-17','LIDL','Tagesbedarf','26.57','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('252','1','1','RE202521-0252','2025-04-21','Flohmarkt','Süßen Verschiedenes','14.00','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('253','1','1','RE202521-0253','2025-03-29','Eintritte','Spohn Tennis 1 Stunde','10.00','Ausgabe','1','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('255','1','1','RE202521-0255','2025-01-31','Einkäufe','Tagesbedarf','400.00','Ausgabe','1','1','Einkäufe');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('256','1','1','RE202521-0256','2025-02-28','Sammelbuchung Supermarkt','Tagesbedarfe Februar','400.00','Ausgabe','1','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('257','1','1','RE202521-0257','2025-03-31','Sammelbuchung Supermarkt','Tagesbedarfe März','390.00','Ausgabe','1','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('258','1','1','RE202521-0258','2025-05-01','Essen','Inder Geburtstag Restaurant Ganseha','62.50','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('260','1','1','RE202521-0260','2025-05-02','Einkauf Aldi','Tagesbedarf','12.51','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('261','1','1','RE202521-0261','2025-05-02','Einlage','Kasse Einlage','400.00','Einlage','1','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('262','1','1','RE202521-0262','2025-05-05','CAP Markt ','Tagesbedarf','1.74','Ausgabe','1','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('263','1','1','RE202521-0263','2025-05-04','Flohmarkt','Fellbach kleine Heizung, Insektenfänger, DVD','11.00','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('264','1','1','RE202521-0264','2025-01-01','Einlage','Kasse Einlage','400.00','Einlage','1','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('265','1','1','RE202521-0265','2025-02-01','Einlage','Kasse Einlage','400.00','Einlage','1','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('266','1','1','RE202521-0266','2025-03-01','Einlage','Kasse Einlage','400.00','Einlage','1','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('267','1','1','RE202521-0267','2025-04-01','Einlage','Kasse Einlage','400.00','Einlage','1','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('268','1','1','RE202521-0268','2025-04-30','Sammelbuchung Supermarkt','Tagesbedarfe April','289.36','Ausgabe','1','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('270','1','1','RE202521-0270','2025-05-06','Essen','Essen','33.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('272','1','1','RE202521-0272','2025-05-06','Essen','2 Latte Macciato','10.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('274','1','1','RE202521-0274','2025-05-10','LIDL','Tagesbedarf','19.51','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('275','1','1','RE202521-0275','2025-05-09','Essen','Restaurant Ganseha Langenargen','34.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('276','1','1','RE202521-0276','2025-05-08','2 Fahrräder','Leihe','20.00','Ausgabe','1','1','2 Fahrräder');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('277','1','1','RE202521-0277','2025-05-06','Einlage Urlaub','Einlage Urlaub','200.00','Einlage','1','1','Einlage Urlaub');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('278','1','1','RE202521-0278','2025-05-08','Essen','Restaurant Nordsee Lindau','30.40','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('279','1','1','RE202521-0279','2025-05-07','Essen','Restaurant Vietnamese Meersburg','26.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('280','1','1','RE202521-0280','2025-05-09','CAP-Markt','Tagesbedarf','3.50','Ausgabe','1','1','CAP Markt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('282','1','1','RE202521-0282','2025-05-23','LIDL','Tagesbedarf','11.56','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('283','1','1','RE202521-0283','2025-05-22','ALDI SUED','Tagesbedarf','8.54','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('284','1','1','RE202521-0284','2025-05-20','Drogeriemarkt','MH Muller Handels GmbH Schampoo und Duschgel','5.40','Ausgabe','1','1','Drogeriemarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('285','1','1','RE202521-0285','2025-05-20','Essen','Bärenschlössle Essen','14.80','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('286','1','1','RE202521-0286','2025-05-18','Flohmarkt','Darts Scheibe und Kleinigkeiten','10.00','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('287','1','1','RE202521-0287','2025-05-19','ALDI SUED','Tagesbedarf','12.41','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('288','1','1','RE202521-0288','2025-05-16','LIDL','Tagesbedarf','15.41','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('289','1','1','RE202521-0289','2025-05-14','LIDL','Tagesbedarf','13.57','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('290','1','1','RE202521-0290','2025-05-21','LIDL','Tagesbedarf','9.74','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('291','1','1','RE202521-0291','2025-05-12','Essen','Ajran und Getränk','5.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('292','1','1','RE202521-0292','2025-05-10','LIDL','Tagesbedarf','18.54','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('293','1','1','RE202521-0293','2025-05-26','ALDI SUED','Tagesbedarf','13.77','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('294','1','1','RE202521-0294','2025-05-31','Sammelbuchung Supermarkt','Tagesbedarfe Mai','160.00','Ausgabe','1','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('296','1','1','RE202521-0296','2025-06-03','CAP Markt ','Tagesbedarf','2.51','Ausgabe','1','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('297','1','1','RE202521-0297','2025-06-01','Einlage','Bareinlage Mai','400.00','Einlage','1','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('298','1','1','RE202521-0298','2025-06-03','LIDL','Tagesbedarf','9.91','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('299','1','1','RE202521-299','2025-05-24','Eintritte','Spohn Tennis 1 Stunde','5.00','Ausgabe','1','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('300','1','1','RE202521-0300','2025-05-31','Essen','Famlienzentrum Mettingen','2.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('301','1','1','RE202521-0301','2025-06-04','ALDI SUED','Tagesbedarf','5.01','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('303','1','1','RE202521-0303','2025-06-05','CAP Markt ','Tagesbedarf','6.61','Ausgabe','1','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('304','1','1','RE202521-0304','2025-06-05','Essen','Döner Obertürkheim','8.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('399','1','1','RE202521-0288','2025-07-01','Einlage','Kasse Einlage','400.00','Einlage','1','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('400','1','1','RE202521-0400','2025-07-29','CAP Markt ','Tagesbedarf','6.64','Ausgabe','1','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('401','1','1','RE202521-0401','2025-07-18','Eintritte','Inselbad','5.00','Ausgabe','1','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('402','1','1','RE202521-0402','2025-07-19','LIDL','Tagesbedarf','22.54','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('403','1','1','RE202521-0403','2025-07-11','Eintritte','Inselbad','5.00','Ausgabe','1','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('404','1','1','RE202521-0404','2025-07-24','Eintritte','Leuze','21.00','Ausgabe','1','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('405','1','1','RE202521-0405','2025-07-27','Essen','Deizisau Schnitzel, Pommes, Currywurst','23.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('406','1','1','RE202521-0406','2025-07-29','ALDI SUED','Tagesbedarf','6.60','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('407','1','1','RE202521-0407','2025-07-28','ALDI SUED','Tagesbedarf','11.84','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('408','1','1','RE202521-0408','2025-07-19','Flohmarkt','Kaltental','2.00','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('409','1','1','RE202521-0409','2025-06-29','Eintritte','Bad Öhningen','6.00','Ausgabe','1','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('410','1','1','RE202521-0410','2025-06-29','Essen','Schnitzel und Currywurst Bad','23.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('411','1','1','RE202521-0411','2025-06-15','ALDI SUED','Tagesbedarf','18.54','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('412','1','1','RE202521-0412','2025-06-30','ALDI SUED','Tagesbedarf','18.54','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('413','1','1','RE202521-0413','2025-06-30','Sammelbuchung Supermarkt','Tagesbedarfe Juli 2025','280.00','Ausgabe','1','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('414','1','1','RE202521-0414','2025-07-12','LIDL','Tagesbedarf','17.84','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('415','1','1','RE202521-0415','2025-07-05','ALDI SUED','Tagesbedarf','15.78','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('416','1','1','RE202521-0416','2025-07-31','ALDI SUED','Tagesbedarf','9.18','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('417','1','1','RE202521-0417','2025-07-07','ALDI SUED','Tagesbedarf','12.54','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('418','1','1','RE202521-0418','2025-07-09','LIDL','Tagesbedarf','16.87','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('419','1','1','RE202521-0419','2025-07-31','Sammelbuchung Supermarkt','Tagesbedarf','200.00','Ausgabe','1','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('420','1','1','RE202521-0420','2025-08-11','ALDI SUED','Tagesbedarf','10.02','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('421','1','1','RE202521-0421','2025-08-01','Einlage','Kasse Einlage August 2025','400.00','Einlage','1','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('422','1','1','RE202521-0422','2025-08-11','Essen','Bäcker 2 Schnitten mit Pistazien','4.60','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('423','1','1','RE202521-0423','2025-08-11','REWE','Tagesbedarf Rewe','3.50','Ausgabe','1','1','REWE');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('424','1','1','RE202521-0424','2025-08-09','LIDL','Tagesbedarf','13.41','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('425','1','1','RE202521-0425','2025-08-05','Baumarkt','Pinsel Bauhaus','7.99','Ausgabe','1','1','Baumarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('426','1','1','RE202521-0426','2025-08-11','ALDI SUED','Tagesbedarf','11.45','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('427','1','1','RE202521-0427','2025-08-08','LIDL','Tagesbedarf','13.40','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('428','1','1','RE202521-0428','2025-08-10','Badeeintritt','2 x Deizisau','9.80','Ausgabe','1','1','Badeeintritt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('429','1','1','RE202521-0429','2025-08-10','Essen','Pommes Frittes Bad Deizisau','4.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('430','1','1','RE202521-0430','2025-08-02','ALDI SUED','Tagesbedarf','16.85','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('431','1','1','RE202521-0431','2025-08-13','Essen','Sindelfingen Türke Falafel und Getränk','8.40','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('432','1','1','RE202521-0432','2025-08-13','CAP Markt ','Tagesbedarf','5.05','Ausgabe','1','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('435','1','1','RE202521-0435','2025-08-14','ALDI SUED','Tagesbedarf','13.08','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('436','1','1','RE202521-0436','2025-08-16','Flohmarkt','Flohmarkt Winnenden Tastatur, Kaktus, DVD, 2 CD','10.00','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('437','1','1','RE202521-0437','2025-08-16','LIDL','Tagesbedarf','13.41','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('438','1','1','RE202521-0438','2025-08-18','Essen','Fellbach Bäckerei','4.45','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('439','1','1','RE202521-0439','2025-08-19','CAP Markt ','Tagesbedarf','6.68','Ausgabe','1','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('440','1','1','RE202521-0440','2025-08-19','ALDI SUED','Tagesbedarf','7.07','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('441','1','1','RE202521-0441','2024-12-31','Essen','Silvestermenü','75.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('442','1','1','RE202521-0442','2025-08-20','ALDI SUED','Tagesbedarf','11.81','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('448','1','1','RE202521-0448','2025-08-21','ALDI SUED','Tagesbedarf','9.73','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('449','1','1','RE202521-0449','2025-07-26','Essen','Famlienzentrum Mettingen','2.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('451','1','1','RE202521-0451','2025-08-22','LIDL','Tagesbedarf','15.58','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('452','1','1','RE202521-0452','2025-08-24','Essen','Cafe Flohmarkt','7.60','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('453','1','1','RE202521-0453','2025-08-24','Flohmarkt','Flohmarkt Starkholzburg Teleon, Buch, Pflanze','11.00','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('454','1','1','RE202521-0454','2025-08-23','Eintritte','Spohn Tennis 1 Stunde','5.00','Ausgabe','1','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('455','1','1','RE202521-0455','2025-08-23','LIDL','Tagesbedarf','15.28','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('456','1','1','RE202521-0456','2025-08-23','CAP Markt ','Tagesbedarf','8.08','Ausgabe','1','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('457','1','1','RE202521-0457','2025-08-25','ALDI SUED','Tagesbedarf','11.87','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('458','1','1','RE202521-0458','2025-08-26','CAP Markt ','Tagesbedarf','3.12','Ausgabe','1','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('459','1','1','RE202521-0459','2025-08-27','ALDI SUED','Tagesbedarf','6.25','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('460','1','1','RE202521-0460','2025-08-27','Essen','Sindelfingen Türke Chicken Burger vegetarisch','4.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('461','1','1','RE202521-0461','2025-08-28','ALDI SUED','Tagesbedarf','13.45','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('462','1','1','RE202521-0462','2025-08-30','Essen','Wöhrwag Rostbraten mit Kraut','25.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('463','1','1','RE202521-0463','2025-08-30','ALDI SUED','Tagesbedarf','12.08','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('464','1','1','RE202521-0464','2025-08-31','Essen','Inder Bad Cannstatt Schlemmerblock','27.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('465','1','1','RE202521-0465','2025-08-31','Sammelbuchung Supermarkt','Tagesbedarfe August 2025','95.00','Ausgabe','1','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('466','1','1','RE202521-0466','2025-09-01','Einlage','Kasse Einlage September 2025','400.00','Einlage','1','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('467','1','1','RE202521-0467','2025-09-03','ALDI SUED','Tagesbedarf','6.20','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('468','1','1','RE202521-0468','2025-09-02','ALDI SUED','Tagesbedarf','11.20','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('469','1','1','RE202521-0469','2025-09-03','CAP Markt ','Tagesbedarf','5.24','Ausgabe','1','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('470','1','1','RE202521-0470','2025-09-04','LIDL','Tagesbedarf','11.64','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('476','1','1','RE202521-0476','2025-09-05','Essen','Inder Steinstr. Schlemmerblock Peter Neugebauer','17.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('478','1','1','RE202521-0478','2025-09-06','CAP Markt ','Tagesbedarf','3.26','Ausgabe','1','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('479','1','1','RE202521-0479','2025-09-06','Flohmarkt','Winnenden Handy, DVD + VHS, Pflanze','14.00','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('480','1','1','RE202521-0480','2025-09-06','LIDL','Tagesbedarf Grillgut Besuch Weiß','26.13','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('485','1','1','RE202521-0485','2025-09-08','ALDI SUED','Tagesbedarf','14.64','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('486','1','1','RE202521-0486','2025-09-08','Einlage','Einlage Urlaub','0.00','Einlage','1','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('487','1','1','RE202521-0487','2025-09-09','LIDL','Tagesbedarf','9.21','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('488','1','1','RE202521-0488','2025-09-10','LIDL','Tagesbedarf','17.64','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('493','1','1','RE202521-0493','2025-09-11','LIDL','Tagesbedarf','15.32','Ausgabe','1','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('494','1','1','RE202521-0494','2025-09-12','Essen','Zwiebelkuchen','3.15','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('495','1','1','RE202521-0495','2025-09-11','_Diverses','Gesundheitsstrumpf','9.00','Ausgabe','1','1','_Diverses');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('496','1','1','RE202521-0496','2025-09-11','Essen','Kirschkuchen','3.00','Ausgabe','1','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('497','1','1','RE202521-0497','2025-09-13','Flohmarkt','Ei, Löffel, LED Fußball','7.50','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('498','1','1','RE202521-0498','2025-09-13','ALDI SUED','Tagesbedarf','5.08','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('500','1','1','RE202521-0500','2025-09-15','Autozubehör','Autoschlüssel Batterie','5.51','Ausgabe','1','1','Autozubehör');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('501','1','1','RE202521-0501','2025-09-15','Flohmarkt','3 Bücher Hospitalhof','2.00','Ausgabe','1','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('503','1','1','RE202521-0503','2025-09-15','ALDI SUED','Tagesbedarf','11.40','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('504','1','1','RE202521-0504','2025-09-16','ALDI SUED','Tagesbedarf','12.04','Ausgabe','1','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('510','71','1','RE202521-0510','2025-09-17','ALDI SUED','Bier','5.00','Ausgabe','35','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`mandantennummer`,`buchungsart`) VALUES ('511','1','1','RE202521-0511','2025-09-17','ALDI SUED','Tagesbedarf','8.64','Ausgabe','1','1','ALDI SUED');


DROP TABLE IF EXISTS `buchungsart_mapping`;
CREATE TABLE `buchungsart_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `buchungsart` varchar(100) NOT NULL,
  `kontenrahmen` varchar(10) NOT NULL,
  `konto` varchar(10) NOT NULL,
  `gegenkonto` varchar(10) NOT NULL,
  `bu_schluessel` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('1','Wareneinkauf19','SKR03','1000','3400','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('2','Wareneinkauf7','SKR03','1000','3300','9');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('3','Erlöse19','SKR03','1000','8400','81');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('4','Erlöse7','SKR03','1000','8300','86');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('5','Bewirtung','SKR03','1000','4650','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('6','Bürobedarf','SKR03','1000','4930','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('7','Privatentnahme','SKR03','1000','1800',NULL);
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('8','Wareneinkauf19','SKR04','1600','5400','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('9','Wareneinkauf7','SKR04','1600','5300','9');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('10','Erlöse19','SKR04','1600','4400','81');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('11','Erlöse7','SKR04','1600','4300','86');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('12','Bewirtung','SKR04','1600','6640','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('13','Bürobedarf','SKR04','1600','6815','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('14','Privatentnahme','SKR04','1600','2100',NULL);
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('15','CAP Markt ','SKR03','1000','3300','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('16','ALDI SUED','SKR03','1000','3300','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('17','LIDL','SKR03','1000','3300','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('18','Badeeintritt','SKR03','1000','3400','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('19','REWE','SKR03','1000','3300','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('20','Flohmarkt','SKR03','1000','4980','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('21','Eintritte','SKR03','1000','4980','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('22','Baumarkt','SKR03','1000','4980','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('23','Essen','SKR03','1000','4650','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('24','Einlage','SKR03','1000','1890','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('25','Sammelbuchung Supermarkt','SKR03','1000','3300','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('26','Einlage Urlaub','SKR03','1000','1890','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('27','CAP Markt ','SKR04','1000','3300','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('28','ALDI SUED','SKR04','1000','3300','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('29','LIDL','SKR04','1000','3300','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('30','Badeeintritt','SKR04','1000','3400','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('31','Baumarkt','SKR04','1000','4980','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('32','Essen','SKR04','1000','4650','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('33','Einlage','SKR04','1000','1890','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('34','Sammelbuchung Supermarkt','SKR04','1000','3300','8');
INSERT INTO `buchungsart_mapping` (`id`,`buchungsart`,`kontenrahmen`,`konto`,`gegenkonto`,`bu_schluessel`) VALUES ('35','Einlage Urlaub','SKR04','1000','1890','8');


DROP TABLE IF EXISTS `buchungsarten`;
CREATE TABLE `buchungsarten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kassennummer` int(11) NOT NULL,
  `Dauerbuchung` bit(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `userid` int(11) NOT NULL,
  `mandantennummer` int(11) NOT NULL,
  `buchungsart` varchar(255) NOT NULL,
  `mwst` float NOT NULL,
  `mwst_ermaessigt` bit(1) NOT NULL,
  `standard` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Buchungsart` (`buchungsart`,`userid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=356 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('17','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','PayPal','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('18','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','Tankstelle','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('24','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','Essen','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('27','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','CAP Markt ','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('29','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','LIDL','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('35','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','Badeeintritt','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('37','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','Lindt Shop','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('44','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','Kosmetikartikel','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('51','1','0','2025-04-25 22:56:45','2025-09-17 15:35:35','2','1','Restaurant','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('52','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','Drogerieartikel','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('54','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','EDEKA','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('56','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','ALDI SUED','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('61','1','0','2025-04-25 22:56:45','2025-09-15 14:31:32','1','1','Eintritte','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('65','1','0','2025-05-14 11:08:58','2025-09-15 14:31:32','1','1','_Diverses','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('66','1','0','2025-06-04 09:10:21','2025-09-15 14:31:32','1','1','Sammelbuchung Supermarkt','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('69','1','0','2025-06-04 09:55:26','2025-09-15 14:31:32','1','1','Flohmarkt','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('72','1','0','2025-06-11 14:23:39','2025-09-17 15:35:35','2','1','ALDI SUED ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('73','1','0','2025-06-11 14:24:49','2025-09-17 15:35:35','2','1','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('74','1','0','2025-06-11 14:26:02','2025-09-17 15:35:35','2','1','Apotheke ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('75','1','1','2025-07-31 11:48:37','2025-09-15 14:31:32','1','1','Einlage','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('83','1','0','2025-08-21 10:35:47','2025-09-15 14:31:32','1','1','REWE','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('98','1','1','2025-08-21 00:00:00','2025-09-17 15:35:35','10','1','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('99','1','1','2025-08-21 00:00:00','2025-09-17 15:35:35','10','1','LIDL','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('100','1','1','2025-08-21 00:00:00','2025-09-17 15:35:35','10','1','ALDI SUED','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('101','1','1','2025-08-21 00:00:00','2025-09-17 15:35:35','10','1','Einlage','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('102','1','0','2025-08-21 00:00:00','2025-09-17 15:35:35','10','1','Tierfutter','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('132','1','1','2025-09-05 00:00:00','2025-09-17 15:56:43','1','1','Grieb','1','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('133','1','1','2025-09-06 00:00:00','2025-09-15 14:31:32','1','1','Baumarkt','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('139','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','PayPal','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('140','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','Tankstelle','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('141','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','Essen','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('142','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','CAP Markt ','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('143','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','LIDL','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('144','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','Badeeintritt','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('145','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','Kosmetikartikel','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('146','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','Drogerieartikel','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('147','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','EDEKA','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('148','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','ALDI SUED','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('149','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','Eintritte','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('150','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','_Diverses','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('151','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','Sammelbuchung Supermarkt','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('152','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','Flohmarkt','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('153','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','Einlage','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('154','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','REWE','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('155','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','Grieb','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('156','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','20','1','Baumarkt','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('157','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','PayPal','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('158','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','Tankstelle','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('159','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('160','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','CAP Markt ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('161','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','LIDL','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('162','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','Badeeintritt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('163','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','Kosmetikartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('164','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','Drogerieartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('165','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','EDEKA','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('166','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','ALDI SUED','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('167','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','Eintritte','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('168','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','_Diverses','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('169','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','Sammelbuchung Supermarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('170','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','Flohmarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('171','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','Einlage','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('172','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','REWE','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('173','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','Grieb','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('174','1','0','2025-09-08 00:00:00','2025-09-17 15:35:35','21','1','Baumarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('175','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','PayPal','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('176','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','Tankstelle','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('177','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('178','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','CAP Markt ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('179','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','LIDL','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('180','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','Badeeintritt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('181','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','Kosmetikartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('182','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','Drogerieartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('183','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','EDEKA','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('184','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','ALDI SUED','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('185','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','Eintritte','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('186','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','_Diverses','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('187','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','Sammelbuchung Supermarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('188','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','Flohmarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('189','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','Einlage','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('190','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','REWE','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('191','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','Grieb','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('192','1','0','2025-09-11 00:00:00','2025-09-17 15:35:35','22','1','Baumarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('193','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','PayPal','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('194','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','Tankstelle','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('195','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('196','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','CAP Markt ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('197','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','LIDL','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('198','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','Badeeintritt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('199','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','Kosmetikartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('200','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','Drogerieartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('201','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','EDEKA','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('202','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','ALDI SUED','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('203','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','Eintritte','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('204','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','_Diverses','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('205','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','Sammelbuchung Supermarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('206','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','Flohmarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('207','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','Einlage','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('208','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','REWE','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('209','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','Grieb','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('210','1','0','2025-09-12 00:00:00','2025-09-17 15:35:35','23','1','Baumarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('211','1','1','2025-09-15 00:00:00','2025-09-15 14:31:32','1','1','Autozubehör','1.19','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('212','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','PayPal','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('213','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','Tankstelle','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('214','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('215','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','CAP Markt ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('216','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','LIDL','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('217','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','Badeeintritt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('218','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','Kosmetikartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('219','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','Drogerieartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('220','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','EDEKA','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('221','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','ALDI SUED','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('222','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','Eintritte','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('223','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','_Diverses','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('224','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','Sammelbuchung Supermarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('225','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','Flohmarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('226','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','Einlage','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('227','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','REWE','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('228','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','Grieb','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('229','1','0','2025-09-15 00:00:00','2025-09-17 15:35:35','24','0','Baumarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('230','1','0','2025-09-15 00:00:00','2025-09-15 17:02:03','1','1','Test1609','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('231','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','PayPal','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('232','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','Tankstelle','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('233','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('234','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','CAP Markt ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('235','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','LIDL','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('236','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','Badeeintritt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('237','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','Kosmetikartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('238','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','Drogerieartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('239','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','EDEKA','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('240','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','ALDI SUED','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('241','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','Eintritte','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('242','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','_Diverses','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('243','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','Sammelbuchung Supermarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('244','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','Flohmarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('245','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','Einlage','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('246','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','REWE','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('247','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','Grieb','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('248','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','25','0','Baumarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('249','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','PayPal','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('250','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','Tankstelle','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('251','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('252','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','CAP Markt ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('253','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','LIDL','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('254','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','Badeeintritt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('255','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','Kosmetikartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('256','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','Drogerieartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('257','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','EDEKA','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('258','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','ALDI SUED','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('259','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','Eintritte','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('260','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','_Diverses','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('261','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','Sammelbuchung Supermarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('262','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','Flohmarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('263','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','Einlage','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('264','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','REWE','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('265','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','Grieb','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('266','1','0','2025-09-16 00:00:00','2025-09-17 15:35:35','31','0','Baumarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('339','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','PayPal','1.19','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('340','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','Tankstelle','1.19','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('341','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','Essen','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('342','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','CAP Markt ','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('343','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','LIDL','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('344','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','Badeeintritt','1.19','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('345','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','Kosmetikartikel','1.19','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('346','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','Drogerieartikel','1.19','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('347','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','EDEKA','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('348','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','ALDI SUED','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('349','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','Eintritte','1.19','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('350','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','_Diverses','1.19','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('351','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','Sammelbuchung Supermarkt','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('352','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','Flohmarkt','1.19','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('353','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','Einlage','1.19','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('354','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','REWE','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`mandantennummer`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('355','71','1','2025-09-17 15:58:29','2025-09-17 15:58:29','35','1','Baumarkt','1.07','1','1');


DROP TABLE IF EXISTS `cash_files`;
CREATE TABLE `cash_files` (
  `id` int(11) NOT NULL,
  `kassennummer` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `FilePath` text NOT NULL,
  `uploadedat` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cash_files` (`id`,`kassennummer`,`userid`,`FilePath`,`uploadedat`) VALUES ('0','1','1','uploads/cashfiles/CashFile_1_Rewe 1  2025.pdf','2025-09-11 13:06:54');


DROP TABLE IF EXISTS `mandanten`;
CREATE TABLE `mandanten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typ` text NOT NULL,
  `kundennummer` int(11) NOT NULL,
  `firmenname` text NOT NULL,
  `vorname` text NOT NULL,
  `nachname` text NOT NULL,
  `strasse` text NOT NULL,
  `plz` text NOT NULL,
  `ort` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `mandanten` (`id`,`typ`,`kundennummer`,`firmenname`,`vorname`,`nachname`,`strasse`,`plz`,`ort`,`created_at`,`updated_at`) VALUES ('1','privat','1','','Gerhard','Wißt','Augsburger Str. 717','70329','Stuttgart','2025-09-12 12:26:18','2025-09-12 23:12:21');
INSERT INTO `mandanten` (`id`,`typ`,`kundennummer`,`firmenname`,`vorname`,`nachname`,`strasse`,`plz`,`ort`,`created_at`,`updated_at`) VALUES ('12','privat','23','','','Langjahr','Augsburger Straße. 717','70329','Stuttgart','2025-09-12 13:01:32','2025-09-16 16:22:23');
INSERT INTO `mandanten` (`id`,`typ`,`kundennummer`,`firmenname`,`vorname`,`nachname`,`strasse`,`plz`,`ort`,`created_at`,`updated_at`) VALUES ('20','privat','21','','','Langjahr','Augsburger Straße. 717','70328','Stuttgart','2025-09-12 13:19:23','2025-09-16 16:10:08');
INSERT INTO `mandanten` (`id`,`typ`,`kundennummer`,`firmenname`,`vorname`,`nachname`,`strasse`,`plz`,`ort`,`created_at`,`updated_at`) VALUES ('22','privat','4','','','Langjahr','Augsburger Straße. 717','70329','Stuttgart','2025-09-12 13:24:22','2025-09-15 16:49:45');
INSERT INTO `mandanten` (`id`,`typ`,`kundennummer`,`firmenname`,`vorname`,`nachname`,`strasse`,`plz`,`ort`,`created_at`,`updated_at`) VALUES ('25','privat','24','','','Langjahr-Islam','Augsburger Straße. 717','70329','Stuttgart','2025-09-12 13:27:33','2025-09-15 18:51:27');


SET FOREIGN_KEY_CHECKS=1;
