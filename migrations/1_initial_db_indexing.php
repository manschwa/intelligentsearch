<?php
/**
 * User: manschwa
 * Date: 05.08.16
 */

class DBIndexing extends Migration {

    function description()
    {
        return 'Initial indexing of all current Stud.IP data (MySQL-Database). '
              .'Fills the tables search_object and search_index.';
    }

    function up ()
    {
        IndexManager::sqlIndex();
    }

    function down ()
    {

    }
}
