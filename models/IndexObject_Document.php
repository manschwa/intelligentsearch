<?php

class IndexObject_Document extends IndexObject
{

    const RATING_DOCUMENT_TITLE = 0.9;
    const RATING_DOCUMENT_DESCRIPTION = 0.8;

    public function __construct()
    {
        $this->setName(_('Dokumente'));
        $this->setSelects($this->getSelectFilters());
    }

    public function sqlIndex()
    {
        IndexManager::createObjects("SELECT dokument_id, 'document', CONCAT(seminare.name, ': ', COALESCE(NULLIF(TRIM(dokumente.name), ''), '" . _('Datei') . "')), seminar_id, range_id FROM dokumente JOIN seminare USING (seminar_id)");
        IndexManager::createIndex("SELECT object_id, name, " . IndexManager::relevance(self::RATING_DOCUMENT_TITLE, 'dokumente.chdate') . " FROM dokumente" . IndexManager::createJoin('dokument_id') . " WHERE name != ''");
        IndexManager::createIndex("SELECT object_id, description, " . IndexManager::relevance(self::RATING_DOCUMENT_DESCRIPTION, 'dokumente.chdate') . " FROM dokumente" . IndexManager::createJoin('dokument_id'). " WHERE description != ''");
    }

    public function getLink($object)
    {
        return "folder.php?cid={$object['range2']}&data[cmd]=tree&open={$object['range_id']}#anker";
    }

    public function getCondition()
    {
        return "EXISTS (SELECT 1 FROM seminar_user WHERE Seminar_id = range2 AND user_id = '{$GLOBALS['user']->id}')";
    }

    public function getAvatar()
    {
        return Assets::img('icons/16/black/file.png');
    }

    public function getSearchParams()
    {
        $search_params = array();
        $search_params['columns']   = ', dokumente.filename ';
        $search_params['joins']     = ' LEFT JOIN dokumente ON  dokumente.dokument_id = search_object.range_id
                                        LEFT JOIN seminare ON dokumente.seminar_id = seminare.Seminar_id ';
        $search_params['conditions'] = ($_SESSION['global_search']['selects'][$this->getSelectName('institute')] ? (" AND Institut_id IN ('" . $this->getInstituteArray() . "') AND inst_perms != 'user' ") : ' ')
                                     . ($_SESSION['global_search']['selects'][$this->getSelectName('seminar')] ? (" AND seminare.Seminar_id ='" . $_SESSION['global_search']['selects'][$this->getSelectName('seminar')] . "' ") : ' ')
                                     . ($_SESSION['global_search']['selects'][$this->getSelectName('semester')] ? (" AND seminare.start_time ='" . $_SESSION['global_search']['selects'][$this->getSelectName('semester')] . "' ") : ' ')
                                     . ($_SESSION['global_search']['selects'][$this->getSelectName('file_type')] ? (" AND IFNULL(NULLIF(SUBSTRING_INDEX(dokumente.filename, '.', -1), dokumente.filename), 'andere') ='" . $_SESSION['global_search']['selects'][$this->getSelectName('file_type')] . "' ") : ' ')
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
        $selects[$this->getSelectName('seminar')] = $this->getSeminars();
        $selects[$this->getSelectName('institute')] = $this->getInstitutes();
        $selects[$this->getSelectName('file_type')] = $this->getFileTypes();
//        ksort($selects);
        return $selects;
    }

//    /**
//     * @return array
//     */
//    public function getFacetFilters()
//    {
//        $facets = array();
//        $statement = DBManager::get()->prepare("SELECT DISTINCT dokumente.filename FROM dokumente");
//        $statement->execute();
//        while ($dokument = $statement->fetch(PDO::FETCH_ASSOC)) {
//            $filename = $dokument['filename'];
//            $pos = strrpos($filename, '.');
//            if ($pos !== false) {
//                $filetype = substr($filename, $pos + 1);
//                array_push($facets, strtoupper($filetype));
//            } else {
//                array_push($facets, _('unbekannt'));
//            }
//        }
//        array_unique($facets);
//        sort($facets);
//        return $facets;
//    }
}
