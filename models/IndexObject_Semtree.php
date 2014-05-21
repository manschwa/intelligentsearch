<?php

/**
 * Description of IndexObject_Seminar
 *
 * @author intelec
 */
class IndexObject_Semtree {

    const RATING_SEMTREE = 1.0;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT sem_tree_id, 'semtree', name, CONCAT('sem_portal.php?start_item_id',sem_tree_id) FROM sem_tree");
        IndexManager::createIndex("SELECT object_id, name, " . self::RATING_SEMTREE . " FROM sem_tree " . IndexManager::createJoin('sem_tree_id'));
    }

    public static function getName() {
        return _('Vorlesungsverzeichnis');
    }

    public static function isVisible($object) {
        return true;
    }

}
