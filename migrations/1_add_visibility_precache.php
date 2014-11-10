<?php
class AddVisibilityPrecache extends DBMigration
{
	function up()
	{
		DBManager::Get()->exec("ALTER TABLE `search_object` ADD COLUMN visible VARCHAR(32)");
	}

	function down()
	{
		DBManager::Get()->exec("ALTER TABLE `search_object` DROP COLUMN visible");
	}
}