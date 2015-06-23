<?php

class IndexObject_Wiki {

    const RATING_WIKI_KEYWORD = 0.7;
    const RATING_WIKI_BODY = 0.5;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT MD5(CONCAT_WS('_',keyword,range_id,version)), 'wiki', keyword, version, null, range_id
FROM wiki
INNER JOIN (
select range_id, keyword,max(version) as version from wiki GROUP BY range_id, keyword) as control
USING (range_id, keyword,version)");
        IndexManager::createIndex("SELECT object_id, keyword, " . IndexManager::relevance(self::RATING_WIKI_KEYWORD, 'wiki.chdate') . "
FROM wiki JOIN search_object_temp ON (search_object_temp.object_id = MD5(CONCAT_WS('_',wiki.keyword,wiki.range_id,wiki.version)))");
        IndexManager::createIndex("SELECT object_id, body, " . IndexManager::relevance(self::RATING_WIKI_BODY, 'wiki.chdate') . "
FROM wiki JOIN search_object_temp ON (search_object_temp.object_id = MD5(CONCAT_WS('_',wiki.keyword,wiki.range_id,wiki.version)))");
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
