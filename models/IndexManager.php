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

    const OBJECT_TABLE = "search_object";
    const TEMP_OJECT_TABLE = "search_object_temp";
    const INDEX_TABLE = "search_index";
    const TEMP_INDEX_TABLE = "search_index_temp";

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

                    // Purge old objects
                    $indexClass::deleteObjects();

                    // Update objects
                    $indexClass::updateObjects();

                    // Index files
                    $indexClass::sqlIndex();
                    self::$current_file = "";
                    self::log("Finished $indexClass");
                }
            }
            self::log("Finished indexing");

            /*
             * OLD SWAP CODE! UNWANTED FOR INCREMENTIAL
             * 

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
             *
             */

            // Delete index entries with updated entries
            self::$db->query('DELETE ' . self::INDEX_TABLE . '.* FROM ' . self::INDEX_TABLE . ' JOIN ' . self::TEMP_INDEX_TABLE . ' USING (object_id)');
            self::log("Index purged");

            // Move new index entries
            self::$db->query('INSERT INTO ' . self::INDEX_TABLE . ' SELECT * FROM ' . self::TEMP_INDEX_TABLE);
            self::log("Index updated");

            // Move new object entries
            self::$db->query('REPLACE INTO ' . self::OBJECT_TABLE . ' SELECT * FROM ' . self::TEMP_OJECT_TABLE);
            self::log("Objects updated");

            // Drop old index
            self::$db->query('DROP TABLE ' . self::TEMP_INDEX_TABLE . ', ' . self::TEMP_OJECT_TABLE);
            self::log("Temp tables dropped");

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
        $count = self::$db->exec("INSERT IGNORE INTO " . self::TEMP_OJECT_TABLE . " (object_id, type, title, range2, range3, chdate, visible) ($sql)");
        self::log("Created $count new objects");
    }

    public static function createIndex($sql) {
        $count = self::$db->exec("INSERT INTO search_index_temp (object_id, text, boost) ($sql)");
        self::log("Created $count new indices");
    }

    public static function deleteObjects($type, $sql) {
        $count = self::$db->exec("DELETE " . self::OBJECT_TABLE . ".*, " . self::INDEX_TABLE . ".* FROM " . self::OBJECT_TABLE . " JOIN " . self::INDEX_TABLE . " USING (object_id) WHERE type = '" . $type . "' AND $sql");
        self::log("Removed $count deleted objects");
    }

    public static function createJoin($on) {
        return " JOIN " . self::TEMP_OJECT_TABLE . " ON (" . self::TEMP_OJECT_TABLE . ".object_id = $on) ";
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
