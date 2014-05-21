<?php

/**
 * Description of IndexObject_Seminar
 *
 * @author intelec
 */
class IndexObject_Institute {

    const RATING_INSTITUTE = 1.1;
    
    public static function sqlIndex() {
        IndexManager::createObjects("SELECT Institut_id, 'institute', Name, CONCAT('institut_main.php?cid=', Institut_id) FROM Institute");
        IndexManager::createIndex("SELECT object_id, Name, ".self::RATING_INSTITUTE." FROM Institute".IndexManager::createJoin('Institut_id'));
    }
}
