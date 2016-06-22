<?php

class IndexObject_Document extends IndexObject
{

    const RATING_DOCUMENT_TITLE = 0.9;
    const RATING_DOCUMENT_DESCRIPTION = 0.8;

    const BELONGS_TO = array('seminar', 'institute');

    public function __construct()
    {
        $this->setName(_('Dokumente'));
        $this->setFacets(array('Long', 'Short', 'PDF', 'TXT'));
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

    /**
     * @param $type string
     * @return bool
     */
    public static function belongsTo($type)
    {
        return in_array($type, self::BELONGS_TO);
    }
}
