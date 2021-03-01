DROP TABLE IF EXISTS `#__whatson`;

CREATE TABLE `#__whatson` (
  `date` int(10) NOT NULL DEFAULT '0',
  `date_readable` datetime DEFAULT NULL,
  `text` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`date`,`user_id`)
)
    ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
