<?php defined('EF5_SYSTEM') || exit;
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
$_locale->load('team');
$_head->set('<link href="'.ADDR_TEMPLATES.'stylesheet/team.css" media="screen" rel="stylesheet" />');

// TO DO Będziesz dodawać cache dla tej strony użyj: $_sett->getUns('cache', 'expire_team') //

$theme = array(
	'Title' => __('Site Team').' &raquo; '.$_sett->get('site_name'),
	'Keys' => 'Ekipa '.$_sett->get('site_name').', skład, właściciele',
	'Desc' => 'Lista osób odpowiedzialnych za działanie i funkcjonowanie strony.'
);
// Blokuje wykonywanie pliku TPL z katalogu szablonu
define('THIS', TRUE);

// Site Admin
$row = $_pdo->getRow('SELECT * FROM [users] WHERE `id` = 1');

	if(time() <= $row['lastvisit']+300)
	{
		$last_visit = 1;
	}
	else
	{
		$last_visit = HELP::showDate('shortdate', $row['lastvisit']);
	}

	$admin = array(
		'id' => $row['id'],
		'username' => $_user->getUsername($row['id']),
		'role' => $_user->getRoleName($row['role']),
		'roles' => implode($_user->getUserRolesTitle($row['id']), ', '),
		'avatar' => $row['avatar'],
		'joined' => HELP::showDate('shortdate', $row['joined']),
		'last_visit' => $last_visit,
		'link' => HELP::profileLink($row['username'], $row['id']),
		'last_visit_time' => $row['lastvisit'] ? HELP::showDate('longdate', $row['lastvisit']) : NULL,
		'is_online' => inArray($row['id'], $_user->getOnline(), 'id') ? 1 : 0,
	);
	
$_tpl->assign('site_admin', $admin);

// Admins
$query = $_pdo->getData('SELECT * FROM [users] WHERE `id` != 1 AND `role` = 1');

if ($_pdo->getRowsCount($query))
{
	foreach($query as $row)
	{
		if(time() <= $row['lastvisit']+300)
		{
			$last_visit = 1;
		}
		else
		{
			$last_visit = HELP::showDate('shortdate', $row['lastvisit']);
		}
		


		$data[] = array(
			'id' => $row['id'],
			'username' => $_user->getUsername($row['id']),
			'role' => $_user->getRoleName($row['role']),
			'roles' => implode($_user->getUserRolesTitle($row['id']), ', '),
			'avatar' => $row['avatar'],
			'joined' => HELP::showDate('shortdate', $row['joined']),
			'last_visit' => $last_visit,
			'link' => HELP::profileLink($row['username'], $row['id']),
			'last_visit_time' => $row['lastvisit'] ? HELP::showDate('longdate', $row['lastvisit']) : NULL,
			'is_online' => inArray($row['id'], $_user->getOnline(), 'id') ? 1 : 0,
		);
	}
		
	$_tpl->assign('admin', $data);
}

// Other groups
$groups = '';
$query = $_pdo->getData('SELECT * FROM [groups] WHERE `id` != 1 AND `team` = 1');
if($query !== NULL)
{
	foreach($query as $data)
	{
		$groups[] = $data;
	}
}

$users = '';
$query = $_pdo->getData('SELECT * FROM [users] WHERE `id` != 1 AND `role` > 3');
if($query !== NULL)
{
	foreach($query as $data)
	{
		$users[] = $data;
	}
}


if($groups != NULL)
{
	$i = 0;

	$all_users = array();

	foreach($groups as $key => $group)
	{
		if($users != NULL)
		{
			foreach($users as $user)
			{
				if ($user['role'] === $group['id'])
				{
					if(time() <= $user['lastvisit']+300)
					{
						$last_visit = 1;
					}
					else
					{
						$last_visit = HELP::showDate('shortdate', $user['lastvisit']);
					}
				
					$all_users[$key][$i] = array(
						'id' => $user['id'],
						'username' => $_user->getUsername($user['id']),
						'role' => $_user->getRoleName($user['role']),
						'roles' => implode($_user->getUserRolesTitle($user['id']), ', '),
						'avatar' => $user['avatar'],
						'joined' => HELP::showDate('shortdate', $user['joined']),
						'last_visit' => $last_visit,
						'link' => HELP::profileLink($user['username'], $user['id']),
						'last_visit_time' => $row['lastvisit'] ? HELP::showDate('longdate', $row['lastvisit']) : NULL,
						'is_online' => inArray($row['id'], $_user->getOnline(), 'id') ? 1 : 0
					);
					$i++;
				}
			}
		}
		else
		{
			$_tpl->printMessage('info', __('Nie znaleziono użytkowników należących do tej grupy'));
		}
	}

	$_tpl->assign('users', $all_users);

$_tpl->assign('groups', $groups);
}


	#************
/*print_r($cos); exit;
$query = $_pdo->getData('SELECT * FROM [groups] WHERE `id` != 1 AND `team` = 1');

$i = 0; $group = array();
foreach($query as $group)
{
	$groups[] = array(
		'Title' => $group['title']
	);
	
	$query2 = $_pdo->getData('SELECT * FROM [users] WHERE `role` = '.$group['id']);
	foreach($query2 as $user)
	{
		$users[] = $user;
	}
	$_tpl->assign('Users', $users);
	
	$i++;
}
	
$_tpl->assign('Groups', $groups);*/