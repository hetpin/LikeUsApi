-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 06, 2013 at 12:29 PM
-- Server version: 5.1.57
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `a2199108_pkhn`
--

-- --------------------------------------------------------

--
-- Table structure for table `gcm_users`
--

DROP TABLE IF EXISTS `gcm_users`;
CREATE TABLE `gcm_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gcm_regid` text,
  `name` varchar(50) NOT NULL,
  `password` varchar(127) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `token` varchar(255) DEFAULT NULL,
  `device_type` tinyint(4) NOT NULL DEFAULT '0',
  `image` text,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `update_time` varchar(23) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(23) NOT NULL,
  `sex` tinyint(1) NOT NULL,
  `age` int(3) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `is_liked` longtext COMMENT 'list of people who are subscribing - following you',
  `is_following` longtext COMMENT 'list of people who you are subscribing - following',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `gcm_users`
--

