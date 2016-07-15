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
        return " EXISTS (SELECT 1 FROM auth_user_md5 JOIN user_visibility USING (user_id) WHERE user_id = range_id AND search = 1 AND (visible = 'global' OR visible = 'always' OR visible = 'yes'))";
    }

    /**
     * @return array
     */
    public function getSelectFilters()
    {
        $selects = array();
        $selects[$this->getSelectName('institute')] = $this->getInstitutes();
        $selects[$this->getSelectName('seminar')] = $this->getSeminars();
        ksort($selects);
        return $selects;
    }

    /**
     * @return array
     */
    public function getSearchParams()
    {
        $search_params = array();
        $search_params['columns']   = ', perms, Seminar_id, Institut_id ';
        $search_params['joins']     = ' LEFT JOIN user_inst ON  user_inst.user_id = search_object.range_id
                                        JOIN auth_user_md5 ON auth_user_md5.user_id = search_object.range_id
                                        LEFT JOIN seminar_user ON seminar_user.user_id = search_object.range_id ';
        $search_params['conditions'] = ($_SESSION['global_search']['selects'][$this->getSelectName('institute')] ? (" AND Institut_id IN ('" . $this->getInstituteArray() . "') AND inst_perms != 'user' ") : ' ')
                                     . ($_SESSION['global_search']['selects'][$this->getSelectName('seminar')] ? (" AND Seminar_id ='" . $_SESSION['global_search']['selects'][$this->getSelectName('seminar')] . "' ") : ' ')
                                     . ($this->getActiveFacets() ? (" AND perms IN ('" . $this->getActiveFacets() . "') ") : ' ')
                                     . ($GLOBALS['perm']->have_perm('root') ? '' : " AND " . $this->getCondition());
        return $search_params;
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
