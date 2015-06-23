<?php
class AddVisibilityPrecache extends DBMigration
{
	function up()
	{
		DBManager::Get()->exec("ALTER TABLE `search_object` ADD COLUMN visible VARCHAR(32)");
                DBManager::Get()->exec("ALTER TABLE `search_object` MODIFY COLUMN object_id VARCHAR(32)");
                DBManager::Get()->exec("ALTER TABLE `search_index` MODIFY COLUMN object_id VARCHAR(32)");
	}
}