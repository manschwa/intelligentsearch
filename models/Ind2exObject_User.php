<?php
/**
 * Description of IndexObject_Seminar
 *
 * @author intelec
 */
class IndexObject_User {

    const RATING_USER = 1.0;

    public static function fullIndex() {
        SearchObject::deleteType('user');
        $users = DBManager::get()->query('SELECT * FROM auth_user_md5');
        while ($user = $users->fetch(PDO::FETCH_ASSOC)) {
            self::indexUser(Course::import($user));
        }
    }

    public static function index($course) {
        $object = SearchObject::create(array(
                    'range_id' => $user->id,
                    'type' => 'user',
                    'title' => $user->getFullname(),
                    'link' => 'about.php?username=' . $user->username
        ));
        SearchIndex::index($object->id, $user->getFullname() . ' ' . $user->username, self::RATING_USER);
    }

}
