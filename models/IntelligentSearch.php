<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SearchManager
 *
 * @author intelec
 */
class IntelligentSearch {

    public $query;
    public $results;
    public $resultTypes = array();
    public $time;
    public $count;

    public function __construct($query) {
        $this->query = $query;
        $this->search();
    }

    private function search() {
        // Timecapture
        $time = microtime(1);

        $search = '%' . $this->query . '%';
        $statement = DBManager::get()->prepare("SELECT distinct(object_id),text FROM search_index WHERE text LIKE ? ORDER BY relevance DESC LIMIT 30");
        $statement->execute(array($search));
        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $object = SearchObject::find($result['object_id']);
            if (self::isVisible($object)) {
                $object->info = preg_replace_callback("/$this->query/i", function($hit) {
                    return "<span class='result'>$hit[0]</span>";
                }, $result['text']);
                $this->results[] = $object;
                $this->count++;
            }
        }

        $this->time = microtime(1) - $time;
    }

    public static function isVisible($object) {
        if ($GLOBALS['perm']->have_perm('root')) {
            return true;
        }
        $class = "IndexObject_{$object->type}";
        return $class::isVisible($object);
    }

}
