<?php

class IndexObject_Resource extends AbstractIndexObject {

    const RATING_RESOURCE_NAME = 1.0;
    const RATING_RESOURCE_DESCRIPTION = 0.8;

    static $range_id = "resources_objects.resource_id";
    static $chdate = "resources_objects.chdate";
    static $range2 = "null";
    static $range3 = "null";
    static $visible = "1";
    static $from = "resources_objects";
    static $condition = "";
    static $idTable = "resources_objects";

    public static function sqlIndex() {
        IndexManager::createIndex("SELECT object_id, name, " . self::RATING_RESOURCE_NAME . " FROM resources_objects" . IndexManager::createJoin('resource_id'));
        IndexManager::createIndex("SELECT object_id, description, " . self::RATING_RESOURCE_DESCRIPTION . " FROM resources_objects" . IndexManager::createJoin('resource_id'));
    }

    public static function getName() {
        return _('Ressourcen');
    }

    public static function link($object) {
        return "resources.php?open_level={$object['range_id']}";
    }

    public static function getAvatar() {
        return Assets::img('icons/16/black/resources.png');
    }

}
