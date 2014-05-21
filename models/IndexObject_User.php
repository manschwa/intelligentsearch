<?php

/**
 * Description of IndexObject_Seminar
 *
 * @author intelec
 */
class IndexObject_User {

    const RATING_USER = 1.0;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT user_id, 'user', CONCAT_WS(' ', Vorname, Nachname) as title, CONCAT('about.php?username=', user_id) as link FROM auth_user_md5");
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ', Vorname, Nachname), " . IndexManager::relevance(self::RATING_USER, 'last_lifesign') . " FROM auth_user_md5 JOIN user_online USING (user_id) JOIN search_object ON (user_id = range_id)");
    }

    public static function getName() {
        return _('Benutzer');
    }

}
