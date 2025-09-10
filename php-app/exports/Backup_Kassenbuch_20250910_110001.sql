CREATE TABLE `bestaende` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kassennummer` int(151) NOT NULL,
  `datum` date NOT NULL,
  `monat` int(11) DEFAULT NULL,
  `ausgaben` decimal(10,2) DEFAULT NULL,
  `einlagen` decimal(10,2) DEFAULT NULL,
  `bestand` decimal(10,2) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_bestaende_user` (`userid`),
  KEY `fk_bestaende_kasse` (`kassennummer`),
  CONSTRAINT `fk_bestaende_kasse` FOREIGN KEY (`kassennummer`) REFERENCES `kasse` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bestaende_user` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('50','1','2025-01-01','1','400.00','400.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('51','1','2025-02-01','2','400.00','400.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('52','1','2025-03-01','3','400.00','400.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('53','1','2025-04-01','4','400.00','400.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('54','1','2025-05-01','5','569.90','600.00','30.10','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('55','1','2025-06-01','6','378.12','400.00','51.98','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('56','1','2025-07-01','7','404.34','400.00','47.64','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('57','1','2025-08-01','8','445.01','400.00','2.63','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('58','1','2025-09-01','9','118.52','590.00','474.11','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('59','1','2025-10-01','10','0.00','0.00','474.11','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('60','1','2025-11-01','11','0.00','0.00','474.11','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('61','1','2025-12-01','12','0.00','0.00','474.11','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('62','2','2025-01-01','1','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('63','2','2025-02-01','2','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('64','2','2025-03-01','3','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('65','2','2025-04-01','4','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('66','2','2025-05-01','5','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('67','2','2025-06-01','6','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('68','2','2025-07-01','7','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('69','2','2025-08-01','8','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('70','2','2025-09-01','9','75.99','400.00','324.01','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('71','2','2025-10-01','10','0.00','0.00','324.01','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('72','2','2025-11-01','11','0.00','0.00','324.01','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('73','2','2025-12-01','12','0.00','0.00','324.01','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('74','3','2025-01-01','1','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('75','3','2025-02-01','2','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('76','3','2025-03-01','3','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('77','3','2025-04-01','4','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('78','3','2025-05-01','5','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('79','3','2025-06-01','6','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('80','3','2025-07-01','7','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('81','3','2025-08-01','8','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('82','3','2025-09-01','9','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('83','3','2025-10-01','10','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('84','3','2025-11-01','11','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('85','3','2025-12-01','12','0.00','0.00','0.00','1');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('134','8','2025-01-01','1','0.00','0.00','0.00','22');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('135','8','2025-02-01','2','0.00','0.00','0.00','22');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('136','8','2025-03-01','3','0.00','0.00','0.00','22');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('137','8','2025-04-01','4','0.00','0.00','0.00','22');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('138','8','2025-05-01','5','0.00','0.00','0.00','22');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('139','8','2025-06-01','6','0.00','0.00','0.00','22');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('140','8','2025-07-01','7','0.00','0.00','0.00','22');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('141','8','2025-08-01','8','0.00','0.00','0.00','22');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('142','8','2025-09-01','9','0.00','0.00','0.00','22');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('143','8','2025-10-01','10','0.00','0.00','0.00','22');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('144','8','2025-11-01','11','0.00','0.00','0.00','22');
INSERT INTO `bestaende` (`id`,`kassennummer`,`datum`,`monat`,`ausgaben`,`einlagen`,`bestand`,`userid`) VALUES ('145','8','2025-12-01','12','0.00','0.00','0.00','22');


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
  `buchungsart` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_buchungen_userid` (`userid`),
  KEY `fk_barkasse_user` (`kassennummer`),
  CONSTRAINT `fk_barkasse_user` FOREIGN KEY (`kassennummer`) REFERENCES `kasse` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_buchungen_userid` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=488 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('1','1','1','RE202521-0001','2025-04-24','Einkauf Aldi','Tagesbedarf','10.59','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('2','1','1','RE202521-0002','2025-07-30','ALDI SUED','Tagesbedarf','26.51','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('9','1','1','RE202521-0009','2025-04-23','Einkauf Aldi','Tagesbedarf','13.00','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('244','1','1','RE202521-244','2025-04-28','Flohmarkt',' Plochingen CDs und DVDs','2.56','Ausgabe','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('245','1','1','RE202521-245','2025-04-27','Flohmarkt',' Plochingen Verlängerungskabel','4.00','Ausgabe','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('247','1','1','RE202521-0247','2025-04-27','Flohmarkt',' Plochingen Socken','10.00','Ausgabe','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('248','1','1','RE202521-0248','2025-04-29','ALDI SUED','Tagesbedarf','5.67','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('249','1','1','RE202521-0249','2025-04-30','Einkauf Aldi','Tagesbedarf','14.25','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('250','1','1','RE202521-0250','2025-04-30','Eintritte','Spohn 1 Stunde Tennis','10.00','Ausgabe','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('251','1','1','RE202521-0251','2025-04-17','LIDL','Tagesbedarf','26.57','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('252','1','1','RE202521-0252','2025-04-21','Flohmarkt','Süßen Verschiedenes','14.00','Ausgabe','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('253','1','1','RE202521-0253','2025-03-29','Eintritte','Spohn Tennis 1 Stunde','10.00','Ausgabe','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('255','1','1','RE202521-0255','2025-01-31','Einkäufe','Tagesbedarf','400.00','Ausgabe','1','Einkäufe');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('256','1','1','RE202521-0256','2025-02-28','Sammelbuchung Supermarkt','Tagesbedarfe Februar','400.00','Ausgabe','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('257','1','1','RE202521-0257','2025-03-31','Sammelbuchung Supermarkt','Tagesbedarfe März','390.00','Ausgabe','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('258','1','1','RE202521-0258','2025-05-01','Essen','Inder Geburtstag Restaurant Ganseha','62.50','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('260','1','1','RE202521-0260','2025-05-02','Einkauf Aldi','Tagesbedarf','12.51','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('261','1','1','RE202521-0261','2025-05-02','Einlage','Kasse Einlage','400.00','Einlage','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('262','1','1','RE202521-0262','2025-05-05','CAP Markt ','Tagesbedarf','1.74','Ausgabe','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('263','1','1','RE202521-0263','2025-05-04','Flohmarkt','Fellbach kleine Heizung, Insektenfänger, DVD','11.00','Ausgabe','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('264','1','1','RE202521-0264','2025-01-01','Einlage','Kasse Einlage','400.00','Einlage','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('265','1','1','RE202521-0265','2025-02-01','Einlage','Kasse Einlage','400.00','Einlage','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('266','1','1','RE202521-0266','2025-03-01','Einlage','Kasse Einlage','400.00','Einlage','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('267','1','1','RE202521-0267','2025-04-01','Einlage','Kasse Einlage','400.00','Einlage','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('268','1','1','RE202521-0268','2025-04-30','Sammelbuchung Supermarkt','Tagesbedarfe April','289.36','Ausgabe','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('270','1','1','RE202521-0270','2025-05-06','Essen','Essen','33.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('272','1','1','RE202521-0272','2025-05-06','Essen','2 Latte Macciato','10.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('274','1','1','RE202521-0274','2025-05-10','LIDL','Tagesbedarf','19.51','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('275','1','1','RE202521-0275','2025-05-09','Essen','Restaurant Ganseha Langenargen','34.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('276','1','1','RE202521-0276','2025-05-08','2 Fahrräder','Leihe','20.00','Ausgabe','1','2 Fahrräder');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('277','1','1','RE202521-0277','2025-05-06','Einlage Urlaub','Einlage Urlaub','200.00','Einlage','1','Einlage Urlaub');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('278','1','1','RE202521-0278','2025-05-08','Essen','Restaurant Nordsee Lindau','30.40','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('279','1','1','RE202521-0279','2025-05-07','Essen','Restaurant Vietnamese Meersburg','26.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('280','1','1','RE202521-0280','2025-05-09','CAP-Markt','Tagesbedarf','3.50','Ausgabe','1','CAP Markt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('282','1','1','RE202521-0282','2025-05-23','LIDL','Tagesbedarf','11.56','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('283','1','1','RE202521-0283','2025-05-22','ALDI SUED','Tagesbedarf','8.54','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('284','1','1','RE202521-0284','2025-05-20','Drogeriemarkt','MH Muller Handels GmbH Schampoo und Duschgel','5.40','Ausgabe','1','Drogeriemarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('285','1','1','RE202521-0285','2025-05-20','Essen','Bärenschlössle Essen','14.80','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('286','1','1','RE202521-0286','2025-05-18','Flohmarkt','Darts Scheibe und Kleinigkeiten','10.00','Ausgabe','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('287','1','1','RE202521-0287','2025-05-19','ALDI SUED','Tagesbedarf','12.41','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('288','1','1','RE202521-0288','2025-05-16','LIDL','Tagesbedarf','15.41','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('289','1','1','RE202521-0289','2025-05-14','LIDL','Tagesbedarf','13.57','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('290','1','1','RE202521-0290','2025-05-21','LIDL','Tagesbedarf','9.74','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('291','1','1','RE202521-0291','2025-05-12','Essen','Ajran und Getränk','5.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('292','1','1','RE202521-0292','2025-05-10','LIDL','Tagesbedarf','18.54','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('293','1','1','RE202521-0293','2025-05-26','ALDI SUED','Tagesbedarf','13.77','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('294','1','1','RE202521-0294','2025-05-31','Sammelbuchung Supermarkt','Tagesbedarfe Mai','160.00','Ausgabe','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('296','1','1','RE202521-0296','2025-06-03','CAP Markt ','Tagesbedarf','2.51','Ausgabe','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('297','1','1','RE202521-0297','2025-06-01','Einlage','Bareinlage Mai','400.00','Einlage','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('298','1','1','RE202521-0298','2025-06-03','LIDL','Tagesbedarf','9.91','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('299','1','1','RE202521-299','2025-05-24','Eintritte','Spohn Tennis 1 Stunde','5.00','Ausgabe','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('300','1','1','RE202521-0300','2025-05-31','Essen','Famlienzentrum Mettingen','2.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('301','1','1','RE202521-0301','2025-06-04','ALDI SUED','Tagesbedarf','5.01','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('303','1','1','RE202521-0303','2025-06-05','CAP Markt ','Tagesbedarf','6.61','Ausgabe','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('304','1','1','RE202521-0304','2025-06-05','Essen','Döner Obertürkheim','8.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('399','1','1','RE202521-0288','2025-07-01','Einlage','Kasse Einlage','400.00','Einlage','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('400','1','1','RE202521-0400','2025-07-29','CAP Markt ','Tagesbedarf','6.64','Ausgabe','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('401','1','1','RE202521-0401','2025-07-18','Eintritte','Inselbad','5.00','Ausgabe','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('402','1','1','RE202521-0402','2025-07-19','LIDL','Tagesbedarf','22.54','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('403','1','1','RE202521-0403','2025-07-11','Eintritte','Inselbad','5.00','Ausgabe','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('404','1','1','RE202521-0404','2025-07-24','Eintritte','Leuze','21.00','Ausgabe','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('405','1','1','RE202521-0405','2025-07-27','Essen','Deizisau Schnitzel, Pommes, Currywurst','23.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('406','1','1','RE202521-0406','2025-07-29','ALDI SUED','Tagesbedarf','6.60','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('407','1','1','RE202521-0407','2025-07-28','ALDI SUED','Tagesbedarf','11.84','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('408','1','1','RE202521-0408','2025-07-19','Flohmarkt','Kaltental','2.00','Ausgabe','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('409','1','1','RE202521-0409','2025-06-29','Eintritte','Bad Öhningen','6.00','Ausgabe','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('410','1','1','RE202521-0410','2025-06-29','Essen','Schnitzel und Currywurst Bad','23.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('411','1','1','RE202521-0411','2025-06-15','ALDI SUED','Tagesbedarf','18.54','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('412','1','1','RE202521-0412','2025-06-30','ALDI SUED','Tagesbedarf','18.54','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('413','1','1','RE202521-0413','2025-06-30','Sammelbuchung Supermarkt','Tagesbedarfe Juli 2025','280.00','Ausgabe','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('414','1','1','RE202521-0414','2025-07-12','LIDL','Tagesbedarf','17.84','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('415','1','1','RE202521-0415','2025-07-05','ALDI SUED','Tagesbedarf','15.78','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('416','1','1','RE202521-0416','2025-07-31','ALDI SUED','Tagesbedarf','9.18','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('417','1','1','RE202521-0417','2025-07-07','ALDI SUED','Tagesbedarf','12.54','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('418','1','1','RE202521-0418','2025-07-09','LIDL','Tagesbedarf','16.87','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('419','1','1','RE202521-0419','2025-07-31','Sammelbuchung Supermarkt','Tagesbedarf','200.00','Ausgabe','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('420','1','1','RE202521-0420','2025-08-11','ALDI SUED','Tagesbedarf','10.02','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('421','1','1','RE202521-0421','2025-08-01','Einlage','Kasse Einlage August 2025','400.00','Einlage','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('422','1','1','RE202521-0422','2025-08-11','Essen','Bäcker 2 Schnitten mit Pistazien','4.60','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('423','1','1','RE202521-0423','2025-08-11','REWE','Tagesbedarf Rewe','3.50','Ausgabe','1','REWE');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('424','1','1','RE202521-0424','2025-08-09','LIDL','Tagesbedarf','13.41','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('425','1','1','RE202521-0425','2025-08-05','Baumarkt','Pinsel Bauhaus','7.99','Ausgabe','1','Baumarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('426','1','1','RE202521-0426','2025-08-11','ALDI SUED','Tagesbedarf','11.45','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('427','1','1','RE202521-0427','2025-08-08','LIDL','Tagesbedarf','13.40','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('428','1','1','RE202521-0428','2025-08-10','Badeeintritt','2 x Deizisau','9.80','Ausgabe','1','Badeeintritt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('429','1','1','RE202521-0429','2025-08-10','Essen','Pommes Frittes Bad Deizisau','4.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('430','1','1','RE202521-0430','2025-08-02','ALDI SUED','Tagesbedarf','16.85','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('431','1','1','RE202521-0431','2025-08-13','Essen','Sindelfingen Türke Falafel und Getränk','8.40','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('432','1','1','RE202521-0432','2025-08-13','CAP Markt ','Tagesbedarf','5.05','Ausgabe','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('435','1','1','RE202521-0435','2025-08-14','ALDI SUED','Tagesbedarf','13.08','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('436','1','1','RE202521-0436','2025-08-16','Flohmarkt','Flohmarkt Winnenden Tastatur, Kaktus, DVD, 2 CD','10.00','Ausgabe','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('437','1','1','RE202521-0437','2025-08-16','LIDL','Tagesbedarf','13.41','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('438','1','1','RE202521-0438','2025-08-18','Essen','Fellbach Bäckerei','4.45','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('439','1','1','RE202521-0439','2025-08-19','CAP Markt ','Tagesbedarf','6.68','Ausgabe','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('440','1','1','RE202521-0440','2025-08-19','ALDI SUED','Tagesbedarf','7.07','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('441','1','1','RE202521-0441','2024-12-31','Essen','Silvestermenü','75.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('442','1','1','RE202521-0442','2025-08-20','ALDI SUED','Tagesbedarf','11.81','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('448','1','1','RE202521-0448','2025-08-21','ALDI SUED','Tagesbedarf','9.73','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('449','1','1','RE202521-0449','2025-07-26','Essen','Famlienzentrum Mettingen','2.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('451','1','1','RE202521-0451','2025-08-22','LIDL','Tagesbedarf','15.58','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('452','1','1','RE202521-0452','2025-08-24','Essen','Cafe Flohmarkt','7.60','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('453','1','1','RE202521-0453','2025-08-24','Flohmarkt','Flohmarkt Starkholzburg Teleon, Buch, Pflanze','11.00','Ausgabe','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('454','1','1','RE202521-0454','2025-08-23','Eintritte','Spohn Tennis 1 Stunde','5.00','Ausgabe','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('455','1','1','RE202521-0455','2025-08-23','LIDL','Tagesbedarf','15.28','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('456','1','1','RE202521-0456','2025-08-23','CAP Markt ','Tagesbedarf','8.08','Ausgabe','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('457','1','1','RE202521-0457','2025-08-25','ALDI SUED','Tagesbedarf','11.87','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('458','1','1','RE202521-0458','2025-08-26','CAP Markt ','Tagesbedarf','3.12','Ausgabe','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('459','1','1','RE202521-0459','2025-08-27','ALDI SUED','Tagesbedarf','6.25','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('460','1','1','RE202521-0460','2025-08-27','Essen','Sindelfingen Türke Chicken Burger vegetarisch','4.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('461','1','1','RE202521-0461','2025-08-28','ALDI SUED','Tagesbedarf','13.45','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('462','1','1','RE202521-0462','2025-08-30','Essen','Wöhrwag Rostbraten mit Kraut','25.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('463','1','1','RE202521-0463','2025-08-30','ALDI SUED','Tagesbedarf','12.08','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('464','1','1','RE202521-0464','2025-08-31','Essen','Inder Bad Cannstatt Schlemmerblock','27.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('465','1','1','RE202521-0465','2025-08-31','Sammelbuchung Supermarkt','Tagesbedarfe August 2025','95.00','Ausgabe','1','Sammelbuchung Supermarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('466','1','1','RE202521-0466','2025-09-01','Einlage','Kasse Einlage September 2025','400.00','Einlage','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('467','1','1','RE202521-0467','2025-09-03','ALDI SUED','Tagesbedarf','6.20','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('468','1','1','RE202521-0468','2025-09-02','ALDI SUED','Tagesbedarf','11.20','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('469','1','1','RE202521-0469','2025-09-03','CAP Markt ','Tagesbedarf','5.24','Ausgabe','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('470','1','1','RE202521-0470','2025-09-04','LIDL','Tagesbedarf','11.64','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('471','2','1','RE202521-0471','2025-09-05','Einlage','Kasse Einlage Sepetmber 2025','400.00','Einlage','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('472','2','1','RE202521-0472','2025-09-05','ALDI SUED','Schnellkochtopf','49.99','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('473','2','1','RE202521-0473','2025-09-05','REWE','Tagesbedarf','10.00','Ausgabe','1','REWE');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('474','2','1','RE202521-0474','2025-09-05','Eintritte','Spohn Tennis 1 Stunde','16.00','Ausgabe','1','Eintritte');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('476','1','1','RE202521-0476','2025-09-05','Essen','Inder Steinstr. Schlemmerblock Peter Neugebauer','17.00','Ausgabe','1','Essen');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('478','1','1','RE202521-0478','2025-09-06','CAP Markt ','Tagesbedarf','3.26','Ausgabe','1','CAP Markt ');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('479','1','1','RE202521-0479','2025-09-06','Flohmarkt','Winnenden Handy, DVD + VHS, Pflanze','14.00','Ausgabe','1','Flohmarkt');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('480','1','1','RE202521-0480','2025-09-06','LIDL','Tagesbedarf Grillgut Besuch Weiß','26.13','Ausgabe','1','LIDL');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('485','1','1','RE202521-0485','2025-09-08','ALDI SUED','Tagesbedarf','14.64','Ausgabe','1','ALDI SUED');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('486','1','1','RE202521-0486','2025-09-08','Einlage','Einlage Urlaub','190.00','Einlage','1','Einlage');
INSERT INTO `buchungen` (`id`,`kassennummer`,`barkasse`,`belegnr`,`datum`,`vonan`,`beschreibung`,`betrag`,`typ`,`userid`,`buchungsart`) VALUES ('487','1','1','RE202521-0487','2025-09-09','LIDL','Tagesbedarf','9.21','Ausgabe','1','LIDL');


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


CREATE TABLE `buchungsarten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kassennummer` int(11) NOT NULL,
  `Dauerbuchung` bit(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `userid` int(11) NOT NULL,
  `buchungsart` varchar(255) NOT NULL,
  `mwst` float NOT NULL,
  `mwst_ermaessigt` bit(1) NOT NULL,
  `standard` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Buchungsart` (`buchungsart`,`userid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('17','1','0','2025-04-25 22:56:45','2025-09-08 13:57:23','1','PayPal','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('18','1','0','2025-04-25 22:56:45','2025-09-08 13:57:23','1','Tankstelle','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('24','1','0','2025-04-25 22:56:45','2025-09-08 13:57:23','1','Essen','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('27','1','0','2025-04-25 22:56:45','2025-09-08 13:57:23','1','CAP Markt ','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('29','1','0','2025-04-25 22:56:45','2025-09-08 13:57:23','1','LIDL','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('35','1','0','2025-04-25 22:56:45','2025-09-08 13:57:23','1','Badeeintritt','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('37','1','0','2025-04-25 22:56:45','2025-09-08 13:57:43','1','Lindt Shop','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('44','1','0','2025-04-25 22:56:45','2025-09-08 13:57:23','1','Kosmetikartikel','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('51','0','0','2025-04-25 22:56:45','2025-09-04 10:38:32','2','Restaurant','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('52','1','0','2025-04-25 22:56:45','2025-09-08 13:57:23','1','Drogerieartikel','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('54','1','0','2025-04-25 22:56:45','2025-09-08 13:57:23','1','EDEKA','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('56','1','0','2025-04-25 22:56:45','2025-09-08 13:57:23','1','ALDI SUED','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('61','1','0','2025-04-25 22:56:45','2025-09-08 13:57:23','1','Eintritte','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('65','1','0','2025-05-14 11:08:58','2025-09-08 13:57:23','1','_Diverses','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('66','1','0','2025-06-04 09:10:21','2025-09-08 13:57:23','1','Sammelbuchung Supermarkt','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('69','1','0','2025-06-04 09:55:26','2025-09-08 13:57:23','1','Flohmarkt','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('72','0','0','2025-06-11 14:23:39','2025-09-04 10:38:32','2','ALDI SUED ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('73','0','0','2025-06-11 14:24:49','2025-09-04 10:38:32','2','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('74','0','0','2025-06-11 14:26:02','2025-09-04 10:38:32','2','Apotheke ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('75','1','1','2025-07-31 11:48:37','2025-09-08 13:57:23','1','Einlage','1.19','0','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('83','1','0','2025-08-21 10:35:47','2025-09-08 13:57:23','1','REWE','1.07','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('98','0','1','2025-08-21 00:00:00','2025-09-04 10:38:32','10','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('99','0','1','2025-08-21 00:00:00','2025-09-04 10:38:32','10','LIDL','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('100','0','1','2025-08-21 00:00:00','2025-09-04 10:38:32','10','ALDI SUED','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('101','0','1','2025-08-21 00:00:00','2025-09-04 10:38:32','10','Einlage','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('102','0','0','2025-08-21 00:00:00','2025-09-04 10:38:32','10','Tierfutter','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('132','1','1','2025-09-05 00:00:00','2025-09-08 13:57:23','1','Grieb','1','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('133','1','1','2025-09-06 00:00:00','2025-09-08 13:57:23','1','Baumarkt','0','1','1');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('139','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','PayPal','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('140','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','Tankstelle','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('141','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','Essen','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('142','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','CAP Markt ','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('143','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','LIDL','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('144','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','Badeeintritt','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('145','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','Kosmetikartikel','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('146','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','Drogerieartikel','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('147','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','EDEKA','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('148','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','ALDI SUED','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('149','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','Eintritte','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('150','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','_Diverses','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('151','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','Sammelbuchung Supermarkt','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('152','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','Flohmarkt','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('153','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','Einlage','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('154','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','REWE','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('155','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','Grieb','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('156','0','0','2025-09-08 00:00:00','2025-09-08 14:10:56','20','Baumarkt','0','1','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('157','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','PayPal','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('158','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','Tankstelle','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('159','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('160','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','CAP Markt ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('161','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','LIDL','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('162','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','Badeeintritt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('163','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','Kosmetikartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('164','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','Drogerieartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('165','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','EDEKA','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('166','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','ALDI SUED','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('167','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','Eintritte','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('168','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','_Diverses','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('169','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','Sammelbuchung Supermarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('170','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','Flohmarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('171','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','Einlage','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('172','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','REWE','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('173','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','Grieb','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('174','0','0','2025-09-08 00:00:00','2025-09-08 14:27:49','21','Baumarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('175','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','PayPal','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('176','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','Tankstelle','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('177','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','Essen','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('178','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','CAP Markt ','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('179','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','LIDL','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('180','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','Badeeintritt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('181','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','Kosmetikartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('182','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','Drogerieartikel','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('183','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','EDEKA','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('184','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','ALDI SUED','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('185','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','Eintritte','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('186','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','_Diverses','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('187','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','Sammelbuchung Supermarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('188','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','Flohmarkt','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('189','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','Einlage','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('190','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','REWE','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('191','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','Grieb','1.19','0','0');
INSERT INTO `buchungsarten` (`id`,`kassennummer`,`Dauerbuchung`,`created_at`,`updated_at`,`userid`,`buchungsart`,`mwst`,`mwst_ermaessigt`,`standard`) VALUES ('192','0','0','2025-09-10 00:00:00','2025-09-10 10:50:55','22','Baumarkt','1.19','0','0');


CREATE TABLE `cash_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filepath` text NOT NULL,
  `kassennummer` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `uploadedat` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cash_files` (`id`,`filepath`,`kassennummer`,`userid`,`uploadedat`) VALUES ('4','uploads/cashfiles/CashFile_1_Fahren_Oktober.pdf','1','1','2025-09-09 09:43:35');
INSERT INTO `cash_files` (`id`,`filepath`,`kassennummer`,`userid`,`uploadedat`) VALUES ('8','uploads/cashfiles/CashFile_1_Arbeitslosengeld Bewilligung.pdf','1','1','2025-09-09 09:45:48');


CREATE TABLE `kasse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kasse` text NOT NULL,
  `anfangsbestand` float NOT NULL,
  `kontonummer` int(8) NOT NULL,
  `datumab` date NOT NULL,
  `checkminus` bit(1) NOT NULL,
  `userid` int(11) NOT NULL,
  `archiviert` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kasse_user` (`userid`),
  CONSTRAINT `fk_kasse_user` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `kasse` (`id`,`kasse`,`anfangsbestand`,`kontonummer`,`datumab`,`checkminus`,`userid`,`archiviert`) VALUES ('1','Kasse Gerhard Wißt','0','1600','2024-12-01','0','1','0');
INSERT INTO `kasse` (`id`,`kasse`,`anfangsbestand`,`kontonummer`,`datumab`,`checkminus`,`userid`,`archiviert`) VALUES ('2','Kasse Gerhard Wißt2','50','1602','2025-01-01','0','1','0');
INSERT INTO `kasse` (`id`,`kasse`,`anfangsbestand`,`kontonummer`,`datumab`,`checkminus`,`userid`,`archiviert`) VALUES ('3','Kasse Gerhard Wißt 3','587','1603','2025-07-01','1','1','1');
INSERT INTO `kasse` (`id`,`kasse`,`anfangsbestand`,`kontonummer`,`datumab`,`checkminus`,`userid`,`archiviert`) VALUES ('8','Kasse Anja','350','1600','2025-09-01','1','22','0');


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `vorname` varchar(255) NOT NULL DEFAULT '',
  `nachname` varchar(255) NOT NULL DEFAULT '',
  `strasse` text NOT NULL,
  `plz` int(11) NOT NULL,
  `ort` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `twofactor_secret` varchar(64) DEFAULT NULL,
  `freigeschaltet` bit(1) NOT NULL,
  `is_admin` bit(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`id`,`email`,`passwort`,`vorname`,`nachname`,`strasse`,`plz`,`ort`,`created_at`,`updated_at`,`twofactor_secret`,`freigeschaltet`,`is_admin`) VALUES ('1','g.wisst@web.de','$2y$10$cs05zWzGCRIhxRmKyyMabuUuIweqoEC.Lak0XL068ONuKLMAyHAmW','','','','0','','2025-04-25 09:57:52','2025-09-04 10:34:53',NULL,'1','0');
INSERT INTO `users` (`id`,`email`,`passwort`,`vorname`,`nachname`,`strasse`,`plz`,`ort`,`created_at`,`updated_at`,`twofactor_secret`,`freigeschaltet`,`is_admin`) VALUES ('15','monterosa2@web.de','$2y$10$owR3b9Nja861oUtTo2feGuoe7NUBY/PdqpsZQac5rgM2er/SXtmOi','','','','0','','2025-08-21 20:00:46','2025-09-04 10:34:53','47TJA6WGYSWHIM7TKU2I73U3OXA5TRGF','1','0');
INSERT INTO `users` (`id`,`email`,`passwort`,`vorname`,`nachname`,`strasse`,`plz`,`ort`,`created_at`,`updated_at`,`twofactor_secret`,`freigeschaltet`,`is_admin`) VALUES ('22','anjalangjahr@web.de','$2y$10$5s5SgN1E9AMedVpQybggZewC/2.WJe8QSiPYnyzpvWxKMlm0vehgq','Anja','Langjahr','Augsburger Str. 717','70329','Stuttgart','2025-09-10 10:50:55','2025-09-10 10:50:55','LTSXJ3I6WEMJKHOGMJWJ4M6FXA5D4JKZ','1','0');


