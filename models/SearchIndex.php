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
class SearchIndex {
    
    public static function prepareUpdate() {
        DBManager::get()->query("ALTER TABLE search_index DISABLE KEYS");
    }

    public static function finishUpdate() {
        DBManager::get()->query("ALTER TABLE search_index ENABLE KEYS");
        DBManager::get()->query("OPTIMIZE TABLE search_index");
    }

}
