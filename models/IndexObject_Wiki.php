<?php

class IndexObject_Wiki {

    const RATING_WIKI_KEYWORD = 0.7;
    const RATING_WIKI_BODY = 0.5;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT range_id, 'wiki', keyword, version, null
FROM wiki
INNER JOIN (
select range_id, keyword,max(version) as version from wiki GROUP BY range_id, keyword) as control
USING (range_id, keyword,version)");
        IndexManager::createIndex("SELECT object_id, keyword, " . IndexManager::relevance(self::RATING_WIKI_KEYWORD, 'wiki.chdate') . "
FROM wiki JOIN search_object ON (search_object.range_id = wiki.range_id AND keyword = search_object.title AND version = search_object.range2)");
        IndexManager::createIndex("SELECT object_id, body, " . IndexManager::relevance(self::RATING_WIKI_BODY, 'wiki.chdate') . "
FROM wiki JOIN search_object ON (search_object.range_id = wiki.range_id AND keyword = search_object.title AND version = search_object.range2)");
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

}
