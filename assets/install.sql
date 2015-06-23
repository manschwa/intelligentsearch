CREATE TABLE IF NOT EXISTS `search_index` (
  `object_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `relevance` float NOT NULL,
  KEY `object_id` (`object_id`),
  KEY `relevance` (`relevance`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS`search_object` (
  `object_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `range_id` varchar(32) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `range2` varchar(32) DEFAULT NULL,
  `range3` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`object_id`),
  KEY `range_id` (`range_id`),
  KEY `type` (`type`)
);