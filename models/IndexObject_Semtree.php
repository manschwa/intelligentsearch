<?php

class IndexObject_Semtree {

    const RATING_SEMTREE = 0.1;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT sem_tree_id, 'semtree', name, null,null FROM sem_tree");
        IndexManager::createIndex("SELECT object_id, name, " . self::RATING_SEMTREE . " FROM sem_tree " . IndexManager::createJoin('sem_tree_id'));
    }

    public static function getName() {
        return _('Vorlesungsverzeichnis');
    }

    public static function link($object) {
        return "dispatch.php/search/courses?start_item_id={$object['range_id']}";
    }

    public static function getAvatar() {
        return Assets::img('icons/16/black/assessment.png');
    }

}
