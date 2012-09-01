-- phpMyAdmin SQL Dump
-- version 3.3.7deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 03 gen, 2011 at 07:26 PM
-- Versione MySQL: 5.0.77
-- Versione PHP: 5.3.3-6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `joomla16`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `eventi`
--

DROP TABLE IF EXISTS `#__eventi`;
CREATE TABLE IF NOT EXISTS `#__eventi` (
  `code` int(11) NOT NULL auto_increment,
  `datainiziale` date NOT NULL,
  `datafinale` date NOT NULL,
  `note` text,
  `titolo` varchar(100) default NULL,
  `codc` varchar(5) default NULL,
  `utente` varchar(30) default NULL,
  `datainserimento` date default NULL,
  `datamodifica` date default NULL,
  `orainiziale` time default NULL,
  `orafinale` time default NULL,
  `ip` varchar(20) default NULL,
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dump dei dati per la tabella `eventi`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `eventi_categorie`
--

DROP TABLE IF EXISTS `#__eventi_categorie`;
CREATE TABLE IF NOT EXISTS `#__eventi_categorie` (
  `codc` varchar(5) NOT NULL,
  `categoria` text,
  `globale` char(1) default NULL,
  `colore` varchar(20) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `eventi_categorie`
--

INSERT INTO `#__eventi_categorie` (`codc`, `categoria`, `globale`, `colore`) VALUES
('CS', 'Chiusura Scuola', 'V', 'rgb(230, 239, 247)'),
('F', 'Festività', 'V', 'rgb(230, 239, 247)'),
('C', 'Classe', 'F', 'rgb(255, 204, 255)'),
('G', 'Generale', 'V', 'rgb(255, 255, 170)'),
('D', 'Docenti', 'V', 'rgb(183, 252, 172)'),
('S', 'Sospensione Lezioni', 'V', 'rgb(230, 239, 170)');

-- --------------------------------------------------------

--
-- Struttura della tabella `eventi_classi`
--

DROP TABLE IF EXISTS `#__eventi_classi`;
CREATE TABLE IF NOT EXISTS `#__eventi_classi` (
  `code` int(11) default NULL,
  `classe` varchar(10) default NULL,
  `codec` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`codec`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dump dei dati per la tabella `eventi_classi`
--

