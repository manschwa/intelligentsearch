<?php

/**
 * IndexManager
 * 
 * Transforms an array of a definded type into a searchindex
 *
 * @author      Florian Bieringer <florian.bieringer@uni-passau.de>
 */
class IndexManager {

    const RATING_USER = 1.0;

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

    public static function indexCourses() {
        SearchObject::deleteType('seminar');
        $courses = DBManager::get()->query('SELECT * FROM seminare');
        while ($course = $courses->fetch(PDO::FETCH_ASSOC)) {
            self::indexCourse(Course::import($course));
        }
    }

    public static function indexUsers() {
        SearchObject::deleteType('user');
        $users = DBManager::get()->query('SELECT * FROM auth_user_md5');
        while ($user = $users->fetch(PDO::FETCH_ASSOC)) {
            self::indexUser(Course::import($user));
        }
    }

    public static function indexCourse($course) {
        $object = SearchObject::create(array(
                    'range_id' => $course->id,
                    'type' => 'seminar',
                    'title' => $course->start_semester->name . ' ' . $GLOBALS['SEM_TYPE'][$course->status]['name'] . ' ' . $course->VeranstaltungsNummer . ' ' . $course->name,
                    'link' => 'details.php?cid=' . $course->id
        ));
        SearchIndex::index($object->id, $course->VeranstaltungsNummer . " " . $course->Name, self::calculateRating(self::RATING_SEMINAR, $course->start_time));

        // Insert Dozenten into database
        $dozenten = $course->members->findBy('status', 'dozent')->pluck('user');
        $dozentenlist = join(', ', array_map(function($user) {
                    return $user->getFullname();
                }, $dozenten));
        SearchIndex::index($object->id, $dozentenlist, self::calculateRating(self::RATING_SEMINAR_DOZENT, $course->start_time));
        if ($course->Untertitel) {
            SearchIndex::index($object->id, $course->Untertitel, self::calculateRating(self::RATING_SEMINAR_SUBTITLE, $course->start_time));
        }
        if ($course->Sonstiges) {
            SearchIndex::index($object->id, $course->Sonstiges, self::calculateRating(self::RATING_SEMINAR_OTHER, $course->start_time));
        }
    }

    public static function indexUser($user) {
        $object = SearchObject::create(array(
                    'range_id' => $user->id,
                    'type' => 'user',
                    'title' => $user->getFullname(),
                    'link' => 'about.php?username=' . $user->username
        ));
        SearchIndex::index($object->id, $user->getFullname() . ' ' . $user->username, self::RATING_USER);
    }

    public static function calculateRating($initial, $timestamp) {
        $time = abs(time() - $timestamp) / 31556926;
        return pow($initial, $time);
    }

}
