<?php
/***********************************************************
| eXtreme-Fusion 5.0 Beta 5
| Content Management System       
|
| Copyright (c) 2005-2012 eXtreme-Fusion Crew                	 
| http://extreme-fusion.org/                               		 
|
| This product is licensed under the BSD License.				 
| http://extreme-fusion.org/ef5/license/						 
***********************************************************/
$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."pages");
$result = dbquery("CREATE TABLE ".$db_prefix."pages (
	`id` SMALLINT UNSIGNED AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`content` TEXT NOT NULL DEFAULT '',
	`preview` MEDIUMTEXT NOT NULL DEFAULT '',
	`description` VARCHAR(255) NOT NULL DEFAULT '',
	`type` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
	`categories` VARCHAR(255) NOT NULL DEFAULT '',	
	`author` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
	`date` INT(10) UNSIGNED NOT NULL DEFAULT '0',	
	`url` VARCHAR(255) NOT NULL DEFAULT '',
	`thumbnail` VARCHAR(255) NOT NULL DEFAULT '',
	`settings` TINYINT UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX USING BTREE (type),
	INDEX USING BTREE (categories),
	INDEX USING BTREE (date),
	INDEX USING BTREE (url)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."pages_types");
$result = dbquery("CREATE TABLE ".$db_prefix."pages_types (
	`id` SMALLINT UNSIGNED AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`for_news_page` TINYINT UNSIGNED NOT NULL DEFAULT '0',
	`user_allow_edit_own` TINYINT UNSIGNED NOT NULL DEFAULT '0',
	`user_allow_use_wysiwyg` TINYINT UNSIGNED NOT NULL DEFAULT '0',
	`insight_groups` VARCHAR(255) NOT NULL DEFAULT '',
	`editing_groups` VARCHAR(255) NOT NULL DEFAULT '',
	`submitting_groups` VARCHAR(255) NOT NULL DEFAULT '',
	`show_preview` TINYINT UNSIGNED NOT NULL DEFAULT '1',
	`add_author` TINYINT UNSIGNED NOT NULL DEFAULT '1',
	`change_author` TINYINT UNSIGNED NOT NULL DEFAULT '0',
	`add_last_editing_date` TINYINT UNSIGNED NOT NULL DEFAULT '0',
	`change_date` TINYINT UNSIGNED NOT NULL DEFAULT '0',
	`show_author` TINYINT UNSIGNED NOT NULL DEFAULT '1',
	`show_category` TINYINT UNSIGNED NOT NULL DEFAULT '1',
	`show_date` TINYINT UNSIGNED NOT NULL DEFAULT '1',
	`show_tags` TINYINT UNSIGNED NOT NULL DEFAULT '1',
	`show_type` TINYINT UNSIGNED NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."pages_categories");
$result = dbquery("CREATE TABLE ".$db_prefix."pages_categories (
	`id` SMALLINT UNSIGNED AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`description` MEDIUMTEXT NOT NULL DEFAULT '',
	`submitting_groups` VARCHAR(255) NOT NULL DEFAULT '',
	`thumbnail` VARCHAR(255) NOT NULL DEFAULT '',
	`is_system` TINYINT UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX USING BTREE (name)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."pages_custom_settings");
$result = dbquery("CREATE TABLE ".$db_prefix."pages_custom_settings (
	`id` SMALLINT UNSIGNED AUTO_INCREMENT,
	`settings` MEDIUMTEXT NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."admin");
$result = dbquery("CREATE TABLE ".$db_prefix."admin (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`permissions` VARCHAR(127) NOT NULL DEFAULT '',
	`image` VARCHAR(120) NOT NULL DEFAULT '',
	`title` VARCHAR(50) NOT NULL DEFAULT '',
	`link` VARCHAR(100) NOT NULL DEFAULT 'reserved',
	`page` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."bbcodes");
