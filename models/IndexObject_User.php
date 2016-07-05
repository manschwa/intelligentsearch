<?php

class IndexObject_User extends IndexObject
{

    // arbitrary rating value for this object class for the search presentation
    const RATING_USER = 0.9;

    public function __construct()
    {
        $this->setName(_('Benutzer'));
        $this->setSelects($this->getSelectFilters());
        $this->setFacets($this->getFacetFilters());
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
        return "EXISTS (SELECT 1 FROM auth_user_md5 JOIN user_visibility USING (user_id) WHERE user_id = range_id AND search = 1 AND (visible = 'global' OR visible = 'always' OR visible = 'yes'))";
    }

    /**
     * @return array
     */
    public function getSelectFilters()
    {
        $selects = array();
        $selects[_('Einrichtungen')] = $this->getInstitutes();
        $selects[_('Vorlesungen')] = $this->getSeminars();
        ksort($selects);
        return $selects;
    }

    /**
     * @return array
     */
    public function getInstitutes()
    {
        $institutes = array();
        if ($GLOBALS['perm']->have_perm('admin')) {
            $statement = DBManager::get()->prepare("SELECT Institut_id, Name FROM Institute");
        } elseif (isset($GLOBALS['user'])) {
            $statement = DBManager::get()->prepare("SELECT Institut_id, Name FROM user_inst JOIN Institute USING (Institut_id) where user_id=:user_id");
            $statement->bindParam(':user_id', $GLOBALS['user']->id);
        }
        $statement->execute();

        $institutes[''] = _('Alle Einrichtungen');
        while ($object = $statement->fetch(PDO::FETCH_ASSOC)) {
            $institutes[$object['Institut_id']] = $object['Name'];
        }
        asort($institutes);
        return $institutes;
    }

    /**
     * @return array
     */
    public function getSeminars()
    {
        $seminars = array();
        if ($GLOBALS['perm']->have_perm('admin')) {
            $statement = DBManager::get()->prepare("SELECT Seminar_id, Name FROM seminare");
        } elseif (isset($GLOBALS['user'])) {
            $statement = DBManager::get()->prepare("SELECT Seminar_id, Name FROM seminar_user JOIN seminare USING (Seminar_id) where user_id=:user_id");
            $statement->bindParam(':user_id', $GLOBALS['user']->id);
        }
        $statement->execute();

        $seminars[''] = _('Alle Vorlesungen');
        while ($object = $statement->fetch(PDO::FETCH_ASSOC)) {
            $seminars[$object['Seminar_id']] = $object['Name'];
        }
        asort($seminars);
        return $seminars;
    }

    public function getFacetFilters()
    {
        $facets = array();
        $statement = DBManager::get()->prepare("SELECT DISTINCT perms FROM auth_user_md5");
        $statement->execute();
        while ($object = $statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($facets, $object['perms']);
        }
        sort($facets);
        return $facets;
    }

    public function getAvatar()
    {
//        return Avatar::getAvatar($object['range_id'])->getImageTag(Avatar::SMALL);
    }
}
