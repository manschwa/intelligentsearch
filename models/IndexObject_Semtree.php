<?php

class IndexObject_Semtree extends IndexObject
{

    const RATING_SEMTREE = 0.1;

    public function __construct()
    {
        $this->setName(_('Studienbereiche'));
        $this->setFacets(array('Bla', 'Blub'));
    }

    public function sqlIndex()
    {
        IndexManager::createObjects("SELECT sem_tree_id, 'semtree', name, null,null FROM sem_tree WHERE name != ''");
        IndexManager::createIndex("SELECT object_id, name, " . self::RATING_SEMTREE . " FROM sem_tree " . IndexManager::createJoin('sem_tree_id') . " WHERE name != ''");
    }

    public function getLink($object)
    {
        return "dispatch.php/search/courses?start_item_id={$object['range_id']}";
    }

    public function getAvatar() {
        return Assets::img('icons/16/black/assessment.png');
    }

    public function getCondition()
    {
        // TODO: Implement getCondition() method.
    }
}
