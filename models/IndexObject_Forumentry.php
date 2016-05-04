<?php

class IndexObject_Forumentry {

    const RATING_FORUMENTRY = 0.6;
    const RATING_FORUMAUTHOR = 0.7;
    const RATING_FORUMENTRY_TITLE = 0.75;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT topic_id, 'forumentry', CONCAT(seminare.name, ': ', COALESCE(NULLIF(TRIM(forum_entries.name), ''), '" . _('Forumeintrag') . "')), seminar_id, null FROM forum_entries JOIN seminare USING (seminar_id) WHERE seminar_id != topic_id");
        IndexManager::createIndex("SELECT object_id, name, " . IndexManager::relevance(self::RATING_FORUMENTRY_TITLE, 'forum_entries.chdate') . " FROM forum_entries" . IndexManager::createJoin('topic_id'));
        IndexManager::createIndex("SELECT object_id, content, " . IndexManager::relevance(self::RATING_FORUMENTRY, 'forum_entries.chdate') . " FROM forum_entries" . IndexManager::createJoin('topic_id'));
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ', vorname, nachname, username), " . IndexManager::relevance(self::RATING_FORUMAUTHOR, 'forum_entries.chdate') . " FROM forum_entries JOIN auth_user_md5 USING (user_id) " . IndexManager::createJoin('topic_id'));
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
