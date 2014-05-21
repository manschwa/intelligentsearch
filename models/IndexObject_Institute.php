<?php

/**
 * Description of IndexObject_Seminar
 *
 * @author intelec
 */
class IndexObject_Institute {

    const RATING_INSTITUTE = 1.1;
    
    public static function fast() {}

    public static function fullIndex() {
        $institute = DBManager::get()->query('SELECT * FROM Institute');
        while ($institut = $institute->fetch(PDO::FETCH_ASSOC)) {
            self::index(Institute::import($institut));
        }
    }

    public static function update() {
        $institute = DBManager::get()->query('SELECT Institute.* FROM Institute WHERE institut_id NOT IN (SELECT distinct(range_id) FROM search_object) LIMIT 500');
        while ($institut = $institute->fetch(PDO::FETCH_ASSOC)) {
            self::index(Institute::import($institut));
        }
        return $institute->rowCount();
    }

    public static function createOrFind($institute) {
        $object = SearchObject::findByRange_id($institute->id);
        if ($object) {
            $object = current($object);
            SearchIndex::deleteObject($object->object_id);
        } else {
            $object = SearchObject::create(array(
                        'range_id' => $institute->id,
                        'type' => 'institute',
                        'title' => $institute->name,
                        'link' => 'institut_main.php?cid=' . $institute->id
            ));
        }
        return $object;
    }

    public static function index($institute) {

        $object = self::createOrFind($institute);
        SearchIndex::index($object->id, $institute->name, self::RATING_INSTITUTE);
    }

}
