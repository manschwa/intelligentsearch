<?php
/**
 * SearchIndex.php
 * model class for table SearchIndex
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

class SearchIndex extends SimpleORMap
{
    public function __construct($id = null)
    {
        $this->db_table = 'search_index';
        parent::__construct($id);
    }
    
    public static function index($object_id, $text, $relevance = 0.5) {
        
        // We know we have no private key
        @self::create(array(
            'object_id' => $object_id,
            'text' => $text,
            'relevance' => $relevance
        ));
    }
}
