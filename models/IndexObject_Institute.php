<?php

class IndexObject_Institute extends IndexObject
{

    const RATING_INSTITUTE = 1.1;

    public function __construct()
    {
        $this->setName(_('Einrichtungen'));
        $this->setFacets(array('Uni', 'FH'));
    }

    public function sqlIndex()
    {
        IndexManager::createObjects("SELECT Institut_id, 'institute', Name, null,null FROM Institute");
        IndexManager::createIndex("SELECT object_id, Name, " . self::RATING_INSTITUTE . " FROM Institute" . IndexManager::createJoin('Institut_id') . " WHERE Name != ''");
    }

    public function getLink($object)
    {
        return "institut_main.php?cid={$object['range_id']}";
    }

    public function getAvatar()
    {
//        $avatar = InstituteAvatar::getAvatar($object['range_id']);
//        return $avatar->is_customized() ? $avatar->getImageTag(Avatar::SMALL) : Assets::img('icons/16/black/institute.png');
    }

    public function getCondition()
    {
        // TODO: Implement getCondition() method.
    }
}
