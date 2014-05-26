<?php

class IndexObject_Institute {

    const RATING_INSTITUTE = 1.1;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT Institut_id, 'institute', Name, null,null FROM Institute");
        IndexManager::createIndex("SELECT object_id, Name, " . self::RATING_INSTITUTE . " FROM Institute" . IndexManager::createJoin('Institut_id'));
    }

    public static function getName() {
        return _('Einrichtung');
    }
    
    public static function link($object) {
        return "institut_main.php?cid={$object['range_id']}";
    }
    
        public static function getAvatar() {
        return null;
    }


}
