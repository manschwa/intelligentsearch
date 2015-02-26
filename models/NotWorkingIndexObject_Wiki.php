<?php

class IndexObject_Wiki extends AbstractIndexObject {

    const RATING_WIKI_KEYWORD = 0.7;
    const RATING_WIKI_BODY = 0.5;

    static $range_id = "md5(CONCAT(range_id,keyword,version))";
    static $chdate = "wiki.chdate";
    static $range2 = "version";
    static $name = "keyword";
    static $idTable = "auth_user_md5";
    static $visible = "wiki.range_id";
    static $from = "wiki
INNER JOIN (
select range_id, keyword,max(version) as version from wiki GROUP BY range_id, keyword) as control
USING (range_id, keyword,version)";

    public static function sqlIndex() {
        /*
        IndexManager::createIndex("SELECT object_id, keyword, " . self::RATING_WIKI_KEYWORD . "
FROM wiki JOIN search_object_temp ON (search_object_temp.object_id = wiki.object_id AND keyword = search_object_temp.title AND version = search_object_temp.range2)");
        IndexManager::createIndex("SELECT object_id, body, " . self::RATING_WIKI_BODY . "
FROM wiki JOIN search_object_temp ON (search_object_temp.object_id = wiki.object_id AND keyword = search_object_temp.title AND version = search_object_temp.range2)");*/
    }
    
    public static function updateObjects($limit = 500) {
        /*
         * TODO:: MAKE IT WORK AGAIN!
         * 
         * IndexManager::createObjects("SELECT range_id, 'wiki', keyword, version, null, wiki.chdate, wiki.range_id
FROM wiki
INNER JOIN (
select range_id, keyword,max(version) as version from wiki GROUP BY range_id, keyword) as control
USING (range_id, keyword,version)");
         */
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
