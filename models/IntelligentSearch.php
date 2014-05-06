<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IntelligentSearch
 *
 * @author intelec
 */
class IntelligentSearch {

    public static function search($string) {

        $search = '%' . $string . '%';
        $statement = DBManager::get()->prepare("SELECT distinct(object_id),text FROM search_index WHERE text LIKE ? ORDER BY relevance DESC LIMIT 30");
        $statement->execute(array($search));
        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $object = SearchObject::find($result['object_id']);
            $object->info = preg_replace_callback("/$string/i", function($hit) {
                return "<span class='result'>$hit[0]</span>";
            }, $result['text']);
            $searchResults[] = $object;
        }
        return $searchResults;
    }

    public static function count($string) {
        $search = '%' . $string . '%';
        $statement = DBManager::get()->prepare("SELECT count(*) FROM search_index WHERE text LIKE ?");
        $statement->execute(array($search));
        return $statement->fetch(PDO::FETCH_COLUMN, 0);
    }

}
