<?php

class IndexObject_User {

    const RATING_USER = 1.0;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT user_id, 'user', CONCAT_WS(' ', Vorname, Nachname) as title, CONCAT('about.php?username=', username) as link FROM auth_user_md5");
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ', Vorname, Nachname, username), " . IndexManager::relevance(self::RATING_USER, 'last_lifesign') . " FROM auth_user_md5 JOIN user_online USING (user_id) JOIN search_object ON (user_id = range_id)");
    }

    public static function getName() {
        return _('Benutzer');
    }

    public static function isVisible($object) {
        return DBManager::get()->fetchOne("SELECT 1 FROM user_visibility WHERE user_id = ? AND search = 1", array($object->range_id));
    }

}
