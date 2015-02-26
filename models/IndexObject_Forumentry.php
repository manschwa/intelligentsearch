<?php

class IndexObject_Forumentry Extends AbstractIndexObject {

    const RATING_FORUMENTRY = 0.6;
    const RATING_FORUMAUTHOR = 0.5;

    static $range_id = "forum_entries.topic_id";
    static $chdate = "forum_entries.chdate";
    static $range2 = "seminar_id";
    static $range3 = "null";
    static $visible = "seminar_id";
    static $from = "forum_entries JOIN seminare USING (seminar_id) ";
    static $condition = "seminar_id != topic_id";
    static $idTable = "forum_entries";

    static function name() {
        return "CONCAT(seminare.name, ': ', COALESCE(NULLIF(TRIM(forum_entries.name), ''), '" . _('Forumeintrag') . "'))";
    }

    public static function sqlIndex() {
        IndexManager::createIndex("SELECT object_id, content, " . self::RATING_FORUMENTRY . " FROM forum_entries " . IndexManager::createJoin('topic_id'));
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ', '" . _('Autor') . ":', vorname, nachname, username), " . self::RATING_FORUMAUTHOR . " FROM forum_entries JOIN auth_user_md5 USING (user_id) " . IndexManager::createJoin('topic_id'));
    }

    public static function getName() {
        return _('Foreneintrag');
    }

    public static function link($object) {
        return "plugins.php/coreforum/index/index/{$object['range_id']}?cid={$object['range2']}";
    }

    public static function getCondition() {
        return "EXISTS (SELECT 1 FROM seminar_user WHERE Seminar_id = range2 AND user_id = '{$GLOBALS['user']->id}')";
    }

    public static function getAvatar() {
        return Assets::img('icons/16/black/forum.png');
    }

}
