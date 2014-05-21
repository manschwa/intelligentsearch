<?php

/**
 * IndexManager
 * 
 * Transforms an array of a definded type into a searchindex
 *
 * @author      Florian Bieringer <florian.bieringer@uni-passau.de>
 */
class IndexManager {

    const TIME_MALUS = 0.5;

    public static function indexAll() {
        set_time_limit(3600);
        $time = time();
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $indexClass = basename($indexFile, ".php");
            SearchIndex::prepareUpdate();
            $indexClass::fullIndex();
            SearchIndex::finishUpdate();
        }
        return time() - $time;
    }

    public static function update() {
        set_time_limit(3600);
        $GLOBALS['intelligente_suche']['time'] = time();
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $indexClass = basename($indexFile, ".php");
            SearchIndex::prepareUpdate();
            while (!IndexManager::isExhausted() && $indexClass::update());
            SearchIndex::finishUpdate();
        }
        return self::isExhausted();
    }

    public static function index($type) {
        $indexClass = "IndexObject_" . $type;
        SearchIndex::prepareUpdate();
        $indexClass::fullIndex();
        SearchIndex::finishUpdate();
    }

    public static function calculateRating($initial, $timestamp) {
        $time = abs(time() - $timestamp) / 31556926;
        return pow($initial, $time);
    }

    public static function isExhausted() {
        return (time() == $GLOBALS['intelligente_suche']['time']) ||
                (time() - $GLOBALS['intelligente_suche']['time']) / ini_get('max_execution_time') > 0.8;
    }

    public static function fast() {
        $time = time();
        DBManager::get()->query('TRUNCATE TABLE search_object');
        DBManager::get()->query('TRUNCATE TABLE search_index');
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $indexClass = basename($indexFile, ".php");
            SearchIndex::prepareUpdate();
            $indexClass::fast();
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

}
