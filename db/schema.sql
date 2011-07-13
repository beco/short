
CREATE DATABASE /*!32312 IF NOT EXISTS*/ `short` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `short`;
DROP TABLE IF EXISTS `instance`;
CREATE TABLE `instance` (
  `instance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url_id` int(10) unsigned NOT NULL,
  `strkey` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT NULL,
  `max_hits` int(10) unsigned DEFAULT NULL,
  `notify_email` varchar(255) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`instance_id`),
  UNIQUE KEY `strkey` (`strkey`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `instance_id` int(10) unsigned NOT NULL,
  `type` enum('create','access') DEFAULT NULL,
  `outcome` enum('ok','error') DEFAULT NULL,
  `tstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `client_ip` varchar(25) DEFAULT NULL,
  `client_host` varchar(255) DEFAULT NULL,
  `client_agent` text,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `url`;
CREATE TABLE `url` (
  `url_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`url_id`),
  UNIQUE KEY `url` (`url`(100))
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
