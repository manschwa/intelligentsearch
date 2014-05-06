<?php

/**
 * IndexManager
 * 
 * Transforms an array of a definded type into a searchindex
 *
 * @author      Florian Bieringer <florian.bieringer@uni-passau.de>
 */
class IndexManager {

    const RATING_RESOURCE = 0.6;
    const RATING_DOCUMENT = 0.55;
    const RATING_POSTING = 0.5;
    const TIME_MALUS = 0.5;

    public static function indexAll() {
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $indexClass = basename($indexFile, ".php");
            SearchIndex::prepareUpdate();
            $indexClass::fullIndex();
            SearchIndex::finishUpdate();
        }
    }

    public static function update() {
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $indexClass = basename($indexFile, ".php");
            SearchIndex::prepareUpdate();
            $indexClass::update();
            SearchIndex::finishUpdate();
        }
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

}
