<?php

class IndexObject_Semtree extends AbstractIndexObject {

    const RATING_SEMTREE = 0.7;

    static $range_id = "sem_tree.sem_tree_id";
    static $chdate = "unix_timestamp()";
    static $idTable = "sem_tree";

    public static function sqlIndex() {
        IndexManager::createIndex("SELECT object_id, name, " . self::RATING_SEMTREE . " FROM sem_tree " . IndexManager::createJoin('sem_tree_id'));
    }

    public static function getName() {
        return _('Vorlesungsverzeichnis');
    }

    public static function link($object) {
        return "sem_portal.php?start_item_id={$object['range_id']}";
    }

    public static function getAvatar() {
        return Assets::img('icons/16/black/assessment.png');
    }

}
