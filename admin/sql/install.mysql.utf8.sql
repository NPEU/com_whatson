DROP TABLE IF EXISTS `#__whatson`;

CREATE TABLE `#__whatson` (
  `id` decimal(14,4) NOT NULL DEFAULT '0.0000',
  `start_date` int(10) NOT NULL DEFAULT '0',
  `start_date_readable` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `mon` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tue` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `wed` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `thu` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fri` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
    ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TRIGGER `generate_id` BEFORE INSERT ON `#__whatson` FOR EACH ROW SET NEW.id = CONCAT(NEW.start_date, '.', NEW.user_id);