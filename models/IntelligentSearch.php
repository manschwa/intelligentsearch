<?php

class IntelligentSearch {

    public $query;
    public $results = array();
    public $resultTypes = array();
    public $time = 0;
    public $count = 0;
    public $error;
    public $resultsPerPage = 30;
    public $minLength = 4;

    public function __construct($query, $filter = null) {
        $this->query = $query;
        $this->filter = $filter;
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

        $search = '%' . str_replace('%', '|%', $this->query) . '%';
        $statement = DBManager::get()->prepare("SELECT distinct(object_id),text FROM search_index WHERE text LIKE ? escape '|' ORDER BY relevance DESC");
        $statement->execute(array($search));
        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $object = SearchObject::find($result['object_id']);
            if (self::isVisible($object)) {
                $object->info = preg_replace_callback("/$this->query/i", function($hit) {
                    return "<span class='result'>$hit[0]</span>";
                }, $result['text']);
                if (!$this->filter || $this->filter == $object->type) {
                    $this->results[] = $object;
                }
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
        $class = self::getClass($object->type);
        return $class::isVisible($object);
    }

    public static function getTypeName($key) {
        $class = self::getClass($key);
        return $class::getName();
    }
    
    private static function getClass($key) {
        return "IndexObject_".ucfirst($key);
    }

}
