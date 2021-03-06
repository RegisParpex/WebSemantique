SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE IF NOT EXISTS `pictionnary` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `pictionnary`;

DROP TABLE IF EXISTS `drawings`;
CREATE TABLE IF NOT EXISTS `drawings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drawingCommands` blob NOT NULL,
  `picture` blob NOT NULL,
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facebookId` varchar(16) DEFAULT NULL,
  `email` varchar(65) NOT NULL,
  `password` varchar(65) DEFAULT NULL,
  `nom` varchar(65) DEFAULT NULL,
  `prenom` varchar(65) DEFAULT NULL,
  `tel` varchar(16) DEFAULT NULL,
  `website` varchar(65) DEFAULT NULL,
  `sexe` char(1) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `ville` varchar(65) DEFAULT NULL,
  `taille` float DEFAULT NULL,
  `couleur` char(7) DEFAULT '#000000',
  `profilepic` blob,
  `role` enum('User','Modo','Admin') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `facebookId` (`facebookId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;
DROP TRIGGER IF EXISTS `deleteDrawings`;
DELIMITER //
CREATE TRIGGER `deleteDrawings` BEFORE DELETE ON `users`
 FOR EACH ROW DELETE FROM drawings WHERE drawings.userId = old.id
//
DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
