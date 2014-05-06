<?php

/**
 * Description of IndexObject_Seminar
 *
 * @author intelec
 */
class IndexObject_User {

    const RATING_USER = 1.0;

    public static function fullIndex() {
        $users = DBManager::get()->query('SELECT * FROM auth_user_md5');
        while ($user = $users->fetch(PDO::FETCH_ASSOC)) {
            self::indexUser(Course::import($user));
        }
    }

    public static function createOrFind($user) {
        $object = SearchObject::findByRange_id($user->id);
        if ($object) {
            $object = current($object);
            SearchIndex::deleteObject($object->object_id);
        } else {
            $object = SearchObject::create(array(
                        'range_id' => $user->id,
                        'type' => 'user',
                        'title' => $user->getFullname(),
                        'link' => 'about.php?username=' . $user->username
            ));
        }
        return $object;
    }

    public static function index($user) {
        $object = self::createOrFind($user);
        SearchIndex::index($object->id, $user->getFullname() . ' ' . $user->username, self::RATING_USER);
    }

}
