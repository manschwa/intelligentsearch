<?php

/**
 * IndexManager
 * 
 * Transforms an array of a definded type into a searchindex
 *
 * @author      Nobody
 */
class IndexManager {

    public static function sqlIndex($restriction = null) {
        set_time_limit(3600);
        $db = DBManager::get();
        $time = time();
        $db->query('CREATE TABLE search_object_temp LIKE search_object');
        $db->query('CREATE TABLE search_index_temp LIKE search_index');
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $type = explode('_', $indexFile);
            if (!$restriction || stripos(array_pop($type), $restriction) !== false) {
                $indexClass = basename($indexFile, ".php");
                $db->query("ALTER TABLE search_index DISABLE KEYS");
                $indexClass::sqlIndex();
                $db->query("ALTER TABLE search_index ENABLE KEYS");
                $db->query("OPTIMIZE TABLE search_index");
            }
        }
        $db->query('DROP TABLE search_object');
        $db->query('RENAME TABLE search_object_temp TO search_object');
        $db->query('DROP TABLE search_index');
        $db->query('RENAME TABLE search_index_temp TO search_index');
        return time() - $time;
    }

    /**
     * Creates search objects with an sql select
     * (range_id, type, title, link)
     * 
     * @param SQL SQL for the input
     */
    public static function createObjects($sql) {
        DBManager::get()->query("INSERT INTO search_object_temp (range_id, type, title, range2, range3) ($sql)");
    }

    public static function createIndex($sql) {
        DBManager::get()->query("INSERT INTO search_index_temp (object_id, text, relevance) ($sql)");
    }

    public static function relevance($base, $modifier) {
        return "pow( $base , ((UNIX_TIMESTAMP() - $modifier ) / 31556926)) as relevance";
    }

    public static function createJoin($on) {
        return " JOIN search_object_temp ON (search_object_temp.range_id = $on) ";
    }

}
