<?php

class IndexObject_User {

    const RATING_USER = 0.9;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT user_id, 'user', CONCAT_WS(' ', title_front, Vorname, Nachname, title_rear), username, null, IF(search = 1 AND (visible = 'global' OR visible = 'always' OR visible = 'yes'), 1, 0) "
                . "FROM auth_user_md5 "
                . "JOIN user_info USING (user_id) "
                . "JOIN user_visibility USING (user_id)");
        IndexManager::createIndex("SELECT user_id, CONCAT_WS(' ', Vorname, Nachname, "
                . "CONCAT('(', username, ')')), "
                . self::RATING_USER." + LOG((SELECT avg(score) FROM user_info WHERE score != 0), score + 3) "
                . " FROM auth_user_md5 JOIN user_online USING (user_id) JOIN user_info USING (user_id)");
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
