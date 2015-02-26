<?php

class UseRangeId extends DBMigration {

    function up() {
        $db = DBManager::get();
        $db->exec("DROP TABLE IF EXISTS `search_index`");
        $db->exec("DROP TABLE IF EXISTS `search_object`");
        $db->exec("CREATE TABLE `search_index` (
  `object_id` varchar(32) NOT NULL,
  `text` text NOT NULL,
  `boost` float NOT NULL DEFAULT '0',
  FULLTEXT KEY `text` (`text`),
  KEY `object_id` (`object_id`)
)ENGINE=MyISAM;");
        $db->exec("CREATE TABLE `search_object` (
  `object_id` varchar(32) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `range2` varchar(32) DEFAULT NULL,
  `range3` varchar(32) DEFAULT NULL,
  `visible` varchar(32) DEFAULT NULL,
  `chdate` int(11) DEFAULT NULL,
  PRIMARY KEY (`range_id`),
  KEY `type` (`type`),
  KEY `chdate` (`chdate`)
);");
        $this->reindex();
    }

    function down() {
         $db = DBManager::get();
        $db->exec("DROP TABLE IF EXISTS `search_index`");
        $db->exec("DROP TABLE IF EXISTS `search_object`");
        $db->exec("CREATE TABLE `search_index` (
  `object_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `boost` float NOT NULL DEFAULT '0',
  FULLTEXT KEY `text` (`text`)
)ENGINE=MyISAM;");
        $db->exec("CREATE TABLE `search_object` (
  `object_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `range_id` varchar(32) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `range2` varchar(32) DEFAULT NULL,
  `range3` varchar(32) DEFAULT NULL,
  `visible` varchar(32) DEFAULT NULL,
  `chdate` int(11) DEFAULT NULL,
  PRIMARY KEY (`object_id`),
  KEY `range_id` (`range_id`),
  KEY `type` (`type`),
  KEY `chdate` (`chdate`)
);");
        $this->reindex();
    }

    function reindex() {
        StudipAutoloader::addAutoloadPath(__DIR__ . '/../models');
        IndexManager::sqlIndex();
    }

}
