<?php

class IntelligentSearch extends SearchType {

    public $query;
    private $category_filter;
    public $results = array();
    public $resultTypes = array();
    public $time = 0;
    public $count = 0;
    public $error;
    private $resultsPerPage = 10;
    private $pages_shown = 10;
    private $minLength = 4;
    private $limit = 30;

    public function query($query, $category_filter = null)
    {
        $this->query = $query;
        $this->category_filter = $category_filter;
        if (($this->query && strlen($query) >= $this->minLength) || $this->category_filter) {
            $this->search($this->category_filter);
        } else {
            $this->error = _('Der eingegebene Suchbegriff ist zu kurz');
        }
    }

    public function resultPage($page = 0)
    {
        return array_slice($this->results, $page * $this->resultsPerPage, $this->resultsPerPage);
    }

    public function search($category = null)
    {
        // Timecapture
        $time = microtime(1);

        $statement = $this->getResultSet($category);

        while ($object = $statement->fetch(PDO::FETCH_ASSOC)) {
            if (!$this->category_filter || $object['type'] === $this->category_filter) {
                $class = self::getClass($object['type']);
                $obj = new $class;
                $object['name'] = $obj->getName();
                $object['link'] = $obj->getLink($object);
                $this->results[] = $object;
                $this->resultTypes[$object['type']] ++;
                $this->count++;
            }
        }

        $this->time = microtime(1) - $time;
    }

    /**
     * Method to build the SQL-query string and return the result set from the DB.
     *
     * @param $type string relevant if a category type is given for the search
     * @return object statement result set of the search
     */
    public function getResultSet($type)
    {
        // build SQL-search string which is included into the statement below if a query is given
        if ($this->query) {
            // Find out single words
            $words = $this->explodeTrim($this->query);
            // Filter for stopwords
            $words = $this->filterStopwords($words);
            // Stick em together
            $query = implode('* ', array_merge($words, array('"'.$this->query.'"')));
            $search = "(SELECT object_id, text FROM search_index"
                . " WHERE MATCH (text) AGAINST ('" . $query . "' IN BOOLEAN MODE)"
                . " GROUP BY object_id"
                . " ORDER BY SUM(MATCH (text) AGAINST ('" . $query . "' IN BOOLEAN MODE) * relevance) DESC"
                . ") as sr";
        } else {
            $search = 'search_index';
        }

        if ($type) {
            $class = $this->getClass($type);
            $object = new $class;
            if (method_exists($object, 'getSearchParams')) {
                $search_params = $object->getSearchParams();
            }
        }
        $statement = DBManager::get()->prepare("SELECT search_object.*, text " . $search_params['columns']
                . " FROM search_object JOIN " . $search . " USING (object_id) " . $search_params['joins']
                . " WHERE " . ($type ? (' type = :type' . $search_params['conditions']) : '')
                . (!$type && $this->query ? $this->buildWhere() : ' ') . " GROUP BY object_id "
                . ($this->query ? '' : " LIMIT $this->limit"));
        if ($type) {
            $statement->bindParam(':type', $type);
        }
        $statement->execute();
        return $statement;
    }

    public function buildWhere()
    {
        if ($GLOBALS['perm']->have_perm('root')) {
            return 1;
        }
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $indexClass = basename($indexFile, ".php");
            $indexObject = new $indexClass;
            $typename = explode('_', $indexClass);
            $typename = strtolower($typename[1]);
            if ($indexObject->getCondition()) {
                //TODO delete the 'AND' here and add it in the IndexObjects
                $condititions[] = " (search_object.type = '$typename' AND " . $indexObject->getCondition() . ") ";
            } else {
                $condititions[] = " (search_object.type = '$typename') ";
            }
        }
        return join(' OR ', $condititions);
    }

    /**
     * Retruns the active filter options for the given category type chosen by the user.
     *
     * @return array containing only the checked/active filters for the given category.
     */
    public function getActiveFilters()
    {
        $facets = array();
        foreach ($_SESSION['global_search']['facets'] as $facet => $value) {
            if ($_SESSION['global_search']['facets'][$facet]) {
                array_push($facets, $facet);
            }
        }
        return $facets;
    }

    public function getIndexObjectTypes()
    {
        $types = array();
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $indexClass = basename($indexFile, ".php");
            $typename = explode('_', $indexClass);
            $typename = strtolower($typename[1]);
            array_push($types, $typename);
        }
        return $types;
    }

    public function getClass($type)
    {
        return "IndexObject_" . ucfirst($type);
    }

    public function getInfo($object, $query)
    {
        // Cut down if info is to long
        if (strlen($object['text']) > 200) {
            $object['text'] = substr($object['text'], max(array(0, $this->findWordPosition($query, $object['text']) - 100)), 200);
        }

        // Split words to get them marked individual
        $words = str_replace(' ', '|', preg_quote($query));

        return preg_replace_callback("/$words/i", function($hit) {
            return "<span class='result'>$hit[0]</span>";
        }, htmlReady($object['text']));
    }

    public function includePath()
    {
        return __FILE__;
    }

    public function getResults($keyword, $contextual_data = array(), $limit = PHP_INT_MAX, $offset = 0)
    {
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

    public function getAvatarImageTag($id)
    {
        $stmt = DBManager::get()->prepare('SELECT * FROM search_object WHERE object_id = ? LIMIT 1');
        $stmt->execute(array($id));
        $object = $stmt->fetch(PDO::FETCH_ASSOC);
        $class = self::getClass($object['type']);
        return $class::getAvatar($object);
    }

    public function getPages($current = 1)
    {
        return array_slice(range(0, $this->countResultPages() - 1),
            min(array(max(array(0, $current - ($this->pages_shown / 2))),
                $this->countResultPages() - ($this->pages_shown))), $this->pages_shown);
    }

    public function countResultPages()
    {
        return ceil(count($this->results) / $this->resultsPerPage);
    }

    private function findWordPosition($words, $text)
    {
        foreach (explode(' ', $words) as $word) {
            $pos = stripos($text, $word);
            if ($pos) {
                return $pos;
            }
        }
    }

    private function explodeTrim($string)
    {
        $trimmed_words = array();
        $words = explode(' ', $string);
        foreach ($words as $word) {
            $trimmed_word = trim($word, "\t\n\r\0\x0B*-+<>@");
            if ($trimmed_word) {
                array_push($trimmed_words, $trimmed_word);
            }
        }
        return $trimmed_words;
    }

    private function filterStopwords($input)
    {
        $new = $input;
        foreach ($input as $key => $test) {
            if (in_array($test, StopWords::getStopWords())) {
                unset($new[$key]);
                continue;
            }
        }
        if ($new) {
            return $new;
        }
        return $input;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getResultTypes()
    {
        return $this->resultTypes;
    }

    /**
     * @return int
     */
    public function getTime()
    {
            return isset($this->time) ? $this->time : null;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getResultsArray()
    {
        return $this->results;
    }
}
