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
        return time()-$time;
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

}
