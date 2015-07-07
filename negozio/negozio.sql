-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: Set 02, 2014 alle 18:38
-- Versione del server: 5.5.35
-- Versione PHP: 5.4.6-1ubuntu1.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `amm15_MarchesiLodovica`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `Operatori`
--

CREATE TABLE IF NOT EXISTS `Operatori` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `nome` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `cognome` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `via` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `civico` int(10) unsigned DEFAULT NULL,
  `cap` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `citta` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `telefono` int(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Dump dei dati per la tabella `Operatori`
--

INSERT INTO `Operatori` (`id`, `username`, `password`, `nome`, `cognome`, `via`, `civico`, `cap`, `citta`, `telefono`) VALUES
(1, 'rossi', '', 'Paolo', 'Rossi', 'XX Settembre', 12, '16100', 'Genova', 3331231231);

-- --------------------------------------------------------

--
-- Struttura della tabella `Clienti`
--

CREATE TABLE IF NOT EXISTS `Clienti` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `nome` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `cognome` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `via` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `civico` int(10) DEFAULT NULL,
  `cap` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `citta` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `telefono` int(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Dump dei dati per la tabella `Clienti`
--

INSERT INTO `Clienti` (`id`, `username`, `password`, `nome`, `cognome`, `via`, `civico`, `cap`, `citta`, `telefono`) VALUES
(1, 'lodo', '', 'Lodovica', 'Marchesi', 'Cagliari', 11, '09012', 'Capoterra', 3381111111);
INSERT INTO `Clienti` (`id`, `username`, `password`, `nome`, `cognome`, `via`, `civico`, `cap`, `citta`, `telefono`) VALUES
(2, 'momo', '', 'Guglielmo', 'Marchesi', 'Cagliari', 11, '09012', 'Capoterra', 3382222222);

-- --------------------------------------------------------
--
-- Struttura della tabella `Ordini`
--

CREATE TABLE IF NOT EXISTS `Ordini` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `spedizione` varchar(2) COLLATE utf8_bin DEFAULT NULL,
  `prezzo` float unsigned DEFAULT NULL,
  `stato` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `data` datetime DEFAULT NULL,
  `cliente_id` bigint(20) unsigned DEFAULT NULL,
  `operatore_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ordini_ibfk_1` (`cliente_id`),
  KEY `ordini_ibfk_2` (`operatore_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `Prodotti`
--

CREATE TABLE IF NOT EXISTS `Prodotti` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `descrizione` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `prezzo` float unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=21 ;

--                                                 
-- Dump dei dati per la tabella `Prodotti`
--
INSERT INTO `Prodotti` (`id`, `nome`, `descrizione`, `prezzo`) VALUES
(1, 'Albicocche', 'Composta di albicocche biologiche', 3.30),                                                  
(2,  'Prugne', 'Marmellata di prugne della California', 2.80),                                                           
(3, 'Miele di corbezzolo', 'Miele di corbezzolo di Sardegna. Aroma amaro dal gusto intenso ', 2.80),
(4, 'Miele di cardo', 'Miele dal sapore deciso e caratteristico, il suo profumo intenso ricorda quello dei fiori della campagna mediterranea ', 5.0),
(5, 'Miele di Mirto', 'Miele di mirto di Sardegna. Proprietà antibiotiche ed antibatteriche', 5.0),
(6, 'Miele di arancio', 'Miele dal profumo intenso, fresco e molto dolce', 3.10),
(7, 'Miele millefiori', 'Il miele millefiori tipico delle zone di pianura della Sardegna. Miscela ineguagliata per il suo aroma ricco e speziato', 2.80),
(9, 'Fichi', 'Confettura extra di fichi. Una delle preferite in abbinamento a formaggi stagionati saporiti e salumi piccanti.', 4.10),
(10, 'Pesche e Lamponi', 'Una confettura extra profumata, gradevolmente acidula. Da colazione, su fette biscottate. Ma anche per una crostata particolare', 3.80),
(11, 'Arance', 'Marmellata extra.  Un classico mediterraneo', 4.0),
(12, 'Mele Cotogne', 'Confettura extra di antica tradizione, cremosa, di consistenza spalmabile. Ideale per dolci e crostate', 3.50),
(13, 'Lamponi', 'Una confettura extra boschiva, acidula, piacevole ingrediente per dolci e crostate, complemento per gelati e dessert al cucchiaio', 5.10),
(14, 'Mandarini', 'Marmellata preziosa classica, laboriosissima ma dal gusto antico', 3.50),
(15, 'Fragoline di bosco', ' Una selezionata marmellata campione di gusto. Ideale per dolci e crostate.', 5.20);                                                      
                                                                                                                                                                               

-- --------------------------------------------------------                                                                                                            
                                                                                                                                      
--
-- Struttura della tabella `RigheOrdini`
--
CREATE TABLE IF NOT EXISTS `RigheOrdini` (
  `prodotto_id` bigint(20) unsigned NOT NULL,
  `ordine_id` bigint(20) unsigned NOT NULL,
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quantita` int(2) unsigned DEFAULT NULL,
  `dimensione` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `RigheOrdini_ibfk_1` (`prodotto_id`),
  KEY `RigheOrdini_ibfk_2` (`ordine_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=278 ;

--
-- Vincoli per la tabella `Ordini`
--
ALTER TABLE `Ordini`
  ADD CONSTRAINT `Ordini_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `Clienti` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Ordini_ibfk_2` FOREIGN KEY (`operatore_id`) REFERENCES `Operatori` (`id`) ON UPDATE CASCADE;

--
-- Vincoli per la tabella `RigaOrdine`
--
ALTER TABLE `RigheOrdini`
  ADD CONSTRAINT `RigheOrdini_ibfk_1` FOREIGN KEY (`prodotto_id`) REFERENCES `Prodotti` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `RigheOrdini_ibfk_2` FOREIGN KEY (`ordine_id`) REFERENCES `Ordini` (`id`) ON UPDATE CASCADE;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
