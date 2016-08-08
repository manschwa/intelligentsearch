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
        StudipAutoloader::addAutoloadPath(__ROOT__ . '/models');
        IndexManager::sqlIndex();
    }

    function down ()
    {

    }
}
