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

// Pobranie z cache zapytania sprawdzaj�cego czy modu� jest zainstalowany
$row = $_system->cache('install_status', NULL, 'point_system', 60);

if ($row === NULL)
{
	// Sprzwdzanie czy modu� znajduje si� na li�cie zainstalowanych modu��w oraz umieszczenie go w cache
	$row = $_pdo->getRow('SELECT `id` FROM [modules] WHERE `folder` = :folder',
		array(':folder', 'point_system', PDO::PARAM_STR)
	);
	$_system->cache('install_status', $row, 'point_system');
}

if ($row)
{
	// Usuni�cie z pami�ci zmiennej $row przechowuj�cej informacje o poprzednim cache
	unset($row);
	
	// Sprawdzenie czy aktualnie znajdujemy si� w galerii
	include DIR_MODULES.'point_system'.DS.'class'.DS.'Points.php';
	
	$_locale->moduleLoad('lang', 'point_system');
	
	$_points = new Points($_pdo, $_system);
}