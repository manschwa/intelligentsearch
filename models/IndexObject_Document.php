<?php

class IndexObject_Document {

    const RATING_DOCUMENT_TITLE = 0.9;
    const RATING_DOCUMENT_DESCRIPTION = 0.8;

    public static function sqlIndex() {
        IndexManager::createObjects("SELECT dokument_id, 'document', CONCAT(seminare.name, ': ', COALESCE(NULLIF(TRIM(dokumente.name), ''), '" . _('Datei') . "')), seminar_id, range_id, dokumente.chdate, seminar_id FROM dokumente JOIN seminare USING (seminar_id)");
        IndexManager::createIndex("SELECT object_id, name, " . self::RATING_DOCUMENT_TITLE . " FROM dokumente" . IndexManager::createJoin('dokument_id'));
        IndexManager::createIndex("SELECT object_id, description, " . self::RATING_DOCUMENT_DESCRIPTION ." FROM dokumente" . IndexManager::createJoin('dokument_id'));
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
