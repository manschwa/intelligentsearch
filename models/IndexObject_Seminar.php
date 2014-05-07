<?php

/**
 * Description of IndexObject_Seminar
 *
 * @author intelec
 */
class IndexObject_Seminar {

    const RATING_SEMINAR = 0.8;
    const RATING_SEMINAR_DOZENT = 0.75;
    const RATING_SEMINAR_SUBTITLE = 0.7;
    const RATING_SEMINAR_OTHER = 0.6;

    public static function fullIndex() {
        $courses = DBManager::get()->query('SELECT * FROM seminare');
        while ($course = $courses->fetch(PDO::FETCH_ASSOC)) {
            self::index(Course::import($course));
        }
    }

    public static function update() {
        $courses = DBManager::get()->query('SELECT seminare.* FROM seminare WHERE seminar_id NOT IN (SELECT distinct(range_id) FROM search_object) LIMIT 50');
        while ($course = $courses->fetch(PDO::FETCH_ASSOC)) {
            self::index(Course::import($course));
        }
        return $courses->rowCount();
    }

    public static function createOrFind($course) {
        $object = SearchObject::findByRange_id($course->id);
        if ($object) {
            $object = current($object);
            SearchIndex::deleteObject($object->object_id);
        } else {
            $object = SearchObject::create(array(
                        'range_id' => $course->id,
                        'type' => 'seminar',
                        'title' => $course->start_semester->name . ' ' . $GLOBALS['SEM_TYPE'][$course->status]['name'] . ' ' . $course->VeranstaltungsNummer . ' ' . $course->name,
                        'link' => 'details.php?cid=' . $course->id
            ));
        }
        return $object;
    }

    public static function index($course) {

        $object = self::createOrFind($course);
        SearchIndex::index($object->id, $course->VeranstaltungsNummer . " " . $course->Name, IndexManager::calculateRating(self::RATING_SEMINAR, $course->start_time));

        // Insert Dozenten into database
        $dozenten = $course->members->findBy('status', 'dozent')->pluck('user');
        $dozentenlist = join(', ', array_map(function($user) {
                    return $user->getFullname();
                }, $dozenten));
        SearchIndex::index($object->id, $dozentenlist, IndexManager::calculateRating(self::RATING_SEMINAR_DOZENT, $course->start_time));
        if ($course->Untertitel) {
            SearchIndex::index($object->id, $course->Untertitel, IndexManager::calculateRating(self::RATING_SEMINAR_SUBTITLE, $course->start_time));
        }
        if ($course->Sonstiges) {
            SearchIndex::index($object->id, $course->Sonstiges, IndexManager::calculateRating(self::RATING_SEMINAR_OTHER, $course->start_time));
        }
    }

}
