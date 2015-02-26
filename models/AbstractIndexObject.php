<?php

class AbstractIndexObject {

    static $range_id = "range_id";
    static $name = "name";
    static $chdate = "chdate";
    static $range2 = "null";
    static $range3 = "null";
    static $visible = "1";
    static $from = "";
    static $idTable = "sometable";

    public static function __callStatic($name, $arguments) {
        $vars = get_class_vars(get_called_class());
        if ($name == "from" && !$vars["from"]) {
            return static::idTable();
        }
        return $vars[$name];
    }

    public static function getTypename() {
        return array_pop(explode('_', get_called_class()));
    }
    
    public static function deleteObjects() {
        IndexManager::deleteObjects(static::getTypename(), " NOT EXISTS (SELECT 1 FROM ".static::idTable()." WHERE ".static::range_id()." = object_id LIMIT 1)");
    }

    public static function updateObjects($limit = 5000) {
        IndexManager::createObjects(static::getSelect()
                ." FROM " . static::from() 
                ." LEFT JOIN ".IndexManager::OBJECT_TABLE." ON (object_id = ".static::range_id().") "
                ." WHERE object_id IS NULL "
                ." OR ".static::chdate()." > " . IndexManager::OBJECT_TABLE . ".chdate "
                . (self::condition() ? " AND ".self::condition() : '')
                ." ORDER BY ".static::chdate()." - " . IndexManager::OBJECT_TABLE . ".chdate LIMIT $limit");
    }
    
    protected static function getSelect() {
        return "SELECT " . static::range_id() . ", '" . static::getTypename() . "', " . static::name() . ", " . static::range2() . ", " . static::range3() . ", " . static::chdate() . ", " . static::visible();
    }

    protected static function createIndex($field, $boost, $joined, $ids) {
        IndexManager::createIndex("SELECT object_id, $field, " . $boost . " FROM $joined WHERE object_id IN ($ids)");
    }

}
