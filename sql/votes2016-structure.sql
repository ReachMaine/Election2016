-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Nov 07, 2016 at 07:47 AM
-- Server version: 5.6.28-76.1-log
-- PHP Version: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `reachvtm_zteste`
--

-- --------------------------------------------------------

--
-- Table structure for table `votes2016`
--

CREATE TABLE IF NOT EXISTS `votes2016` (
  `electionrecordid` int(11) NOT NULL AUTO_INCREMENT,
  `raceorder` int(11) NOT NULL,
  `votes` int(11) DEFAULT NULL,
  `reported` bit(1) NOT NULL,
  `race` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `candidate` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `party` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `town` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precinct` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `r_d` int(11) NOT NULL,
  `r_g` int(11) NOT NULL,
  `r_r` int(11) NOT NULL,
  `r_u` int(11) NOT NULL,
  `pct_d` decimal(4,2) NOT NULL,
  `pct_r` decimal(4,2) NOT NULL,
  `pct_u` decimal(4,2) NOT NULL,
  PRIMARY KEY (`electionrecordid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1319 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
