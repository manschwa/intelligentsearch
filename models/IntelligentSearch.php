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
        //echo "SELECT search_object.* FROM (SELECT distinct(object_id),text FROM search_index WHERE text LIKE ? escape '|' ORDER BY relevance DESC) as sr JOIN search_object USING (object_id) WHERE " . self::buildWhere();die;
        $statement = DBManager::get()->prepare("SELECT search_object.*,text FROM (SELECT distinct(object_id),text FROM search_index WHERE text LIKE ? escape '|' ORDER BY relevance DESC) as sr JOIN search_object USING (object_id)" . self::buildWhere());
        $statement->execute(array($search));
        while ($object = $statement->fetch(PDO::FETCH_ASSOC)) {
            $object['info'] = preg_replace_callback("/$this->query/i", function($hit) {
                return "<span class='result'>$hit[0]</span>";
            }, htmlReady($object['text']));
            if (!$this->filter || $this->filter == $object['type']) {
                $object['link'] = self::getLink($object);
                $this->results[] = $object;
            }
            $this->resultTypes[$object['type']]++;
            $this->count++;
        }

        $this->time = microtime(1) - $time;
    }

    public static function buildWhere() {
        if ($GLOBALS['perm']->have_perm('root')) {
            return "";
        }
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $indexClass = basename($indexFile, ".php");
            $typename = explode('_', $indexClass);
            $typename = strtolower($typename[1]);
            if (method_exists($indexClass, 'getCondition')) {
            $condititions[] = " (search_object.type = '$typename' AND " . $indexClass::getCondition() . ") ";
            } else {
                $condititions[] = " (search_object.type = '$typename') ";
            }
        }
        return " WHERE ".join(' OR ', $condititions);
    }

    public static function isVisible($object) {
        if ($GLOBALS['perm']->have_perm('root')) {
            return true;
        }
        $class = self::getClass($object['type']);
        return $class::isVisible($object);
    }

    public static function getTypeName($key) {
        $class = self::getClass($key);
        return $class::getName();
    }

    private static function getClass($object) {
        return "IndexObject_" . ucfirst($object['type']);
    }

    public static function getLink($object) {
        $class = self::getClass($object);
        return $class::link($object);
    }

}
