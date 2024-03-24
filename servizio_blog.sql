-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mar 24, 2024 alle 16:37
-- Versione del server: 10.4.25-MariaDB
-- Versione PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `servizio_blog`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `blog`
--

CREATE TABLE `blog` (
  `IdBlog` int(11) NOT NULL,
  `Titolo` varchar(30) NOT NULL,
  `Descrizione` text NOT NULL,
  `Immagine` varchar(128) DEFAULT NULL,
  `IdUtente` int(11) NOT NULL,
  `IdCategoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `blog`
--

INSERT INTO `blog` (`IdBlog`, `Titolo`, `Descrizione`, `Immagine`, `IdUtente`, `IdCategoria`) VALUES
(1, 'Star Wars', 'solo per i veri amanti della saga!', 'foto_utenti/Senza titolo.png', 1, 8),
(2, 'Vinili', 'consigli e discussioni intorno al vinile...e non solo!', NULL, 2, 9),
(19, 'Iron man', 'blog dedicato al mio supereroe preferito', NULL, 1, 17),
(32, 'Vespa Piaggio', 'Quanto sono ganze le Vespe', 'foto_utenti/598077890.jpg', 1, 4),
(48, 'pizza', 'viva la pizza', 'foto_utenti/marx.jpg', 19, 5),
(50, 'Letteratura Italiana', 'amanti di libri e lettere', 'foto_utenti/alessandro-manzoni-2012.jpg', 19, 5),
(52, 'Lovecraft', 'Blog sul più grande autore horror', 'foto_utenti/29256378.jpg', 19, 19),
(54, 'Love & robots', 'storie appassionanti', 'foto_utenti/the-mandalorian-cover-2-534x400.jpg', 21, 35);

--
-- Trigger `blog`
--
DELIMITER $$
CREATE TRIGGER `limite_utente_standard` BEFORE INSERT ON `blog` FOR EACH ROW BEGIN
                   IF (NEW.IdUtente IN (SELECT utente.IdUtente FROM utente WHERE utente.Premium=0)) THEN 
                   IF (SELECT numeroblog.NumeroBlog FROM numeroblog WHERE NEW.IdUtente=numeroblog.IdUtente)>=3 THEN SET NEW.IdUtente = null;
                  END IF;
                  END IF;
                  END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `categoria`
--

CREATE TABLE `categoria` (
  `IdCategoria` int(11) NOT NULL,
  `Nome` varchar(25) NOT NULL,
  `Icona` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `categoria`
--

INSERT INTO `categoria` (`IdCategoria`, `Nome`, `Icona`) VALUES
(1, 'Natura', 'foto/icone/natura.png'),
(2, 'Società', 'foto/icone/società.png'),
(3, 'Tempo libero e lifestyle', 'foto/icone/lifestyle.png'),
(4, 'Sport', NULL),
(5, 'Arte', NULL),
(6, 'Gaming', NULL),
(7, 'Scienza e tecnologia', 'foto/icone/tech.png'),
(8, 'Cinema e TV', NULL),
(9, 'Musica', NULL),
(11, 'Intrattenimento e Cultura', 'foto/icone/intrattenimento.png'),
(12, 'Animali', NULL),
(14, 'Piante', NULL),
(16, 'Paesaggi', NULL),
(17, 'Ecologia e ambiente', NULL),
(18, 'Cucina', NULL),
(19, 'Letteratura', NULL),
(20, 'Moda e design', NULL),
(21, 'Teatro e performance', NULL),
(22, 'Astrologia', NULL),
(23, 'Humor', NULL),
(24, 'Filosofia', NULL),
(25, 'Storia', NULL),
(26, 'Politica e attualità', NULL),
(27, 'Religione e spiritualità', NULL),
(28, 'Economia e finanza', NULL),
(29, 'Geografia', NULL),
(30, 'Fotografia', NULL),
(31, 'Viaggio', NULL),
(32, 'Artigianato', NULL),
(33, 'Giardinaggio', NULL),
(34, 'Salute e benessere', NULL),
(35, 'Robotica e IA', NULL),
(36, 'Astronomia', NULL),
(37, 'Informatica', NULL),
(38, 'Medicina', NULL),
(39, 'Ingegneria', NULL),
(40, 'Scienze naturali', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `coautore`
--

CREATE TABLE `coautore` (
  `IdUtente` int(11) NOT NULL,
  `IdBlog` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `coautore`
--

INSERT INTO `coautore` (`IdUtente`, `IdBlog`) VALUES
(1, 48),
(1, 50),
(1, 52),
(2, 50),
(7, 32),
(14, 32),
(19, 32);

--
-- Trigger `coautore`
--
DELIMITER $$
CREATE TRIGGER `coautori_tuoi_blog` BEFORE INSERT ON `coautore` FOR EACH ROW BEGIN
                   IF NEW.IdBlog IN (SELECT blog.IdBlog FROM blog WHERE blog.IdUtente = NEW.IdUtente) THEN SET NEW.IdUtente = null;
                  END IF;
                  END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `limite_coautori` BEFORE INSERT ON `coautore` FOR EACH ROW BEGIN
                   IF NEW.IdBlog IN (SELECT blog.IdBlog FROM blog, utente, numeroblog, numero_coautori WHERE utente.IdUtente=blog.IdUtente AND blog.IdBlog=numero_coautori.blog AND utente.IdUtente=numeroblog.IdUtente AND utente.Premium=0 AND numero_coautori.numerocoautori=1) THEN SET NEW.IdUtente = null;
                  END IF;
                  END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `commenta`
--

CREATE TABLE `commenta` (
  `IdCommento` int(11) NOT NULL,
  `IdUtente` int(11) NOT NULL,
  `IdPost` int(11) NOT NULL,
  `Data` date NOT NULL,
  `Ora` time NOT NULL,
  `Contenuto` text NOT NULL,
  `Modificato` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `commenta`
--

INSERT INTO `commenta` (`IdCommento`, `IdUtente`, `IdPost`, `Data`, `Ora`, `Contenuto`, `Modificato`) VALUES
(11, 19, 5, '2024-01-29', '18:09:12', 'grande pippo, come stai?', b'1'),
(12, 13, 5, '2024-01-17', '17:11:35', 'eilà', b'0'),
(17, 19, 6, '2024-01-31', '16:28:37', 'sì', b'1'),
(43, 19, 5, '2024-02-26', '14:20:33', 'eeeee', b'1'),
(44, 19, 5, '2024-02-26', '14:59:00', 'buongiornoo', b'1'),
(66, 19, 68, '2024-03-21', '16:15:33', 'ue come va?', b'1'),
(69, 21, 69, '2024-03-23', '17:41:45', 'grande mark!', b'0');

-- --------------------------------------------------------

--
-- Struttura della tabella `contiene`
--

CREATE TABLE `contiene` (
  `IdSopracategoria` int(11) NOT NULL,
  `IdSottocategoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `contiene`
--

INSERT INTO `contiene` (`IdSopracategoria`, `IdSottocategoria`) VALUES
(3, 4),
(11, 5),
(7, 6),
(11, 8),
(11, 9),
(1, 12),
(1, 14),
(1, 16),
(1, 17),
(3, 18),
(11, 19),
(11, 20),
(11, 21),
(11, 22),
(11, 23),
(11, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 29),
(3, 30),
(3, 31),
(3, 32),
(3, 33),
(3, 34),
(7, 35),
(7, 36),
(7, 37),
(7, 38),
(7, 39),
(7, 40);

--
-- Trigger `contiene`
--
DELIMITER $$
CREATE TRIGGER `limite_categoria` BEFORE INSERT ON `contiene` FOR EACH ROW BEGIN
          IF (SELECT COUNT(contiene.IdSottocategoria) FROM contiene WHERE NEW.IdSottocategoria=contiene.IdSottocategoria)>=1
                  THEN SET NEW.IdSottocategoria = null;
                  END IF;
                  END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `limite_figli_categorie` BEFORE INSERT ON `contiene` FOR EACH ROW BEGIN
          IF (SELECT COUNT(*) FROM contiene WHERE NEW.IdSopracategoria=contiene.IdSottocategoria)>=1
                  THEN SET NEW.IdSottocategoria = null;
                  END IF;
                  END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `feedback`
--

CREATE TABLE `feedback` (
  `IdUtente` int(11) NOT NULL,
  `IdPost` int(11) NOT NULL,
  `Tipo` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `feedback`
--

INSERT INTO `feedback` (`IdUtente`, `IdPost`, `Tipo`) VALUES
(1, 2, b'1'),
(1, 5, b'1'),
(7, 67, b'0'),
(10, 62, b'1'),
(13, 67, b'1'),
(19, 2, b'1'),
(19, 3, b'1'),
(19, 5, b'1'),
(19, 6, b'1'),
(19, 62, b'1'),
(19, 67, b'1'),
(19, 68, b'1'),
(19, 70, b'1'),
(21, 69, b'1');

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `numeroblog`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `numeroblog` (
`IdUtente` int(11)
,`NumeroBlog` bigint(21)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `numero_coautori`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `numero_coautori` (
`blog` int(11)
,`numerocoautori` bigint(21)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `numero_commenti`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `numero_commenti` (
`IdPost` int(11)
,`numerocommenti` bigint(21)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `numero_feedback_negativi`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `numero_feedback_negativi` (
`idPost` int(11)
,`numerofeedbacknegativi` bigint(21)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `numero_feedback_positivi`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE `numero_feedback_positivi` (
`idPost` int(11)
,`numerofeedbackpositivi` bigint(21)
);

-- --------------------------------------------------------

--
-- Struttura della tabella `post`
--

CREATE TABLE `post` (
  `IdPost` int(11) NOT NULL,
  `Titolo` varchar(50) NOT NULL,
  `Data` date NOT NULL,
  `Ora` time NOT NULL,
  `Testo` text NOT NULL,
  `Immagine` varchar(128) DEFAULT NULL,
  `Modificato` bit(1) NOT NULL DEFAULT b'0',
  `IdBlog` int(11) NOT NULL,
  `IdUtente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `post`
--

INSERT INTO `post` (`IdPost`, `Titolo`, `Data`, `Ora`, `Testo`, `Immagine`, `Modificato`, `IdBlog`, `IdUtente`) VALUES
(1, 'Che bello', '2012-03-23', '14:35:00', 'questo blog è ganzo davvero!', NULL, b'0', 1, 1),
(2, 'Eccomi anche io', '2012-05-23', '14:35:00', 'yuppi', NULL, b'0', 1, 1),
(3, 'Cosa mi piace', '2013-03-23', '22:00:00', 'la pizza', NULL, b'0', 2, 1),
(5, 'capperi', '2023-10-16', '15:23:05', 'acciughe', NULL, b'0', 32, 1),
(6, 'la mia nuova vespa!', '2023-10-16', '16:34:59', 'vi piace?', 'foto_utenti/1871624287.gif', b'0', 32, 1),
(62, 'IL mio libro preferito di Lovecraft', '2024-02-17', '12:53:04', 'non lo so :(', NULL, b'1', 52, 19),
(67, 'W LA CECINA', '2024-02-24', '12:12:41', 'quanto è buona!', 'foto_utenti/49750_hd.jpg', b'1', 48, 19),
(68, 'Guardate qui!', '2024-03-15', '16:27:52', 'Bill Murray', 'foto_utenti/1345304384.jpg', b'1', 48, 19),
(69, 'rieccomi', '2024-03-20', '16:47:25', 'ciaonee raga', 'foto_utenti/697x392.jpg', b'1', 50, 19),
(70, 'Amo i robots', '2024-03-23', '17:40:18', 'Viva Asimov', NULL, b'0', 54, 21);

--
-- Trigger `post`
--
DELIMITER $$
CREATE TRIGGER `scrittura` BEFORE INSERT ON `post` FOR EACH ROW BEGIN
          IF NEW.IdUtente NOT IN (SELECT blog.IdUtente FROM blog WHERE NEW.IdBlog = blog.IdBlog UNION SELECT coautore.IdUtente FROM coautore WHERE NEW.IdBlog = coautore.IdBlog)
                  THEN SET NEW.IdUtente = null;
                  END IF;
                  END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `IdUtente` int(11) NOT NULL,
  `Username` varchar(25) NOT NULL,
  `Passw` varchar(128) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Premium` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`IdUtente`, `Username`, `Passw`, `Email`, `Premium`) VALUES
(1, 'pippo', 'ddd7104839f2e3857ac49367e00f47d7092c8bbf0214add1033a3a0fc31007e6a69859419c2fcf6bba24cee3520c4fd3fab14d801697f076299f68b4f49b1e84', 'ciao', b'1'),
(2, 'maria', '2829d7b4e26acd4fbce26df65387cd7f2bb5092008c643731603f9d5c18ccd62358e2901c4256e18356b5d92ebea09e0af1de70fa0bb016b11fac35eaabb522f', 'ciao2', b'0'),
(7, 'mario', '431a7282b6acf1b04e987c83f17af6d734b44c2577dfc820f29912492c3790af40b1e54b60c95d2c2856c2760686937d93040f090292c24c03ab1f07f87b6622', 'mail@italo.it', b'0'),
(9, 'pluto', 'ddd7104839f2e3857ac49367e00f47d7092c8bbf0214add1033a3a0fc31007e6a69859419c2fcf6bba24cee3520c4fd3fab14d801697f076299f68b4f49b1e84', 'mail22@italo.it', b'0'),
(10, 'michele', '431a7282b6acf1b04e987c83f17af6d734b44c2577dfc820f29912492c3790af40b1e54b60c95d2c2856c2760686937d93040f090292c24c03ab1f07f87b6622', 'mail23@italo.it', b'0'),
(13, 'giuse', 'e851eeb2fc590f2592f0c759d78720bf16be12eb7a475603ec7a12e03829fa48f8c767c5ae94c086701b474e69a4dab10a17f4a6a1f09f8a89cb61470efabdda', 'mail25@italo.it', b'0'),
(14, 'lorenzo', 'ba7f012761edeadc11403ee98f3cc492ba262d5b88a2f084a60440983e59ce95d2801574e73f67478fabd1cc37fbf08b8d73ceef6f8cdb01f5f492258cd2d1c9', 'mail40@italo.it', b'0'),
(19, 'mark2', '72adaf2b426e730ac8b8218639c5c0774176155fe4f26f4108226af63e0eaa7918b3b7d8bcc586a19492ae5299deeca94cb05a8bed18507152debaebc5449f9a', 'mail78@italo.it', b'1'),
(20, 'pietro', 'dd22652203d6a293089c6ff644dc75805be358010e828e29e7c71269b3a1c081d99c24e9d94b973477c54661d67c891f8aab72ac0abe32dd4b342258f140a825', 'a@gmail.com', b'0'),
(21, 'mirko', 'e130cc49c56fa8d6cfedad85b519612a8a7cf32eee4bddb178708cb984fe844a522c176d719f6c03499b84b4d96d764f3b4f0d179b4cc497b5574c536d5812e3', 'as@gmail.com', b'0');

-- --------------------------------------------------------

--
-- Struttura per vista `numeroblog`
--
DROP TABLE IF EXISTS `numeroblog`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `numeroblog`  AS SELECT `blog`.`IdUtente` AS `IdUtente`, count(`blog`.`IdBlog`) AS `NumeroBlog` FROM `blog` GROUP BY `blog`.`IdUtente`  ;

-- --------------------------------------------------------

--
-- Struttura per vista `numero_coautori`
--
DROP TABLE IF EXISTS `numero_coautori`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `numero_coautori`  AS SELECT `coautore`.`IdBlog` AS `blog`, count(0) AS `numerocoautori` FROM `coautore` GROUP BY `coautore`.`IdBlog`  ;

-- --------------------------------------------------------

--
-- Struttura per vista `numero_commenti`
--
DROP TABLE IF EXISTS `numero_commenti`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `numero_commenti`  AS SELECT `commenta`.`IdPost` AS `IdPost`, count(0) AS `numerocommenti` FROM `commenta` GROUP BY `commenta`.`IdPost`  ;

-- --------------------------------------------------------

--
-- Struttura per vista `numero_feedback_negativi`
--
DROP TABLE IF EXISTS `numero_feedback_negativi`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `numero_feedback_negativi`  AS SELECT `feedback`.`IdPost` AS `idPost`, count(0) AS `numerofeedbacknegativi` FROM `feedback` WHERE `feedback`.`Tipo` = 0 GROUP BY `feedback`.`IdPost`  ;

-- --------------------------------------------------------

--
-- Struttura per vista `numero_feedback_positivi`
--
DROP TABLE IF EXISTS `numero_feedback_positivi`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `numero_feedback_positivi`  AS SELECT `feedback`.`IdPost` AS `idPost`, count(0) AS `numerofeedbackpositivi` FROM `feedback` WHERE `feedback`.`Tipo` = 1 GROUP BY `feedback`.`IdPost`  ;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`IdBlog`),
  ADD KEY `fk_autore` (`IdUtente`),
  ADD KEY `fk_categoria` (`IdCategoria`);

--
-- Indici per le tabelle `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`IdCategoria`);

--
-- Indici per le tabelle `coautore`
--
ALTER TABLE `coautore`
  ADD PRIMARY KEY (`IdUtente`,`IdBlog`),
  ADD KEY `fk_blog` (`IdBlog`);

--
-- Indici per le tabelle `commenta`
--
ALTER TABLE `commenta`
  ADD PRIMARY KEY (`IdCommento`),
  ADD KEY `fk_post_commento` (`IdPost`);

--
-- Indici per le tabelle `contiene`
--
ALTER TABLE `contiene`
  ADD PRIMARY KEY (`IdSottocategoria`,`IdSopracategoria`),
  ADD KEY `fk_categoria_1` (`IdSopracategoria`);

--
-- Indici per le tabelle `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`IdUtente`,`IdPost`),
  ADD KEY `fk_post_feedback` (`IdPost`);

--
-- Indici per le tabelle `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`IdPost`),
  ADD KEY `fk_blog_post` (`IdBlog`),
  ADD KEY `fk_autore_post` (`IdUtente`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`IdUtente`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `blog`
--
ALTER TABLE `blog`
  MODIFY `IdBlog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT per la tabella `categoria`
--
ALTER TABLE `categoria`
  MODIFY `IdCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT per la tabella `commenta`
--
ALTER TABLE `commenta`
  MODIFY `IdCommento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT per la tabella `post`
--
ALTER TABLE `post`
  MODIFY `IdPost` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `IdUtente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `blog`
--
ALTER TABLE `blog`
  ADD CONSTRAINT `fk_autore` FOREIGN KEY (`IdUtente`) REFERENCES `utente` (`IdUtente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`IdCategoria`) REFERENCES `categoria` (`IdCategoria`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `coautore`
--
ALTER TABLE `coautore`
  ADD CONSTRAINT `fk_blog` FOREIGN KEY (`IdBlog`) REFERENCES `blog` (`IdBlog`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_coautore` FOREIGN KEY (`IdUtente`) REFERENCES `utente` (`IdUtente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `commenta`
--
ALTER TABLE `commenta`
  ADD CONSTRAINT `fk_autore_commento` FOREIGN KEY (`IdUtente`) REFERENCES `utente` (`IdUtente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_post_commento` FOREIGN KEY (`IdPost`) REFERENCES `post` (`IdPost`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `contiene`
--
ALTER TABLE `contiene`
  ADD CONSTRAINT `fk_categoria_1` FOREIGN KEY (`IdSopracategoria`) REFERENCES `categoria` (`IdCategoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_categoria_2` FOREIGN KEY (`IdSottocategoria`) REFERENCES `categoria` (`IdCategoria`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_autore_feedback` FOREIGN KEY (`IdUtente`) REFERENCES `utente` (`IdUtente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_post_feedback` FOREIGN KEY (`IdPost`) REFERENCES `post` (`IdPost`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fk_autore_post` FOREIGN KEY (`IdUtente`) REFERENCES `utente` (`IdUtente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_blog_post` FOREIGN KEY (`IdBlog`) REFERENCES `blog` (`IdBlog`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
