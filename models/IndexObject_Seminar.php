<?php

class IndexObject_Seminar {
    
    const RATING_SEMINAR = 0.8;
    const RATING_SEMINAR_DOZENT = 0.75;
    const RATING_SEMINAR_SUBTITLE = 0.7;
    const RATING_SEMINAR_OTHER = 0.6;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT seminar_id, 'seminar', CONCAT_WS(' ', sd.name, s.Veranstaltungsnummer, s.name) as title, CONCAT('dispatch.php/course/overview?cid=',s.seminar_id) as link FROM seminare s JOIN semester_data sd ON s.start_time BETWEEN sd.beginn AND sd.ende");
        IndexManager::createIndex("SELECT object_id, Name, " . IndexManager::relevance(self::RATING_SEMINAR, 'start_time') . " FROM seminare JOIN search_object ON (seminar_id = range_id)");
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ',i.title_front, a.Vorname, a.Nachname), " . IndexManager::relevance(self::RATING_SEMINAR_DOZENT, 'start_time') . "
FROM seminare s 
JOIN search_object ON (s.seminar_id = range_id) 
JOIN seminar_user u ON (s.seminar_id = u.seminar_id AND u.status = 'dozent')
JOIN auth_user_md5 a ON (u.user_id = a.user_id)
JOIN user_info i ON (u.user_id = i.user_id)");
        IndexManager::createIndex("SELECT object_id, Untertitel, " . IndexManager::relevance(self::RATING_SEMINAR_SUBTITLE, 'start_time') . " FROM seminare JOIN search_object ON (seminar_id = range_id) WHERE Untertitel != ''");
        IndexManager::createIndex("SELECT object_id, Beschreibung, " . IndexManager::relevance(self::RATING_SEMINAR_OTHER, 'start_time') . " FROM seminare JOIN search_object ON (seminar_id = range_id) WHERE Beschreibung != ''");
        IndexManager::createIndex("SELECT object_id, Sonstiges, " . IndexManager::relevance(self::RATING_SEMINAR_OTHER, 'start_time') . " FROM seminare JOIN search_object ON (seminar_id = range_id) WHERE Sonstiges != ''");
    }
    
    public static function isVisible($object) {
        return DBManager::get()->fetchOne("SELECT 1 FROM seminare WHERE seminar_id = ? AND visible = 1", array($object->range_id));
    }
    
    public static function getName() {
        return _('Veranstaltungen');
    }

}
