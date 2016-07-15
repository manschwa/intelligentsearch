<?php

class IndexObject_Forumentry extends IndexObject
{

    const RATING_FORUMENTRY = 0.6;
    const RATING_FORUMAUTHOR = 0.7;
    const RATING_FORUMENTRY_TITLE = 0.75;

    const BELONGS_TO = array('seminar', 'institute');

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
        $selects[_('Veranstaltungen')] = $this->getSeminars();
        ksort($selects);
        return $selects;
    }

    /**
     * @return array
     */
    public function getSearchParams()
    {
        $search_params = array();
        $search_params['columns']   = ', forum_entries.seminar_id, forum_entries.user_id ';
        $search_params['joins']     = ' LEFT JOIN forum_entries ON forum_entries.topic_id = search_object.range_id ';
        $search_params['conditions'] = ($_SESSION['global_search']['selects'][_('Veranstaltungen')] ? (" AND Seminar_id ='" . $_SESSION['global_search']['selects'][_('Veranstaltungen')] . "' ") : ' ')
            . ($GLOBALS['perm']->have_perm('root') ? '' : " AND " . $this->getCondition());
        return $search_params;
    }

    /**
     * @return array
     */
    public function getSeminars()
    {
        $seminars = array();
        if ($GLOBALS['perm']->have_perm('admin')) {
            $statement = DBManager::get()->prepare("SELECT Seminar_id, Name FROM seminare LIMIT 30"); //OBACHT im Livesystem, zu viele Veranstaltungen
        } elseif (isset($GLOBALS['user'])) {
            $statement = DBManager::get()->prepare("SELECT Seminar_id, Name FROM seminar_user JOIN seminare USING (Seminar_id) where user_id=:user_id");
            $statement->bindParam(':user_id', $GLOBALS['user']->id);
        }
        $statement->execute();

        $seminars[''] = _('Alle Veranstaltungen');
        while ($object = $statement->fetch(PDO::FETCH_ASSOC)) {
            $seminars[$object['Seminar_id']] = $object['Name'];
        }
        ksort($seminars);
        return $seminars;
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

    /**
     * @param $type string
     * @return bool
     */
    public static function belongsTo($type)
    {
        return in_array($type, self::BELONGS_TO);
    }

}
