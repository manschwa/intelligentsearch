<?php

class IndexObject_User {

    const RATING_USER = 1.0;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT user_id, 'user', CONCAT_WS(' ', Vorname, Nachname), username, null FROM auth_user_md5");
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ', Vorname, Nachname, username), " . IndexManager::relevance(self::RATING_USER, 'last_lifesign') . " FROM auth_user_md5 JOIN user_online USING (user_id) JOIN search_object ON (user_id = range_id)");
    }

    public static function getName() {
        return _('Benutzer');
    }

    public static function link($object) {
        return 'about.php?username='.$object['range2'];
    }

}
