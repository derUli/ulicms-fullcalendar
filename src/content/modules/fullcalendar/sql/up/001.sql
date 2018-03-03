CREATE TABLE IF NOT EXISTS `{prefix}events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `start` bigint(20) NOT NULL,
  `end` bigint(20) NOT NULL,
  `url` text NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;