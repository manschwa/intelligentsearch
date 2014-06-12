CREATE TABLE `search_index` (
  `object_id` int(11) NOT NULL,
  `text` text COLLATE latin1_german1_ci NOT NULL,
  `relevance` float NOT NULL,
  KEY `object_id` (`object_id`),
  KEY `text` (`text`(767)),
  KEY `relevance` (`relevance`)
);

CREATE TABLE `search_object` (
  `object_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `range_id` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `type` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `title` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `range2` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `range3` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  PRIMARY KEY (`object_id`),
  KEY `range_id` (`range_id`),
  KEY `type` (`type`)
);