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

    public static function sqlIndex() {
        $db = DBManager::get();
        IndexManager::createObjects("SELECT seminar_id, 'seminar', CONCAT_WS(' ', sd.name, s.Veranstaltungsnummer, s.name) as title, CONCAT('details.php?cid=',s.seminar_id) as link FROM seminare s JOIN semester_data sd ON s.start_time BETWEEN sd.beginn AND sd.ende");
        IndexManager::createIndex("SELECT object_id, Name, " . IndexManager::relevance(self::RATING_SEMINAR, 'start_time') . " FROM seminare JOIN search_object ON (seminar_id = range_id)");
        $db->query("INSERT INTO search_index (object_id, text, relevance) (SELECT object_id, CONCAT_WS(' ',i.title_front, a.Vorname, a.Nachname), pow(" . self::RATING_SEMINAR_DOZENT . ", ((UNIX_TIMESTAMP()-start_time)/31556926)) as relevance 
FROM seminare s 
JOIN search_object ON (s.seminar_id = range_id) 
JOIN seminar_user u ON (s.seminar_id = u.seminar_id AND u.status = 'dozent')
JOIN auth_user_md5 a ON (u.user_id = a.user_id)
JOIN user_info i ON (u.user_id = i.user_id))");
        $db->query("INSERT INTO search_index (object_id, text, relevance) (SELECT object_id, Untertitel, pow(" . self::RATING_SEMINAR_SUBTITLE . ", ((UNIX_TIMESTAMP()-start_time)/31556926)) as relevance FROM seminare JOIN search_object ON (seminar_id = range_id) WHERE Untertitel != '')");
        $db->query("INSERT INTO search_index (object_id, text, relevance) (SELECT object_id, Beschreibung, pow(" . self::RATING_SEMINAR_OTHER . ", ((UNIX_TIMESTAMP()-start_time)/31556926)) as relevance FROM seminare JOIN search_object ON (seminar_id = range_id) WHERE Beschreibung != '')");
        $db->query("INSERT INTO search_index (object_id, text, relevance) (SELECT object_id, Sonstiges, pow(" . self::RATING_SEMINAR_OTHER . ", ((UNIX_TIMESTAMP()-start_time)/31556926)) as relevance FROM seminare JOIN search_object ON (seminar_id = range_id) WHERE Sonstiges != '')");
    }

}
