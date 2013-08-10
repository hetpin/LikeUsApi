-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 29, 2013 at 11:26 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `thanhnx`
--

-- --------------------------------------------------------

--
-- Table structure for table `like_us`
--

CREATE TABLE IF NOT EXISTS `like_us` (
  `PK_UID` int(11) NOT NULL AUTO_INCREMENT,
  `gcm_regid` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(63) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(23) COLLATE utf8_unicode_ci NOT NULL,
  `sex` tinyint(1) NOT NULL COMMENT '0: girl, 1: boy',
  `avatar` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `update_time` varchar(23) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`PK_UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
