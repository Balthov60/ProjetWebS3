-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 17, 2018 at 09:08 AM
-- Server version: 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog-projet-web`
--
CREATE DATABASE IF NOT EXISTS `blog-projet-web` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `blog-projet-web`;

-- --------------------------------------------------------

--
-- Table structure for table `commentary`
--

DROP TABLE IF EXISTS `commentary`;
CREATE TABLE IF NOT EXISTS `commentary` (
  `idPost` int(11) NOT NULL,
  `idCommentary` int(11) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `postDate` varchar(10) NOT NULL,
  PRIMARY KEY (`idCommentary`,`idPost`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `commentary`
--

INSERT INTO `commentary` (`idPost`, `idCommentary`, `pseudo`, `content`, `postDate`) VALUES
(1, 3, 'user', 'Aliquam odio urna, eonvallis ultricies ante, id ! En Bref un chef d\'oeuvre', '2018-01-16'),
(2, 1, 'Marie Jeanne Drucker', 'Aliquam odio urna, eonvallis ultricies ante, id ! En Bref un chef d\'oeuvre !!!!!', '2019-04-08'),
(1, 4, 'admin', 'Michel-Michelle Michel Michelle MICHEL-MICHELLE !!!', '2018-01-16');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `idPost` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `content` text NOT NULL,
  `postDate` varchar(10) NOT NULL,
  `image` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`idPost`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`idPost`, `title`, `content`, `postDate`, `image`) VALUES
(1, 'Le Monologue D\'Otis', 'Mais, vous savez, moi je ne crois pas qu\'il y ait de bonne ou de mauvaise situation. Moi, si je devais rÃ©sumer ma vie aujourd\'hui avec vous, je dirais que c\'est d\'abord des rencontres, des gens qui m\'ont tendu la main, peut-Ãªtre Ã  un moment oÃ¹ je ne pouvais pas, oÃ¹ j\'Ã©tais seul chez moi. Et c\'est assez curieux de se dire que les hasards, les rencontres forgent une destinÃ©e ! Parce que quand on a le goÃ»t de la chose, quand on a le goÃ»t de la chose bien faite, le beau geste, parfois on ne trouve pas l\'interlocuteur en face, je dirais, le miroir qui vous aide Ã  avancer. Alors ce n\'est pas mon cas, comme je le disais lÃ , puisque moi au contraire, j\'ai pu ; et je dis merci Ã  la vie, je lui dis merci, je chante la vie, je danse la vie ! Je ne suis qu\'amour ! Et finalement, quand beaucoup de gens aujourd\'hui me disent... Mais comment fais-tu pour avoir cette humanitÃ© ? Eh ben je leur rÃ©ponds trÃ¨s simplement, je leur dis que c\'est ce goÃ»t de l\'amour, ce goÃ»t donc qui m\'a poussÃ© aujourd\'hui Ã  entreprendre une construction mÃ©canique, mais demain, qui sait, peut-Ãªtre seulement Ã  me mettre au service de la communautÃ©, et faire le don, le don de soi...', '2018-01-17', 'monologue.jpg'),
(2, 'Mon Super Post', 'MMaecenas imperdiet accumsan leo ultricies placerat. Donec ultrices egestas pulvinar. Curabitur neque nibh, elementum ut velit id, bibendum hendrerit nibh. In eleifend quam ut arcu commodo, at bibendum erat dictum. Mauris tortor neque, bibendum a dictum non, condimentum ac nulla. Suspendisse bibendum lectus sed semper hendrerit. Nulla a neque nibh. Fusce consequat nec arcu in auctor. Donec ut purus ut tortor fermentum eleifend. Morbi id nisl vestibulum, faucibus tortor vitae, aliquam sem. Pellentesque at sem sem. ', '2018-01-16', 'post_test_1.jpg'),
(6, '\0Elle est ou la poulette', '\r\nAliquam odio urna, blandit auctor neque sit amet, tincidunt pellentesque urna. Nullam convallis ultricies ante, id condimentum arcu elementum quis. Nulla molestie mauris non lorem tincidunt, vitae sagittis urna tincidunt. Nunc condimentum gravida diam, eget dictum leo dignissim non. Nullam commodo, ipsum vitae aliquet venenatis, quam arcu finibus libero, sed lacinia tellus quam ac augue. In porta scelerisque suscipit. Nulla at quam quam. Integer rutrum odio mollis diam posuere ultricies. Nam nisi sem, feugiat sit amet sapien ut, posuere bibendum mauris. Nullam semper, lorem a porttitor mollis, purus dolor porta sapien, sed imperdiet augue massa non orci. Duis vulputate orci auctor porta porta. Pellentesque interdum fringilla lorem vitae faucibus.\r\n\r\nSuspendisse ullamcorper nunc sodales nisl efficitur aliquet. Duis non metus ut lorem dictum viverra. Integer a sapien arcu. Morbi a felis ultrices, rhoncus ante eget, rhoncus lorem. Praesent blandit, ex tristique aliquet bibendum, dui nisi luctus ex, non consequat orci neque sed lectus. Phasellus condimentum tortor odio, non sagittis purus lacinia ac. Fusce finibus, ex ut facilisis mattis, odio urna tincidunt erat, a dapibus libero ipsum vehicula tortor. Suspendisse ipsum urna, auctor ac porttitor at, sollicitudin at erat. Sed venenatis eros at turpis cursus, nec vestibulum neque mattis. Praesent a turpis quam. Vestibulum vitae diam rhoncus, interdum purus sit amet, ornare ipsum. Sed congue urna orci, et porttitor leo scelerisque non. Curabitur quis lacus cursus, accumsan sem a, eleifend erat. ', '2018-01-16', 'post_test_2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `pseudo` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `mail` varchar(256) NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pseudo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`pseudo`, `password`, `firstname`, `lastname`, `mail`, `isAdmin`) VALUES
('admin', 'password', 'admin', 'admin', 'admin@mail.com', 1),
('user', 'password', 'user', 'user', 'user@mail.com', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
