<?php

class IndexObject_Seminar extends IndexObject
{
    const RATING_SEMINAR = 0.8;
    const RATING_SEMINAR_DOZENT = 0.75;
    const RATING_SEMINAR_SUBTITLE = 0.7;
    const RATING_SEMINAR_OTHER = 0.6;

    public function __construct()
    {
        $this->setName(_('Veranstaltungen'));
        $this->setSelects($this->getSelectFilters());
    }

    public function sqlIndex() {
        IndexManager::createObjects("SELECT seminar_id, 'seminar', CONCAT_WS(' ', sd.name, s.Veranstaltungsnummer, s.name), null,null FROM seminare s JOIN semester_data sd ON s.start_time BETWEEN sd.beginn AND sd.ende");
        IndexManager::log("Seminar objects created");
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ', Veranstaltungsnummer, Name), " . IndexManager::relevance(self::RATING_SEMINAR, 'start_time') . " FROM seminare JOIN search_object_temp ON (seminar_id = range_id)");
        IndexManager::log("Indexed name");
        IndexManager::createIndex("SELECT object_id, Untertitel, " . IndexManager::relevance(self::RATING_SEMINAR_SUBTITLE, 'start_time') . " FROM seminare JOIN search_object_temp ON (seminar_id = range_id) WHERE Untertitel != ''");
        IndexManager::log("Indexed subtitle");
        IndexManager::createIndex("SELECT object_id, Beschreibung, " . IndexManager::relevance(self::RATING_SEMINAR_OTHER, 'start_time') . " FROM seminare JOIN search_object_temp ON (seminar_id = range_id) WHERE Beschreibung != ''");
        IndexManager::log("Indexed description");
        IndexManager::createIndex("SELECT object_id, Sonstiges, " . IndexManager::relevance(self::RATING_SEMINAR_OTHER, 'start_time') . " FROM seminare JOIN search_object_temp ON (seminar_id = range_id) WHERE Sonstiges != ''");
        IndexManager::log("Indexed other");
        IndexManager::createIndex("SELECT object_id, CONCAT_WS(' ',i.title_front, a.Vorname, a.Nachname), " . IndexManager::relevance(self::RATING_SEMINAR_DOZENT, 'start_time') . "
            FROM seminare s 
            JOIN search_object_temp ON (s.seminar_id = range_id) 
            JOIN seminar_user u ON (s.seminar_id = u.seminar_id AND u.status = 'dozent')
            JOIN auth_user_md5 a ON (u.user_id = a.user_id)
            JOIN user_info i ON (u.user_id = i.user_id)");
        IndexManager::log("Indexed lecturers");
    }

    public function getLink($object) {
        return "details.php?sem_id={$object['range_id']}";
    }

    public function getCondition() {
        return " EXISTS (SELECT 1 FROM seminare WHERE Seminar_id = range_id AND visible = 1) OR EXISTS (SELECT 1 FROM seminar_user WHERE Seminar_id = range_id AND user_id = '{$GLOBALS['user']->id}')";
    }

    /**
     * @return array
     */
    public function getSearchParams()
    {
        $search_params = array();
        $search_params['columns']   = ', start_time, seminar_inst.institut_id, seminare.status ';
        $search_params['joins']     = ' JOIN seminare ON seminare.Seminar_id = search_object.range_id '
                                    . ' LEFT JOIN seminar_inst ON  seminar_inst.seminar_id = search_object.range_id ';
        $search_params['conditions'] = ($_SESSION['global_search']['selects'][$this->getSelectName('semester')] ? (" AND start_time ='" . $_SESSION['global_search']['selects'][$this->getSelectName('semester')] . "' ") : ' ')
                                     . ($_SESSION['global_search']['selects'][$this->getSelectName('institute')] ? (" AND seminar_inst.institut_id IN ('" . $this->getInstituteArray() . "') ") : ' ')
                                     . ($_SESSION['global_search']['selects'][$this->getSelectName('sem_class')] ? (" AND seminare.status ='" . $_SESSION['global_search']['selects'][$this->getSelectName('sem_class')] . "' ") : ' ')
                                     . ($GLOBALS['perm']->have_perm('root') ? '' : " AND " . $this->getCondition());
        return $search_params;
    }

    /**
     * @return array
     */
    public function getSelectFilters()
    {
        $selects = array();
        $selects[$this->getSelectName('semester')] = $this->getSemesters();
        $selects[$this->getSelectName('institute')] = $this->getInstitutes();
        $selects[$this->getSelectName('sem_class')] = $this->getSemClasses();
        return $selects;
    }

    /**
     * @return array
     */
    public function getSemtree()
    {
        $institutes = array();
        $statement = DBManager::get()->prepare("SELECT Institut_id, Name FROM Institute");
        $statement->execute();

        $institutes[''] = _('Alle Einrichtungen');
        while ($object = $statement->fetch(PDO::FETCH_ASSOC)) {
            $institutes[$object['Institut_id']] = $object['Name'];
        }
        krsort($institutes);
        return $institutes;
    }

    public function getAvatar() {
//        $avatar = CourseAvatar::getAvatar($object['range_id']);
//        return $avatar->is_customized() ? $avatar->getImageTag(Avatar::SMALL) : Assets::img('icons/16/black/seminar.png');
    }

}
