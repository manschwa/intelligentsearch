<?php

class IndexObject_Wiki {

    const RATING_WIKI_KEYWORD = 0.7;
    const RATING_WIKI_BODY = 0.5;

    const FILTERS = array('Wiki', 'Pedia', 'ASDF');
    const BELONGS_TO = array('seminar');

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT range_id, 'wiki', keyword, version, null
            FROM wiki
            INNER JOIN (
            select range_id, keyword,max(version) as version from wiki GROUP BY range_id, keyword) as control
            USING (range_id, keyword,version)");
                    IndexManager::createIndex("SELECT object_id, keyword, " . IndexManager::relevance(self::RATING_WIKI_KEYWORD, 'wiki.chdate') . "
                    FROM wiki JOIN search_object_temp ON (search_object_temp.range_id = wiki.range_id AND keyword = search_object_temp.title AND version = search_object_temp.range2) WHERE keyword != ''");
                    IndexManager::createIndex("SELECT object_id, body, " . IndexManager::relevance(self::RATING_WIKI_BODY, 'wiki.chdate') . "
                    FROM wiki JOIN search_object_temp ON (search_object_temp.range_id = wiki.range_id AND keyword = search_object_temp.title AND version = search_object_temp.range2) WHERE body != ''");
    }

    public static function getName() {
        return _('Wiki');
    }

    public static function link($object) {
        return "wiki.php?cid={$object['range_id']}&keyword={$object['title']}";
    }

    public static function getCondition() {
        return "EXISTS (SELECT 1 FROM seminar_user WHERE Seminar_id = range_id AND user_id = '{$GLOBALS['user']->id}')";
    }

    public static function getAvatar() {
        return Assets::img('icons/16/black/wiki.png');
    }

    /**
     * @return array
     */
    public static function getFilters()
    {
        return self::FILTERS;
    }

    /**
     * @param $type string
     * @return bool
     */
    public static function belongsTo($type)
    {
        return in_array($type, self::BELONGS_TO);
    }
}
