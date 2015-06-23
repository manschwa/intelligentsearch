<?php

/**
 * IndexManager
 * 
 * Transforms an array of a definded type into a searchindex
 *
 * @author      Nobody
 */
class IndexManager {

    public static $log;
    public static $current_file;
    public static $db;

    public static function sqlIndex($restriction = null) {
        set_time_limit(3600);
        self::$db = DBManager::get();
        $time = time();

        self::log("### Indexing started");

        try {

            // Purge DB
            self::$db->query('DROP TABLE IF EXISTS search_object_temp,search_index_temp,search_object_old,search_index_old');
            self::log("Database purged");

            // Create temporary tables
            self::$db->query('CREATE TABLE search_object_temp LIKE search_object');
            self::$db->query('CREATE TABLE search_index_temp LIKE search_index');
            self::log("Temporary tables created");

            // Make indexing a lot faster
            self::$db->query("ALTER TABLE search_index_temp DISABLE KEYS");
            self::log("Keys disabled");
            foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
                $type = explode('_', $indexFile);
                if (!$restriction || stripos(array_pop($type), $restriction) !== false) {
                    $indexClass = basename($indexFile, ".php");
                    self::log("Indexing $indexClass");
                    self::$current_file = $indexClass;
                    $indexClass::sqlIndex();
                    self::$current_file = "";
                    self::log("Finished $indexClass");
                }
            }
            self::log("Finished indexing");

            // Create searchindex
            self::$db->query("ALTER TABLE search_index_temp ENABLE KEYS");
            self::log("Keys enabled");

            // Swap tables
            self::$db->query('RENAME TABLE '
                    . 'search_object TO search_object_old,'
                    . 'search_object_temp TO search_object,'
                    . 'search_index TO search_index_old,'
                    . 'search_index_temp TO search_index');
            self::log("Tables swapped");

            // Drop old index
            self::$db->query('DROP TABLE search_object_old,search_index_old');
            self::log("Old tables dropped");

            $runtime = time() - $time;
            self::log("FINISHED! Runtime: " . floor($runtime / 60) . ":" . ($runtime % 60));

            // Return runtime
            return $runtime;
            
        // In case of mysql error imediately abort
        } catch (PDOException $e) {
            self::log("MySQL Error occured!");
            self::log($e->getMessage());
            self::log("Aborting");
        }
    }

    /**
     * Creates search objects with an sql select
     * (range_id, type, title, link)
     * 
     * @param SQL SQL for the input
     */
    public static function createObjects($sql) {
        self::$db->query("INSERT INTO search_object_temp (object_id, type, title, range2, range3, visible) ($sql)");
    }

    public static function createIndex($sql) {
        self::$db->query("INSERT INTO search_index_temp (object_id, text, relevance) ($sql)");
    }

    public static function relevance($base, $modifier) {
        return "pow( $base , ((UNIX_TIMESTAMP() - $modifier ) / 31556926)) as relevance";
    }

    public static function createJoin($on) {
        return " JOIN search_object_temp ON (search_object_temp.object_id = $on) ";
    }

    /**
     * Logs an indexing event in the index.log file
     * 
     * @param type $info
     */
    public static function log($info) {
        if (!self::$log) {
            Log::set('indexlog', dirname(__DIR__) . '/index.log');
            self::$log = Log::get('indexlog');
        }
        self::$log->info((self::$current_file ? : "IndexManager") . ": " . $info);
    }

}
