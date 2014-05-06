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
        //self::indexCourses();
        //self::indexUsers();
        foreach (glob(__DIR__.'/IndexObject_*') as $indexFile) {
            $indexClass = basename($indexFile, ".php");
            $indexClass::fullIndex();
        }
    }

    public static function calculateRating($initial, $timestamp) {
        $time = abs(time() - $timestamp) / 31556926;
        return pow($initial, $time);
    }

}