$result = dbquery("CREATE TABLE ".$db_prefix."bbcodes (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(20) NOT NULL DEFAULT '',
	`order` SMALLINT(5) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	KEY `order` (`order`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."blacklist");
$result = dbquery("CREATE TABLE ".$db_prefix."blacklist (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`ip` VARCHAR(45) NOT NULL DEFAULT '',
	`type` TINYINT(1) UNSIGNED NOT NULL DEFAULT '4',
	`email` VARCHAR(100) NOT NULL DEFAULT '',
	`reason` TEXT NOT NULL,
	`datestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `type` (`type`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."captcha");
$result = dbquery("CREATE TABLE ".$db_prefix."captcha (
	`datestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`ip` VARCHAR(20) NOT NULL,
	`encode` VARCHAR(32) NOT NULL DEFAULT '',
	`string` VARCHAR(15) NOT NULL DEFAULT '',
	KEY `datestamp` (`datestamp`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."comments");
$result = dbquery("CREATE TABLE ".$db_prefix."comments (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`content_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`content_type` VARCHAR(20) NOT NULL DEFAULT '',
	`author` VARCHAR(50) NOT NULL DEFAULT '',
	`author_type` VARCHAR(1) NOT NULL DEFAULT '',
	`post` TEXT NOT NULL,
	`datestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`ip` VARCHAR(20) NOT NULL DEFAULT '0.0.0.0',
	PRIMARY KEY (`id`),
	KEY `datestamp` (`datestamp`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."flood_control");
$result = dbquery("CREATE TABLE ".$db_prefix."flood_control (
	`ip` VARCHAR(20) NOT NULL DEFAULT '0.0.0.0',
	`timestamp` INT(5) UNSIGNED NOT NULL DEFAULT '0',
	KEY flood_timestamp (`timestamp`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."modules");
$result = dbquery("CREATE TABLE ".$db_prefix."modules (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(100) NOT NULL DEFAULT '',
	`folder` VARCHAR(100) NOT NULL DEFAULT '',
	`category` VARCHAR(100) NOT NULL DEFAULT '',
	`version` VARCHAR(10) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."links");
$result = dbquery("CREATE TABLE ".$db_prefix."links (
	`id` int(7) unsigned NOT NULL AUTO_INCREMENT,
	`link` VARCHAR(200) NOT NULL DEFAULT '',
	`file` VARCHAR(100) NOT NULL DEFAULT '',
	`full_path` VARCHAR(200) NOT NULL DEFAULT '',
	`short_path` VARCHAR(100) NOT NULL DEFAULT '',
	`datestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `datestamp` (`datestamp`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."logs");
$result = dbquery("CREATE TABLE ".$db_prefix."logs (
	`id` int(7) unsigned NOT NULL AUTO_INCREMENT,
	`action` text NOT NULL,
	`datestamp` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."messages");
$result = dbquery("CREATE TABLE ".$db_prefix."messages (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`item_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`to` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`from` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`subject` VARCHAR(100) NOT NULL DEFAULT '',
	`message` TEXT NOT NULL,
	`read` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`datestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `datestamp` (`datestamp`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."navigation");
$result = dbquery("CREATE TABLE ".$db_prefix."navigation (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NOT NULL DEFAULT '',
	`url` VARCHAR(200) NOT NULL DEFAULT '',
	`visibility` VARCHAR(255) NOT NULL DEFAULT '',
	`position` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`window` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`order` SMALLINT(2) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."news");
$result = dbquery("CREATE TABLE ".$db_prefix."news (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`link` VARCHAR(255) NOT NULL DEFAULT '',
	`category` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`language` VARCHAR(255) NOT NULL DEFAULT 'English',
	`content` TEXT NOT NULL,
	`content_extended` TEXT NOT NULL,
	`author` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`source` TEXT NOT NULL,
	`description` TEXT NOT NULL,
	`breaks` CHAR(1) NOT NULL DEFAULT '',
	`datestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`access` VARCHAR(255) NOT NULL DEFAULT '',
	`reads` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`draft` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`sticky` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`allow_comments` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`allow_ratings` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`),
	KEY `datestamp` (`datestamp`),
	KEY `reads` (`reads`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."news_cats");
$result = dbquery("CREATE TABLE ".$db_prefix."news_cats (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NOT NULL DEFAULT '',
	`link` VARCHAR(100) NOT NULL DEFAULT '',
	`image` VARCHAR(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."notes");
$result = dbquery("CREATE TABLE ".$db_prefix."notes (
	`id` SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(64) NOT NULL,
	`note` TEXT NOT NULL,
	`author` MEDIUMINT(8) UNSIGNED NOT NULL,
	`block` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	`datestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."permissions");
$result = dbquery("CREATE TABLE ".$db_prefix."permissions (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(127) CHARACTER SET latin1 NOT NULL,
	`section` int(11) NOT NULL,
	`description` varchar(255) NOT NULL,
	`is_system` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `name` (`name`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."permissions_sections");
$result = dbquery("CREATE TABLE ".$db_prefix."permissions_sections (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(128) NOT NULL,
	`description` varchar(255) NOT NULL,
	`is_system` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."ratings");
$result = dbquery("CREATE TABLE ".$db_prefix."ratings (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`item_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`type` CHAR(1) NOT NULL DEFAULT '',
	`user` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`vote` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`datestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`ip` VARCHAR(20) NOT NULL DEFAULT '0.0.0.0',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."groups");
$result = dbquery("CREATE TABLE ".$db_prefix."groups (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(127) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `format` varchar(255) NOT NULL DEFAULT '{username}',
  `permissions` text NOT NULL,
  `team` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."panels");
$result = dbquery("CREATE TABLE ".$db_prefix."panels (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NOT NULL DEFAULT '',
	`filename` VARCHAR(100) NOT NULL DEFAULT '',
	`content` TEXT NOT NULL,
	`side` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`order` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	`type` VARCHAR(20) NOT NULL DEFAULT '',
	`access` VARCHAR(255) NOT NULL DEFAULT '',
	`display` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `order` (`order`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."sessions");
$result = dbquery("CREATE TABLE ".$db_prefix."sessions (
	`id` varchar(32) NOT NULL,
	`started` int(10) unsigned NOT NULL default '0',
	`expire` int(10) unsigned NOT NULL default '0',
	`ip` varchar(20) NOT NULL,
	`data` text NOT NULL
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."settings");
$result = dbquery("CREATE TABLE ".$db_prefix."settings (
	`key` VARCHAR(100) NOT NULL DEFAULT '',
	`value` text NOT NULL,
	PRIMARY KEY (`key`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate."");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."settings_inf");
$result = dbquery("CREATE TABLE ".$db_prefix."settings_inf (
	`name` VARCHAR(200) NOT NULL DEFAULT '',
	`value` TEXT NOT NULL,
	`inf` VARCHAR(200) NOT NULL DEFAULT '',
	PRIMARY KEY (`name`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate."");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."smileys");
$result = dbquery("CREATE TABLE ".$db_prefix."smileys (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL auto_increment,
	`code` VARCHAR(50) NOT NULL,
	`image` VARCHAR(100) NOT NULL,
	`text` VARCHAR(100) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."submissions");
$result = dbquery("CREATE TABLE ".$db_prefix."submissions (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` CHAR(1) NOT NULL,
	`user` MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
	`datestamp` INT(10) UNSIGNED DEFAULT '0' NOT NULL,
	`criteria` TEXT NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."time_formats");
$result = dbquery("CREATE TABLE ".$db_prefix."time_formats (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`value` VARCHAR(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `value` (`value`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."tags");
$result = dbquery("CREATE TABLE ".$db_prefix."tags (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`supplement` VARCHAR(100) NOT NULL DEFAULT '',
	`supplement_id` MEDIUMINT(8) UNSIGNED NOT NULL,
	`value` TEXT NOT NULL,
	`value_for_link` TEXT NOT NULL,
	`access` VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	INDEX USING BTREE (supplement),
	INDEX USING BTREE (supplement_id)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."user_field_cats");
$result = dbquery("CREATE TABLE ".$db_prefix."user_field_cats (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`name` VARCHAR(200) NOT NULL ,
	`order` SMALLINT(5) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."user_fields");
$result = dbquery("CREATE TABLE ".$db_prefix."user_fields (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL,
	`index` VARCHAR(50) NOT NULL,
	`cat` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1',
	`type` SMALLINT(5) NOT NULL DEFAULT '0',
	`option` TEXT NOT NULL DEFAULT '',
	`register` TINYINT(1) NOT NULL DEFAULT '0',
	`hide` TINYINT(1) NOT NULL DEFAULT '0',
	`edit` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");
if ( ! $result) $fail = TRUE;

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."users");
$result = dbquery("CREATE TABLE ".$db_prefix."users (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(30) NOT NULL DEFAULT '',
	`password` CHAR(129) NOT NULL DEFAULT '',
	`salt` CHAR(5) NOT NULL DEFAULT '',

	`user_hash` VARCHAR(10) NOT NULL DEFAULT '',
	`user_last_logged_in` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`user_remember_me` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',

	`admin_hash` VARCHAR(10) NOT NULL DEFAULT '',
	`admin_last_logged_in` INT(10) UNSIGNED NOT NULL DEFAULT '0',

	`browser_info` VARCHAR(100) NOT NULL DEFAULT '',
	`link` VARCHAR(30) NOT NULL DEFAULT '',
	`email` VARCHAR(100) NOT NULL DEFAULT '',
	`hide_email` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`valid_code` VARCHAR(32) NOT NULL DEFAULT '',
	`valid` TINYINT(1) NOT NULL DEFAULT '0',
	`offset` CHAR(5) NOT NULL DEFAULT '0',
	`avatar` VARCHAR(100) NOT NULL DEFAULT '',
	`joined` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`lastvisit` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`datestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`ip` VARCHAR(20) NOT NULL DEFAULT '0.0.0.0',
	`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`actiontime` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`theme` VARCHAR(100) NOT NULL DEFAULT 'Default',

	`roles` TEXT NOT NULL DEFAULT '',
	`role` INT(11) NOT NULL DEFAULT '2',
	`lastupdate` INT(10) NOT NULL DEFAULT '0',
	`lang` VARCHAR(20) NOT NULL,
	PRIMARY KEY (`id`),
	KEY name (username),
	KEY joined (joined),
	KEY lastvisit (lastvisit)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."users_online");
$result = dbquery("CREATE TABLE ".$db_prefix."users_online (
	`user_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`ip` VARCHAR(20) NOT NULL DEFAULT '0.0.0.0',
	`last_activity`INT(10) UNSIGNED NOT NULL DEFAULT '0',
	UNIQUE `user` (`user_id`, `ip`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");

$result = dbquery("DROP TABLE IF EXISTS ".$db_prefix."users_data");
$result = dbquery("CREATE TABLE ".$db_prefix."users_data (
  `user_id` int(11) AUTO_INCREMENT NOT NULL,
  `name` VARCHAR(200) NOT NULL DEFAULT '',
  `old` VARCHAR(200) NOT NULL DEFAULT '',
  `gg` VARCHAR(200) NOT NULL DEFAULT '',
  `skype` VARCHAR(200) NOT NULL DEFAULT '',
  `www` VARCHAR(200) NOT NULL DEFAULT '',
  `location` VARCHAR(200) NOT NULL DEFAULT '',
  `sig` TEXT NOT NULL DEFAULT '',
  `lang` VARCHAR(200) NOT NULL DEFAULT 'English',
  PRIMARY KEY (`user_id`)
) ENGINE = InnoDB CHARACTER SET ".$charset." COLLATE ".$collate.";");

if ( ! $result) $fail = TRUE;