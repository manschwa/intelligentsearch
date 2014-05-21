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
    public $results = array();
    public $resultTypes = array();
    public $time = 0;
    public $count = 0;
    public $error;
    public $resultsPerPage = 30;
    public $minLength = 4;

    public function __construct($query) {
        $this->query = $query;
        if (strlen($query) >= $this->minLength) {
            $this->search();
        } else {
            $this->error = _('Der eingegebene Suchbegriff ist zu kurz');
        }
    }

    public function resultPage($page = 0) {
        return array_slice($this->results, $page * $this->resultsPerPage, $this->resultsPerPage);
    }

    private function search() {
        // Timecapture
        $time = microtime(1);

        $search = '%' . $this->query . '%';
        $statement = DBManager::get()->prepare("SELECT distinct(object_id),text FROM search_index WHERE text LIKE ? ORDER BY relevance DESC");
        $statement->execute(array($search));
        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $object = SearchObject::find($result['object_id']);
            if (self::isVisible($object)) {
                $object->info = preg_replace_callback("/$this->query/i", function($hit) {
                    return "<span class='result'>$hit[0]</span>";
                }, $result['text']);
                $this->results[] = $object;
                $this->resultTypes[$object->type] ++;
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

    public static function getTypeName($key) {
        $class = "IndexObject_$key";
        return $class::getName();
    }

}
