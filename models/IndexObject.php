<?php
/**
 * User: manschwa
 * Date: 22.06.16
 * Time: 10:25
 */
abstract class IndexObject
{
    protected $name;
    protected $selects;
    protected $facets;

    abstract public function __construct();
    abstract public function sqlIndex();
    abstract public function getLink($object);
    abstract public function getAvatar();

    /**
     * @param $type string
     * @return string select filter name
     */
    protected function getSelectName($type)
    {
        switch ($type) {
            case 'semester':
                return _('Semester');
            case 'seminar':
                return _('Veranstaltungen');
            case 'institute':
                return _('Einrichtungen');
            case 'sem_class':
                return _('Veranstaltungsarten');
            default:
                return '';
        }
    }

    /**
     * @return array
     */
    protected function getSeminars()
    {
        $seminars = array();
        if ($GLOBALS['perm']->have_perm('admin')) {
            //OBACHT im Livesystem, zu viele Veranstaltungen
            $statement = DBManager::get()->prepare("SELECT Seminar_id, Name FROM seminare LIMIT 30");
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

    /**
     * @return array
     */
    protected function getSemesters()
    {
        // set current semester as selected
        if (!$_SESSION['global_search']['selects']) {
            $sem = Semester::findCurrent();
            $_SESSION['global_search']['selects']['Semester'] = $sem['beginn'];
        }

        $semesters = array();
        $sems = array_reverse(Semester::getAll());
        $semesters[' '] = _('Alle Semester');
        foreach ($sems as $semester) {
            $semesters[$semester['beginn']] = $semester['name'];
        }
        return $semesters;
    }

    /**
     * @return array
     */
    protected function getInstitutes()
    {
        $institutes = array();
        $insts = Institute::getInstitutes();
        $institutes[''] = _('Alle Einrichtungen');
        foreach ($insts as $institute) {
            $institutes[$institute['Institut_id']] = ($institute['is_fak'] ? '' : '  ') . $institute['Name'];
        }
        return $institutes;
    }

    /**
     * @return array
     */
    protected function getSemClasses()
    {
        $sem_classes = array();
        $statement = DBManager::get()->prepare("SELECT id, name FROM sem_classes");
        $statement->execute();
        $sem_classes[''] = _('Alle Veranstaltungsarten');
        while ($sem_class = $statement->fetch(PDO::FETCH_ASSOC)) {
            $sem_classes[$sem_class['id']] = $sem_class['name'];
        }
        ksort($sem_classes);
        return $sem_classes;
    }

    /**
     * @return string
     */
    protected function getActiveFacets()
    {
        $facets = array();
        foreach ($_SESSION['global_search']['facets'] as $facet => $value) {
            if ($value) {
                array_push($facets, $facet);
            }
        }
        if ($facets) {
            return implode('\', \'', $facets);
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    protected function getInstituteArray()
    {
        $institutes = Institute::findByFaculty($_SESSION['global_search']['selects'][$this->getSelectName('institute')]);
        if ($institutes) {
            $var = implode('\', \'', array_column($institutes, 'Institut_id'));
            // append the parent institute itself
            return $var . '\', \'' . $_SESSION['global_search']['selects'][$this->getSelectName('institute')];
        } else {
            return $_SESSION['global_search']['selects'][$this->getSelectName('institute')];
        }
    }

    /**
     * @return mixed
     */
    public function getCondition()
    {
        return null;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        if (is_string($name)) {
            $this->name = (string)$name;
        }
    }

    /**
     * @param mixed $selects
     */
    public function setSelects($selects)
    {
        if (is_array($selects)) {
            $this->selects = $selects;
        }
    }

    /**
     * @param array $facets
     */
    public function setFacets($facets)
    {
        if (is_array($facets)) {
            $this->facets = (array)$facets;
        }
    }

    /**
     * @return array
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * @return mixed
     */
    public function getSelects()
    {
        return $this->selects;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
