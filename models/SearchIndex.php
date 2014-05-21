<?php

/**
 * SearchIndex.php
 * model class for table SearchIndex
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Florian Bieringer <florian.bieringer@uni-passau.de>
 * @copyright   2014 Stud.IP Core-Group
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       3.0
 */
class SearchIndex {

    public static function search($string) {

        $search = '%' . $string . '%';
        $statement = DBManager::get()->prepare("SELECT distinct(object_id),text FROM search_index WHERE text LIKE ? ORDER BY relevance DESC LIMIT 30");
        $statement->execute(array($search));
        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $object = SearchObject::find($result['object_id']);
            if ($object) {
                $object->info = preg_replace_callback("/$string/i", function($hit) {
                    return "<span class='result'>$hit[0]</span>";
                }, $result['text']);
                $searchResults[] = $object;
            }
        }
        return $searchResults;
    }

    public static function count($string) {
        $search = '%' . $string . '%';
        $statement = DBManager::get()->prepare("SELECT count(*) FROM search_index WHERE text LIKE ?");
        $statement->execute(array($search));
        return $statement->fetch(PDO::FETCH_COLUMN, 0);
    }

    public static function prepareUpdate() {
        DBManager::get()->query("ALTER TABLE search_index DISABLE KEYS");
    }

    public static function finishUpdate() {
        DBManager::get()->query("ALTER TABLE search_index ENABLE KEYS");
        DBManager::get()->query("OPTIMIZE TABLE search_index");
    }

}
