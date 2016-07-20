<?php

class IndexObject_Forumentry extends IndexObject
{

    const RATING_FORUMENTRY = 0.6;
    const RATING_FORUMAUTHOR = 0.7;
    const RATING_FORUMENTRY_TITLE = 0.75;

    public function __construct()
    {
        $this->setName(_('Forumeinträge'));
        $this->setSelects($this->getSelectFilters());
    }

    public function sqlIndex()
    {
        IndexManager::createObjects("SELECT topic_id, 'forumentry', CONCAT(seminare.name, ': ', COALESCE(NULLIF(TRIM(forum_entries.name), ''), '" . _('Forumeintrag') . "')), seminar_id, null FROM forum_entries JOIN seminare USING (seminar_id) WHERE seminar_id != topic_id");
        IndexManager::createIndex("SELECT object_id, name, " . IndexManager::relevance(self::RATING_FORUMENTRY_TITLE, 'forum_entries.chdate') . " FROM forum_entries" . IndexManager::createJoin('topic_id') . " WHERE name != ''");
        IndexManager::createIndex("SELECT object_id, content, " . IndexManager::relevance(self::RATING_FORUMENTRY, 'forum_entries.chdate') . " FROM forum_entries" . IndexManager::createJoin('topic_id') . " WHERE content != ''");
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ', vorname, nachname, username), " . IndexManager::relevance(self::RATING_FORUMAUTHOR, 'forum_entries.chdate') . " FROM forum_entries JOIN auth_user_md5 USING (user_id) " . IndexManager::createJoin('topic_id'));
    }

    /**
     * @return array
     */
    public function getSelectFilters()
    {
        $selects = array();
        $selects[$this->getSelectName('semester')] = $this->getSemesters();
        $selects[$this->getSelectName('seminar')] = $this->getSeminars();
        return $selects;
    }

    /**
     * @return array
     */
    public function getSearchParams()
    {
        $search_params = array();
        $search_params['columns']   = '';
        $search_params['joins']     = ' LEFT JOIN forum_entries ON forum_entries.topic_id = search_object.range_id '
                                    . ' LEFT JOIN seminare ON seminare.Seminar_id = forum_entries.seminar_id ';
        $search_params['conditions'] = ($_SESSION['global_search']['selects'][$this->getSelectName('seminar')] ? (" AND seminare.Seminar_id ='" . $_SESSION['global_search']['selects'][$this->getSelectName('seminar')] . "' ") : ' ')
                                     . ($_SESSION['global_search']['selects'][$this->getSelectName('semester')] ? (" AND seminare.start_time ='" . $_SESSION['global_search']['selects'][$this->getSelectName('semester')] . "' ") : ' ')
                                     . ($GLOBALS['perm']->have_perm('root') ? '' : " AND " . $this->getCondition());
        return $search_params;
    }

    public function getLink($object)
    {
        return "plugins.php/coreforum/index/index/{$object['range_id']}?cid={$object['range2']}";
    }

    public function getCondition()
    {
        return "EXISTS (SELECT 1 FROM seminar_user WHERE Seminar_id = range2 AND user_id = '{$GLOBALS['user']->id}')";
    }

    public function getAvatar()
    {
        return Assets::img('icons/16/black/forum.png');
    }
}
