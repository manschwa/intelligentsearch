<?php
/**
 * SearchObject.php
 * model class for table SearchObject
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Florian Bieringer <florian.bieringer@uni-passau.de>
 * @copyright   2014 Stud.IP Core-Group
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       3.0
 */

class SearchObject extends SimpleORMap
{
    public $info;
    
    public function __construct($id = null)
    {
        $this->db_table = 'search_object';
        $this->has_many['indices'] = array(
            "class_name" => "SearchIndex",
            "on_delete" => "delete"
        );
        parent::__construct($id);
    }
    
    public static function find($id) {
        $object = parent::find($id);
        return SearchManager::isVisible($object) ? $object : null;
    }

        public static function deleteType($type) {
        $statment = DBManager::get()->prepare('DELETE search_object,search_index FROM search_object LEFT JOIN search_index USING (object_id) WHERE type = ?');
        $statment->execute(array($type));
    }
}
