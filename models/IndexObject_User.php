<?php

class IndexObject_User extends AbstractIndexObject {

    const RATING_USER = 0.9;

    static $range_id = "auth_user_md5.user_id";
    static $chdate = "user_info.chdate";
    static $range2 = "username";
    static $name = "CONCAT_WS(' ', title_front, Vorname, Nachname, title_rear)";
    static $idTable = "auth_user_md5";
    static $visible = "IF (search = 1 AND auth_user_md5.visible IN ('global', 'always', 'yes'), 1, 0)";
    static $from = "auth_user_md5 
JOIN user_info USING (user_id)
JOIN user_visibility USING (user_id)";

    public static function sqlIndex() {
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ', Vorname, Nachname, "
                . "CONCAT('(', username, ')')), "
                . self::RATING_USER
                . " FROM auth_user_md5 JOIN user_online USING (user_id) JOIN user_info USING (user_id) JOIN search_object_temp ON (user_id = object_id)");
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
