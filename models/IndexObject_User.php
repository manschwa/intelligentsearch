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
        return 'dispatch.php/profile?username=' . $object['range2'];
    }

    public static function getCondition() {
        return "EXISTS (SELECT 1 FROM auth_user_md5 JOIN user_visibility USING (user_id) WHERE user_id = range_id AND search = 1 AND (visible = 'global' OR visible = 'always' OR visible = 'yes'))";
    }

    public static function getAvatar($object) {
        return Avatar::getAvatar($object['range_id'])->getImageTag(Avatar::SMALL);
    }

}
