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
        $time = time();
        if ($restriction) {
            DBManager::get()->execute('DELETE search_object,search_index FROM search_object JOIN search_index USING (object_id) WHERE type LIKE ?', array($restriction));
        } else {
            DBManager::get()->query('TRUNCATE TABLE search_object');
            DBManager::get()->query('TRUNCATE TABLE search_index');
        }
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $type = explode('_', $indexFile);
            if (!$restriction || stripos(array_pop($type), $restriction) !== false) {
                $indexClass = basename($indexFile, ".php");
                DBManager::get()->query("ALTER TABLE search_index DISABLE KEYS");
                $indexClass::sqlIndex();
                DBManager::get()->query("ALTER TABLE search_index ENABLE KEYS");
                DBManager::get()->query("OPTIMIZE TABLE search_index");
            }
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
        DBManager::get()->query("INSERT INTO search_object (range_id, type, title, range2, range3) ($sql)");
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
