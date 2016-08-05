<?php
/**
 * User: manschwa
 * Date: 05.08.16
 */

define('__ROOT__', dirname(dirname(__FILE__)));

class InitialDbIndexing extends Migration {

    function description()
    {
        return 'Initial indexing of all current Stud.IP data (MySQL-Database). '
              .'Fills the tables search_object and search_index.';
    }

    function up ()
    {
        foreach (glob(__ROOT__ . "/models/Index*") as $file) {
            require $file;
        }
        IndexManager::sqlIndex();
    }

    function down ()
    {

    }
}
