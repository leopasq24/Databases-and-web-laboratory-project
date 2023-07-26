-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Lug 26, 2023 alle 12:37
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
  `Immagine` blob NOT NULL,
  `IdUtente` int(11) NOT NULL,
  `IdCategoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `blog`
--

INSERT INTO `blog` (`IdBlog`, `Titolo`, `Descrizione`, `Immagine`, `IdUtente`, `IdCategoria`) VALUES
(1, 'giovanni', 'hello there', '', 1, NULL),
(2, 'cd', 'cd', '', 1, NULL);

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
  `Nome` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `categoria`
--

INSERT INTO `categoria` (`IdCategoria`, `Nome`) VALUES
(1, 'Animali'),
(2, 'Gatto'),
(3, 'Mammiferi');

-- --------------------------------------------------------

--
-- Struttura della tabella `coautore`
--

CREATE TABLE `coautore` (
  `IdUtente` int(11) NOT NULL,
  `IdBlog` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `commenta`
--

CREATE TABLE `commenta` (
  `IdUtente` int(11) NOT NULL,
  `IdPost` int(11) NOT NULL,
  `Data` date NOT NULL,
  `Contenuto` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(1, 2);

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

-- --------------------------------------------------------

--
-- Struttura della tabella `feedback`
--

CREATE TABLE `feedback` (
  `IdUtente` int(11) NOT NULL,
  `IdPost` int(11) NOT NULL,
  `Tipo` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Struttura della tabella `post`
--

CREATE TABLE `post` (
  `IdPost` int(11) NOT NULL,
  `Titolo` varchar(30) DEFAULT NULL,
  `Data` date NOT NULL,
  `Ora` time NOT NULL,
  `Testo` text NOT NULL,
  `Immagine` blob DEFAULT NULL,
  `IdBlog` int(11) NOT NULL,
  `IdUtente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `post`
--

INSERT INTO `post` (`IdPost`, `Titolo`, `Data`, `Ora`, `Testo`, `Immagine`, `IdBlog`, `IdUtente`) VALUES
(1, NULL, '2012-03-23', '14:35:00', '', NULL, 1, 1),
(2, NULL, '2012-03-23', '14:35:00', 'yuppi', NULL, 1, 1);

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
  `Passw` varchar(30) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Premium` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`IdUtente`, `Username`, `Passw`, `Email`, `Premium`) VALUES
(1, 'pippo', 'pluto', 'ciao', b'1'),
(2, 'maria', 'db', 'ciao2', b'0');

-- --------------------------------------------------------

--
-- Struttura per vista `numeroblog`
--
DROP TABLE IF EXISTS `numeroblog`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `numeroblog`  AS SELECT `blog`.`IdUtente` AS `IdUtente`, count(`blog`.`IdBlog`) AS `NumeroBlog` FROM `blog` GROUP BY `blog`.`IdUtente``IdUtente`  ;

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
  ADD PRIMARY KEY (`IdUtente`,`IdPost`),
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
  MODIFY `IdBlog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT per la tabella `categoria`
--
ALTER TABLE `categoria`
  MODIFY `IdCategoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `post`
--
ALTER TABLE `post`
  MODIFY `IdPost` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `IdUtente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
