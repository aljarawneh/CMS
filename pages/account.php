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
if(!iUSER) HELP::redirect(ADDR_SITE);

if ($_route->getAction() === 'logout')
{
	$_user->userLogout();
	HELP::redirect(ADDR_SITE);
}

$_locale->load('account');

$status = $_route->getByID(1);
$error = $_route->getByID(2);

$theme = array(
	'Title' => 'Ustawienia konta użytkownika - '.$_user->get('username').' &raquo; '.$_sett->get('site_name'),
	'Keys' => 'Ustawienia profilu, ustawienia konta, edycja konta, profil',
	'Desc' => 'W tym miescju możesz dokonać aktualizacji swoich danych osobowych.'
);

if(isset($status) && $status == 'ok')
{
	$_tpl->printMessage('valid', __('Konto edytowane prawidłowo'));
}
elseif(isset($status) && $status == 'error')
{
	if(isset($status) && $status == 'error' && isset($error) && $error == 1)
	{
		$_tpl->printMessage('error', __('Pola z nazwą użytkownika i emailem nie mogą być puste.'));
	}
	elseif(isset($status) && $status == 'error' && isset($error) && $error == 2)
	{
		$_tpl->printMessage('error', __('Nazwa użytkownika zawiera niedozwolone znaki.'));
	}
	elseif(isset($status) && $status == 'error' && isset($error) && $error == 3)
	{
		$_tpl->printMessage('error', __('Podano nieprawidłowe aktualne hasło.'));
	}
	elseif(isset($status) && $status == 'error' && isset($error) && $error == 4)
	{
		$_tpl->printMessage('error', __('Hasła użytkownika nie pasują do siebie.'));
	}
	elseif(isset($status) && $status == 'error' && isset($error) && $error == 5)
	{
		$_tpl->printMessage('error', __('Wystąpił błąd przy próbie zmiany hasła. Prosimy o kontakt z Administracją.'));
	}
	elseif(isset($status) && $status == 'error' && isset($error) && $error == 6)
	{
		$_tpl->printMessage('error', __('Adres email zawiera niedozwolone znaki.'));
	}
	else
	{
		$_tpl->printMessage('error', __('Błąd podczas edycji konta'));
	}
}

