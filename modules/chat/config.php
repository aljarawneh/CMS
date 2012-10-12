<?php defined('EF5_SYSTEM') || exit;
/*---------------------------------------------------------------+
| eXtreme-Fusion - Content Management System - version 5         |
+----------------------------------------------------------------+
| Copyright (c) 2005-2012 eXtreme-Fusion Crew                	 |
| http://extreme-fusion.org/                               		 |
+----------------------------------------------------------------+
| This product is licensed under the BSD License.				 |
| http://extreme-fusion.org/ef5/license/						 |
+---------------------------------------------------------------*/

$mod_info = array(
	'title' => 'Chat',
	'description' => 'Chat dla użytkowników strony',
	'developer' => 'eXtreme Crew',
	'support' => 'http://extreme-fusion.org',
	'version' => '1.0',
	'dir' => 'chat'
);

$new_table[1] = array(
	"chat_messages",
	"(
		`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		`user_id` MEDIUMINT(8) NOT NULL DEFAULT '0',
		`content` TEXT NOT NULL,
		`datestamp` INT(10) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`)
	) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;"
);

$new_table[2] = array(
	"chat_settings",
	"(
		`refresh` SMALLINT(5) NOT NULL DEFAULT '0',
		`life_messages` SMALLINT(5) NOT NULL DEFAULT '0',
		PRIMARY KEY (`refresh`)
	) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;"
);

$drop_table[1] = "chat_messages";
$drop_table[2] = "chat_settings";

$new_row[1] = array(
	"chat_settings", 
	"(`refresh`, `life_messages`) VALUES (5, 0)"
);

$admin_page[1] = array(
	'title' => 'Chat',
	'image' => 'images/chat.png',
	'page' => 'admin/chat.php',
	'perm' => 'admin'
);

$perm[1] = array(
	'name' => 'admin',
	'desc' => 'Zarządzanie modułem'
);