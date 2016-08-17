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
        IndexManager::createIndex("SELECT object_id, SUBSTRING_INDEX(content, '<admin_msg', 1), " . IndexManager::relevance(self::RATING_FORUMENTRY, 'forum_entries.chdate') . " FROM forum_entries" . IndexManager::createJoin('topic_id') . " WHERE content != ''");
    }

    /**
     * @return array
     */
    public function getSelectFilters()
    {
        $selects = array();
        $selects[$this->getSelectName('semester')] = $this->getSemesters();
        if (!$GLOBALS['perm']->have_perm('admin')) {
            $selects[$this->getSelectName('seminar')] = $this->getSeminars();
        }
        return $selects;
    }

    /**
     * @return array
     */
    public function getSearchParams()
    {
        $search_params = array();
        $search_params['joins']     = ' LEFT JOIN forum_entries ON forum_entries.topic_id = search_object.range_id '
                                    . ' LEFT JOIN seminare ON seminare.Seminar_id = forum_entries.seminar_id ';
        $search_params['conditions'] = ($_SESSION['global_search']['selects'][$this->getSelectName('seminar')] ? (" AND seminare.Seminar_id ='" . $_SESSION['global_search']['selects'][$this->getSelectName('seminar')] . "' ") : ' ')
                                     . ($_SESSION['global_search']['selects'][$this->getSelectName('semester')] ? (" AND seminare.start_time ='" . $_SESSION['global_search']['selects'][$this->getSelectName('semester')] . "' ") : ' ');
        return $search_params;
    }

    public function getLink($object)
    {
        return "plugins.php/coreforum/index/index/{$object['range_id']}?cid={$object['range2']}";
    }

    public static function getStaticLink($object)
    {
        return "plugins.php/coreforum/index/index/{$object['range_id']}?cid={$object['range2']}";
    }

    public static function getStaticName()
    {
        return _('Forumeinträge');
    }

    public function getCondition()
    {
        return " (EXISTS (SELECT 1 FROM seminar_user WHERE Seminar_id = range2 AND user_id = '{$GLOBALS['user']->id}')) ";
    }

    public function getAvatar()
    {
        return Assets::img('icons/16/black/forum.png');
    }

    /**
     * @param $event
     * @param $topic_id
     */
    public function insert($event, $topic_id)
    {
        $forumentry = ForumEntry::getEntry($topic_id);
        $statement = $this->getInsertStatement();

        // insert new ForumEntry into search_object
        $type = 'forumentry';
        $seminar = Course::find($forumentry['seminar_id']);
        $title = $seminar['Name'] . ': ' . $forumentry['name'];
        $statement['object']->execute(array($topic_id, $type, $title, $forumentry['seminar_id'], null));

        // insert new ForumEntry into search_index
        $statement['index']->execute(array($topic_id, $forumentry['name']));
        $statement['index']->execute(array($topic_id, ForumEntry::killEdit($forumentry['content'])));
    }

    /**
     * @param $event
     * @param $topic_id
     */
    public function update($event, $topic_id)
    {
        $this->delete($event, $topic_id);
        $this->insert($event, $topic_id);
    }

    /**
     * @param $event
     * @param $topic_id
     */
    public function delete($event, $topic_id)
    {
        $statement = $this->getDeleteStatement();
        // delete from search_index
        $statement['index']->execute(array($topic_id));

        // delete from search_object
        $statement['object']->execute(array($topic_id));
    }
}