if ($_request->post('save')->show() && $_request->post('email')->show())
{
	if ($_sett->get('change_name') == 1)
	{
		$username = trim(preg_replace("/ +/i", " ", $_request->post('username')->show()));
	}
	else
	{
		$username = $_user->get('username');
	}

	if ($username === '' || trim($_request->post('email')->show()) === '')
	{
		HELP::redirect($_route->path(array('controller' => 'account', 'action' => 'error', '1')));
	}

	if ( ! preg_match("/^[-0-9A-Z_@\s]+$/i", $username))
	{
		HELP::redirect($_route->path(array('controller' => 'account', 'action' => 'error', '2')));
	}

	if ( ! $_request->post('email')->isEmail())
	{
		HELP::redirect($_route->path(array('controller' => 'account', 'action' => 'error', '6')));
	}

	// Aktualizacja nazwy użytkownika
	if ($_sett->get('change_name') == 1)
	{
		if ($username != $_user->get('username'))
		{
			$_user->newName($username, $_user->get('id'));
		}
	}

	// Aktualizacja adresu e-mail
	if ($_request->post('email') != $_user->get('email'))
	{
		$_user->newEmail($_request->post('email')->show(), $_user->get('id'));
	}

	// Aktualizuje hasło użytkownika
	if ($_request->post('old_password')->show() && $_request->post('password1')->show())
	{
		if ( ! $_user->checkOldPass($_user->get('id'), $_request->post('old_password')->show()))
		{
			HELP::redirect($_route->path(array('controller' => 'account', 'action' => 'error', '3')));
		}

		if ($_request->post('password1')->show() !== $_request->post('password2')->show())
		{
			HELP::redirect($_route->path(array('controller' => 'account', 'action' => 'error', '4')));
		}

		if ($_user->changePass($_user->get('id'), $_request->post('password1')->show()))
		{
			//deprecated $_user->updateLoginSession($_request->post('password1')->show());
		}
		else
		{
			HELP::redirect($_route->path(array('controller' => 'account', 'action' => 'error', '5')));
		}
	}

	$count = $_user->update(array(
		'hide_email' => $_request->post('hideemail')->isNum(TRUE),
		'theme' => $_request->post('theme')->strip(),
		'lang' => $_request->post('language')->strip()
		), $_user->get('id')
	);

	if ($_request->post('del_avatar')->show())
	{
		$_user->delAvatar();
	}

	if ($_request->files('avatar')->show())
	{
		$avatar = $_user->saveNewAvatar($_user->get('id'), $_request->files('avatar')->show());
	}

	$data = $_pdo->getRow('SELECT `user_id` FROM [users_data] WHERE `user_id` = :id',
		array(':id', $_user->get('id'), PDO::PARAM_INT)
	);

	$query = $_pdo->getData('SELECT * FROM [user_fields] WHERE `edit` = 0');
	$match = $_pdo->getRowsCount($query);
	$i = 0; $field = '';
	if($match != NULL)
	{
		foreach($query as $data)
		{
			$index = $data['index'];
			$val = $_request->post($index)->show();
			$index_val .= '`'.$index.'`'.($i < $match-1 ? ', ' : '');
			$field_val .= '"'.$val.'"'.($i < $match-1 ? ', ' : '');
			$field .= '`'.$index.'` = "'.$val.'"'.($i < $match-1 ? ', ' : '');
			$i++;
		}

		$_user->updateField($field, $_user->get('id'), $index_val, $field_val);
	}

	$_system->clearCache('profiles');
	HELP::redirect($_route->path(array('controller' => 'account', 'action' => 'ok')));
}

	$user = array(
		'ID' => $_user->get('id'),
		'Username' => $_user->get('username'),
		'Email' => $_user->get('email'),
		'HideEmail' => $_user->get('hide_email'),
		'Theme' => $_user->get('theme'),
		'Avatar' => $_user->get('avatar')
	);

	$_tpl->assignGroup(array(
		'theme_set' => $_tpl->createSelectOpts($_files->createFileList(DIR_SITE.'themes', array('templates'), TRUE, 'folders'), $_user->get('theme')),
		'locale_set' => $_tpl->createSelectOpts($_files->createFileList(DIR_SITE.'locale', array(), TRUE, 'folders'), $_user->get('lang')),
		'User' => $user,
		'ChangeName' => $_sett->get('change_name')
	));

	//print_r($_user->getLang());
	// Pobieranie kategorii
	$query = $_pdo->getData('SELECT * FROM [user_field_cats] ORDER BY `order` ASC');
	$cats = array();

	// Przepisywanie pobranych danych na zwykłą tablicę
	foreach($query as $data)
	{
		$cats[] = $data;
	}

	// Pobieranie pól
	$query = $_pdo->getData('SELECT * FROM [user_fields] WHERE `edit` = 0');

	// Przepisywanie pobranych pól na zwykłą tablicę
	foreach($query as $data)
	{
		$fields[] = $data;
	}

	// Pobieranie wszystkich dodatkowych pól uzytkowników
	$data = $_pdo->getRow('SELECT * FROM [users_data] WHERE `user_id` = '.$_user->get('id').' LIMIT 1');

	// Przepisywanie pobranych pól na zwykłą tablicę
	$i = 0;

	# Segregacja danych
	if (isset($fields))
	{
		$_new_fields = array();

		foreach($cats as $key => &$cat)
		{
			$has_data = FALSE;
			foreach($fields as $field)
			{
				if ($field['cat'] === $cat['id'])
				{
					$option = array();
					if ($field['type'] == 3)
					{
						$n = 1;
						foreach(unserialize($field['option']) as $val)
						{
							$option[$n] = $val;
							$n++;
						}
					}

					$new_fields[$key][$i] = array(
						'name' => $field['name'],
						'index' => $field['index'],
						'type' => $field['type'],
						'value' => ($data[$field['index']] ? $data[$field['index']] : NULL),
						'option' => $_tpl->createSelectOpts($option, $data[$field['index']], FALSE, FALSE),
					);

					$has_data = TRUE;

					$i++;
				}
			}

			if ($has_data)
			{
				$cat['has_fields'] = '1';
			}
			else
			{
				$cat['has_fields'] = '0';
			}
		}
		$_tpl->assign('Fields', $new_fields);
	}

	$_tpl->assign('Cats', $cats);
	#************

	$_tpl->assignGroup(array(
		'bbcode' => $ec->sbb->bbcodes(),
		'smiley' => $ec->sbb->smileys()
	));