<?php

class IntelligentSearch extends SearchType {

    public $query;
    public $results = array();
    public $resultTypes = array();
    public $time = 0;
    public $count = 0;
    public $error;
    public $resultsPerPage = 30;
    public $minLength = 4;

    public function query($query, $filter = null) {
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

        $statement = $this->getResultSet();
        while ($object = $statement->fetch(PDO::FETCH_ASSOC)) {

            if (!$this->filter || $this->filter == $object['type']) {
                $object['link'] = self::getLink($object);
                $this->results[] = $object;
            }
            $this->resultTypes[$object['type']] ++;
            $this->count++;
        }

        $this->time = microtime(1) - $time;
    }

    private function getResultSet($limit = null) {     
        $search = '%' . str_replace('%', '|%', $this->query) . '%';
        $statement = DBManager::get()->prepare("SELECT search_object.*,text FROM (SELECT distinct(object_id),text FROM search_index WHERE text LIKE ? escape '|' ORDER BY relevance DESC) as sr JOIN search_object USING (object_id)" . self::buildWhere() . ($limit ? " LIMIT $limit" : ""));
        $statement->execute(array($search));
        return $statement;
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
        return " WHERE " . join(' OR ', $condititions);
    }

    public static function getTypeName($key) {
        $class = self::getClass($key);
        return $class::getName();
    }

    private static function getClass($type) {
        return "IndexObject_" . ucfirst($type);
    }

    public static function getLink($object) {
        $class = self::getClass($object['type']);
        return $class::link($object);
    }

    public static function getInfo($object, $query) {
        // Cut down if info is to long
        if (strlen($object['text']) > 200) {
            $object['text'] = substr($object['text'], max(array(0, stripos($object['text'], $query, true) - 100)), 200);
        }

        return preg_replace_callback("/$query/i", function($hit) {
            return "<span class='result'>$hit[0]</span>";
        }, htmlReady($object['text']));
    }

    public function includePath() {
        return __FILE__;
    }

    public function getResults($keyword, $contextual_data = array(), $limit = PHP_INT_MAX, $offset = 0) {

        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            include $indexFile;
        }

        $this->query = $keyword;
        $stmt = $this->getResultSet(10);
        while ($object = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = array($object['object_id'], $object['title']);
        }
        return $result;
    }

    public function getAvatarImageTag($id) {
        $stmt = DBManager::get()->prepare('SELECT * FROM search_object WHERE object_id = ? LIMIT 1');
        $stmt->execute(array($id));
        $object = $stmt->fetch(PDO::FETCH_ASSOC);
        $class = self::getClass($object['type']);
        return $class::getAvatar($object);
    }

    public function getPages($current = 1) {
        return array_slice(range(1, $this->countResultPages() - 1), min(array(max(array(0, $current - 5)), $this->countResultPages() - 10)), 10);
    }

    public function countResultPages() {
        return ceil(count($this->results) / $this->resultsPerPage);
    }

}
