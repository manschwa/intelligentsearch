<?php

class IndexObject_User extends IndexObject
{

    // arbitrary rating value for this object class for the search presentation
    const RATING_USER = 0.9;

    public function __construct()
    {
        $this->setName(_('Personen'));
        $this->setSelects($this->getSelectFilters());
    }

    public function sqlIndex()
    {
        IndexManager::createObjects("SELECT user_id, 'user', CONCAT_WS(' ',title_front, Vorname, Nachname, title_rear), username, null FROM auth_user_md5 JOIN user_info USING (user_id)");
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ', Vorname, Nachname, "
                . "CONCAT('(', username, ')')), "
                . self::RATING_USER." + LOG((SELECT avg(score) FROM user_info WHERE score != 0), score + 3) "
                . " FROM auth_user_md5 JOIN user_online USING (user_id) JOIN user_info USING (user_id) JOIN search_object_temp ON (user_id = range_id)");
    }

    public function getLink($object)
    {
        return 'dispatch.php/profile?username=' . $object['range2'];
    }

    public function getCondition()
    {
        return " EXISTS (SELECT 1 FROM auth_user_md5 JOIN user_visibility USING (user_id) WHERE user_id = range_id AND search = 1 AND (visible = 'global' OR visible = 'always' OR visible = 'yes'))";
    }

    /**
     * @return array
     */
    public function getSelectFilters()
    {
        $selects = array();
        $selects[$this->getSelectName('institute')] = $this->getInstitutes();
        return $selects;
    }

    /**
     * @return array
     */
    public function getSearchParams()
    {
        $search_params = array();
        $search_params['columns']   = ', Institut_id ';
        $search_params['joins']     = ' LEFT JOIN user_inst ON  user_inst.user_id = search_object.range_id ';
        $search_params['conditions'] = ($_SESSION['global_search']['selects'][$this->getSelectName('institute')] ? (" AND Institut_id IN ('" . $this->getInstituteArray() . "') AND inst_perms != 'user' ") : ' ')
                                     . ($GLOBALS['perm']->have_perm('root') ? '' : " AND " . $this->getCondition());
        return $search_params;
    }

    /**
     * @param $event
     * @param $user
     */
    public function insert($event, $user)
    {
        $statement = $this->getInsertStatement();

        // insert new User into search_object
        $type = 'user';
        $title = $user['vorname'] . ' ' . $user['nachname'];
        $statement['object']->execute(array($user['user_id'], $type, $title, $user['username'], null));

        // insert new User into search_index
        $text = $title . ' (' . $user['username'] . ')';
        $statement['index']->execute(array($user['user_id'], $text));
    }

    /**
     * @param $event
     * @param $user
     */
    public function update($event, $user)
    {
        $statement = $this->getUpdateStatement();
        // update search_object
        $title = $user['vorname'] . ' ' . $user['nachname'];
        $statement['object']->execute(array($title, $user['username'], null, $user['user_id']));

        // update search_index
        $text = $title . ' (' . $user['username'] . ')';
        $statement['index']->execute(array($text, $user['user_id']));
    }

    /**
     * @param $event
     * @param $user
     */
    public function delete($event, $user)
    {
        $statement = $this->getDeleteStatement();
        // delete from search_index
        $statement['index']->execute(array($user['user_id']));

        // delete from search_object
        $statement['object']->execute(array($user['user_id']));
    }

    public function getAvatar()
    {
//        return Avatar::getAvatar($object['range_id'])->getImageTag(Avatar::SMALL);
    }
}
