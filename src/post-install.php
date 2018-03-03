<?php
$migrator = new DBMigrator("package/fullcalendar", ModuleHelper::buildModuleRessourcePath("fullcalendar", "sql/up"));
$migrator->migrate();