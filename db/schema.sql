-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- Host: prueba.kamikazelab.com
-- Generation Time: Aug 05, 2011 at 02:44 PM
-- Server version: 5.1.53
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `short_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `instance`
--

CREATE TABLE IF NOT EXISTS `instance` (
  `instance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url_id` int(10) unsigned NOT NULL,
  `strkey` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT NULL,
  `max_hits` int(10) unsigned DEFAULT NULL,
  `notify_email` varchar(255) DEFAULT NULL,
  `notes` text,
  `notifications` tinyint(1) DEFAULT '0',
  `validation_code` varchar(32) DEFAULT NULL,
  `private_stats` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`instance_id`),
  UNIQUE KEY `strkey` (`strkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=80 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `instance_id` int(10) unsigned NOT NULL,
  `type` enum('create','access') DEFAULT NULL,
  `outcome` enum('ok','error') DEFAULT NULL,
  `tstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `client_ip` varchar(25) DEFAULT NULL,
  `client_host` varchar(255) DEFAULT NULL,
  `client_agent` text,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=190 ;

-- --------------------------------------------------------

--
-- Table structure for table `url`
--

CREATE TABLE IF NOT EXISTS `url` (
  `url_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`url_id`),
  UNIQUE KEY `url` (`url`(100))
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;