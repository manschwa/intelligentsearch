<?php

class IndexObject_Resource {

    const RATING_RESOURCE_NAME = 1.0;
    const RATING_RESOURCE_DESCRIPTION = 0.8;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT resource_id, 'resource', name, null,null FROM resources_objects");
        IndexManager::createIndex("SELECT object_id, name, " . self::RATING_RESOURCE_NAME . " FROM resources_objects".IndexManager::createJoin('resource_id'));
        IndexManager::createIndex("SELECT object_id, description, " . self::RATING_RESOURCE_DESCRIPTION . " FROM resources_objects".IndexManager::createJoin('resource_id'));
    }

    public static function getName() {
        return _('Ressourcen');
    }

    public static function link($object) {
        return "resources.php?open_level={$object['range_id']}";
    }
    
            public static function getAvatar() {
        return null;
    }

}
