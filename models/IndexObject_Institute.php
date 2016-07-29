<?php

class IndexObject_Institute extends IndexObject
{

    const RATING_INSTITUTE = 1.1;

    public function __construct()
    {
        $this->setName(_('Einrichtungen'));
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

    /**
     * @param $event
     * @param $institute
     */
    public function insert($event, $institute)
    {
        $statement = parent::getInsertStatement();

        // insert new User into search_object
        $type = 'institute';
        $title = $institute['name'];
        $statement['object']->execute(array($institute['institut_id'], $type, $title, null, null));

        // insert new User into search_index
        $statement['index']->execute(array($institute['institut_id'], $title));
    }

    /**
     * @param $event
     * @param $institute
     */
    public function update($event, $institute)
    {
        $statement = $this->getUpdateStatement();
        // update search_object
        $title = $institute['name'];
        $statement['object']->execute(array($title, null, null, $institute['institut_id']));

        // update search_index
        $statement['index']->execute(array($title, $institute['institut_id']));
    }

    /**
     * @param $event
     * @param $institute
     */
    public function delete($event, $institute)
    {
        $statement = $this->getDeleteStatement();
        // delete from search_index
        $statement['index']->execute(array($institute['institut_id']));

        // delete from search_object
        $statement['object']->execute(array($institute['institut_id']));
    }
}
