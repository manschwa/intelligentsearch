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

    public function insert($event, $user)
    {
        $statement = parent::insert($event, $user);
        // insert new User into search_object
        $this->type = 'user';
        $this->title = $user['vorname'] . ' ' . $user['nachname'];
        $statement['object']->bindParam(':' . self::RANGE_ID, $user['user_id']);
        $statement['object']->bindParam(':' . self::TYPE, $this->type);
        $statement['object']->bindParam(':' . self::TITLE, $this->title);
        $statement['object']->bindParam(':' . self::RANGE2, $user['username']);
        $statement['object']->execute();

        // insert new User into search_index
        $this->text = $user['vorname'] . ' ' . $user['nachname'] . ' (' . $user['username'] . ')';
        $statement['index']->bindParam(':' . self::ID, $user['user_id']);
        $statement['index']->bindParam(':' . self::TEXT, $this->text);
        $statement['index']->execute();
    }

    public function update($event, $user)
    {
        $statement = parent::update($event, $user);
        // update search_object
        $this->title = $user['vorname'] . ' ' . $user['nachname'];
        $statement['object']->bindParam(':' . self::TITLE, $this->title);
        $statement['object']->bindParam(':' . self::RANGE2, $user['username']);
        $statement['object']->bindParam(':' . self::ID, $user['user_id']);
        $statement['object']->execute();

        // update search_index
        $this->text = $user['vorname'] . ' ' . $user['nachname'] . ' (' . $user['username'] . ')';
        $statement['index']->bindParam(':' . self::ID, $user['user_id']);
        $statement['index']->bindParam(':' . self::TEXT, $this->text);
        $statement['index']->execute();
    }

    public function delete($event, $user)
    {
        $statement = parent::delete($event, $user);
        // delete from search_index
        $statement['index']->bindParam(':' . self::ID, $user['user_id']);
        $statement['index']->execute();

        // delete from search_object
        $statement['object']->bindParam(':' . self::ID, $user['user_id']);
        $statement['object']->execute();
    }

    public function getAvatar()
    {
//        return Avatar::getAvatar($object['range_id'])->getImageTag(Avatar::SMALL);
    }
}
