<?php

class IndexObject_Seminar extends AbstractIndexObject {

    const RATING_SEMINAR = 0.8;
    const RATING_SEMINAR_DOZENT = 0.75;
    const RATING_SEMINAR_SUBTITLE = 0.7;
    const RATING_SEMINAR_OTHER = 0.6;

    static $range_id = "seminare.seminar_id";
    static $chdate = "seminare.chdate";
    static $range2 = "null";
    static $range3 = "null";
    static $name = "CONCAT_WS(' ', sd.name, seminare.Veranstaltungsnummer, seminare.name)";
    static $visible = "IF (seminare.visible=1, 1,seminare.seminar_id)";
    static $from = "seminare JOIN semester_data sd ON seminare.start_time BETWEEN sd.beginn AND sd.ende";
    static $condition = "";
    static $idTable = "seminare";

    public static function sqlIndex() {
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ', Veranstaltungsnummer, Name), " . self::RATING_SEMINAR . " FROM seminare JOIN search_object_temp ON (seminar_id = object_id)");
        IndexManager::log("Indexed name");
        IndexManager::createIndex("SELECT object_id, Untertitel, " . self::RATING_SEMINAR_SUBTITLE . " FROM seminare JOIN search_object_temp ON (seminar_id = object_id) WHERE Untertitel != ''");
        IndexManager::log("Indexed subtitle");
        IndexManager::createIndex("SELECT object_id, Beschreibung, " . self::RATING_SEMINAR_OTHER . " FROM seminare JOIN search_object_temp ON (seminar_id = object_id) WHERE Beschreibung != ''");
        IndexManager::log("Indexed description");
        IndexManager::createIndex("SELECT object_id, Sonstiges, " . self::RATING_SEMINAR_OTHER . " FROM seminare JOIN search_object_temp ON (seminar_id = object_id) WHERE Sonstiges != ''");
        IndexManager::log("Indexed other");
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ',i.title_front, a.Vorname, a.Nachname), " . self::RATING_SEMINAR_DOZENT . "
FROM seminare s 
JOIN search_object_temp ON (s.seminar_id = object_id) 
JOIN seminar_user u ON (s.seminar_id = u.seminar_id AND u.status = 'dozent')
JOIN auth_user_md5 a ON (u.user_id = a.user_id)
JOIN user_info i ON (u.user_id = i.user_id)");
        IndexManager::log("Indexed lecturers");
    }

    public static function getCondition() {
        return "EXISTS (SELECT 1 FROM seminare WHERE Seminar_id = range_id AND visible = 1) OR EXISTS (SELECT 1 FROM seminar_user WHERE Seminar_id = range_id AND user_id = '{$GLOBALS['user']->id}')";
    }

    public static function getName() {
        return _('Veranstaltungen');
    }

    public static function link($object) {
        return "details.php?sem_id={$object['range_id']}";
    }

    public static function getAvatar($object) {
        $avatar = CourseAvatar::getAvatar($object['range_id']);
        return $avatar->is_customized() ? $avatar->getImageTag(Avatar::SMALL) : Assets::img('icons/16/black/seminar.png');
    }

}
