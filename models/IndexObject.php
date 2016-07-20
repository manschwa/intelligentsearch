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
            case 'file_type':
                return _('Dateitypen');
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
            $statement = DBManager::get()->prepare("SELECT Seminar_id, seminare.Name, semester_data.name FROM seminare JOIN semester_data ON seminare.start_time = semester_data.beginn " . $this->getSeminarsForSemester() . "  LIMIT 30");
        } elseif (isset($GLOBALS['user'])) {
            $statement = DBManager::get()->prepare("SELECT Seminar_id, seminare.Name, semester_data.name FROM seminar_user JOIN seminare USING (Seminar_id) JOIN semester_data ON seminare.start_time = semester_data.beginn WHERE user_id=:user_id");
            $statement->bindParam(':user_id', $GLOBALS['user']->id);
        }
        $statement->execute();

        $seminars[''] = _('Alle Veranstaltungen');
        while ($seminar = $statement->fetch(PDO::FETCH_ASSOC)) {
            $seminars[$seminar['Seminar_id']] = $seminar['Name'] . ' (' . $seminar['name'] . ')';
        }
        // clear the seminar filter if the semester filter changes and the seminar does not exist in the chosen semester
        if (!array_key_exists($_SESSION['global_search']['selects'][$this->getSelectName('seminar')], $seminars)) {
            $_SESSION['global_search']['selects'][$this->getSelectName('seminar')] = '';
        }
        asort($seminars);
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
        $sem_classes[''] = _('Alle Veranstaltungsarten');
        foreach ($GLOBALS['SEM_CLASS'] as $class_id => $class) {
            $sem_classes[$class_id] = $class['name'];
            foreach ($class->getSemTypes() as $type_id => $type) {
                $sem_classes[$class_id . '_' . $type_id] = '  ' . $type['name'];
            }
        }
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

    protected function  getSemClassString()
    {
        $classes = SemClass::getClasses();
        $v = $_SESSION['global_search']['selects'][$this->getSelectName('sem_class')];
        if ($pos = strpos($v, '_')) {
            // return just the sem_types.id (which is equal to seminare.status)
            return substr($v, $pos + 1);
        } else {
            $type_ids = array();
            // return a concatenated string containing all sem_types
            // belonging to the chosen sem_class
            foreach ($classes[$v]->getSemTypes() as $types_id => $types) {
                array_push($type_ids, $types['id']);
            }
            return implode('\', \'', $type_ids);
        }
    }

    /**
     * @return string
     */
    protected function getInstituteString()
    {
        $institutes = Institute::findByFaculty($_SESSION['global_search']['selects'][$this->getSelectName('institute')]);
        if ($institutes) {
            var_dump($institutes);
            $var = implode('\', \'', array_column($institutes, 'Institut_id'));
            // append the parent institute itself
            return $var . '\', \'' . $_SESSION['global_search']['selects'][$this->getSelectName('institute')];
        } else {
            return $_SESSION['global_search']['selects'][$this->getSelectName('institute')];
        }
    }

    /**
     * @return array
     */
    public function getFileTypes()
    {
        $file_types = array();
        $file_types[''] = _('Alle Dateitypen');
        $statement = DBManager::get()->prepare("SELECT DISTINCT dokumente.filename FROM dokumente");
        $statement->execute();
        while ($dokument = $statement->fetch(PDO::FETCH_ASSOC)) {
            $filename = $dokument['filename'];
            $pos = strrpos($filename, '.');
            if ($pos !== false) {
                $filetype = substr($filename, $pos + 1);
                $file_types[$filetype] = $filetype;
            } else {
                $file_types[_('andere')] = _('andere');
            }
        }
        array_unique($file_types);
        ksort($file_types);
        return $file_types;
    }

    private function getSeminarsForSemester()
    {
        if ($semester = $_SESSION['global_search']['selects'][$this->getSelectName('semester')]) {
            return 'WHERE seminare.start_time = ' . $semester;
        } else {
            return '';
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
