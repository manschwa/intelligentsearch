<?php

class IndexObject_Document Extends AbstractIndexObject {

    const RATING_DOCUMENT_TITLE = 0.9;
    const RATING_DOCUMENT_DESCRIPTION = 0.8;

    static $range_id = "dokument_id";
    static $chdate = "dokumente.chdate";
    static $range2 = "seminar_id";
    static $range3 = "range_id";
    static $visible = "seminar_id";
    static $from = "dokumente JOIN seminare USING (seminar_id)";
    static $idTable = "dokumente";
    
    static function name() {
        return "CONCAT(seminare.name, ': ', COALESCE(NULLIF(TRIM(dokumente.name), ''), '" . _('Datei') . "'))";
}

    public static function sqlIndex() {
        IndexManager::createIndex("SELECT object_id, name, " . self::RATING_DOCUMENT_TITLE . " FROM dokumente" . IndexManager::createJoin('dokument_id'));
        IndexManager::createIndex("SELECT object_id, description, " . self::RATING_DOCUMENT_DESCRIPTION . " FROM dokumente" . IndexManager::createJoin('dokument_id'));
    }

    public static function getName() {
        return _('Dokument');
    }

    public static function link($object) {
        return "folder.php?cid={$object['range2']}&data[cmd]=tree&open={$object['range_id']}#anker";
    }

    public static function getCondition() {
        return "EXISTS (SELECT 1 FROM seminar_user WHERE Seminar_id = range2 AND user_id = '{$GLOBALS['user']->id}')";
    }

    public static function getAvatar() {
        return Assets::img('icons/16/black/file.png');
    }

}
