<?php

/**
 * IndexManager
 * 
 * Transforms an array of a definded type into a searchindex
 *
 * @author      Florian Bieringer <florian.bieringer@uni-passau.de>
 */
class IndexManager {

    public static function sqlIndex() {
        set_time_limit(3600);
        $time = time();
        DBManager::get()->query('TRUNCATE TABLE search_object');
        DBManager::get()->query('TRUNCATE TABLE search_index');
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $indexClass = basename($indexFile, ".php");
            SearchIndex::prepareUpdate();
            $indexClass::sqlIndex();
            SearchIndex::finishUpdate();
        }
        return time() - $time;
    }
    
    /**
     * Creates search objects with an sql select
     * (range_id, type, title, link)
     * 
     * @param SQL SQL for the input
     */
    public static function createObjects($sql) {
        DBManager::get()->query("INSERT INTO search_object (range_id, type, title, link) ($sql)");
    }
    
    public static function createIndex($sql) {
         DBManager::get()->query("INSERT INTO search_index (object_id, text, relevance) ($sql)");
    }
    
    public static function relevance($base, $modifier) {
        return "pow( $base , ((UNIX_TIMESTAMP() - $modifier ) / 31556926)) as relevance";
    }
    
    public static function createJoin($on) {
        return " JOIN search_object ON (search_object.range_id = $on) ";
    }

}
