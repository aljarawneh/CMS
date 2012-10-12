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
try
{
	require_once '../../config.php';
	require DIR_SITE.'bootstrap.php';
	require_once DIR_SYSTEM.'admincore.php';
	
	$_locale->load('urls');

    if ( ! $_user->hasPermission('admin.urls'))
    {
        throw new userException(__('Access denied'));
    }

	$_tpl = new Iframe;

	if ($_request->get('status')->show() && $_request->get('act')->show())
    {
		$_tpl->getMessage($_request->get('status')->show(), $_request->get('act')->show(), array(
			'add' => array(
				__('The URL has been added.'), __('Error! The URL has not been added.')
			),
			'edit' => array(
				__('The URL has been edited.'), __('Error! The URL has not been edited.')
			),
			'delete' => array(
				__('The URL has been deleted.'), __('Error! The URL has not been deleted.')
			)
		));
    }

	if ($_request->get('action')->show())
	{
		if ($_request->get('id')->isNum())
		{
			if ($_request->get('action')->show() === 'delete')
			{
				$count = $_pdo->exec('DELETE FROM [links] WHERE `id` = :id',
					array(':id', $_request->get('id')->show(), PDO::PARAM_INT)
				);

				if ($count)
				{
					$_log->insertSuccess('delete', __('The URL has been deleted.'));
					$_request->redirect(FILE_PATH, array('act' => 'delete', 'status' => 'ok'));
				}

				$_log->insertFail('delete', __('Error! The URL has not been deleted.'));
				$_request->redirect(FILE_PATH, array('act' => 'delete', 'status' => 'error'));
			}
			elseif ($_request->get('action')->show() === 'edit')
			{
				$row = $_pdo->getRow('SELECT `id`, `full_path`, `short_path` FROM [links] WHERE id = :id', 
					array(':id', $_request->get('id')->show(), PDO::PARAM_INT)
				);

				if ($row)
				{
					$_tpl->assign('link', array(
						'id'	=> $row['id'],
						'full'  => $row['full_path'],
						'short' => $row['short_path']
					));
				}
				else
				{
					throw new userException(__('Error! Missing id.', array(':id' => $_request->get('id')->show())));
				}
				
				$_tpl->assign('edit', TRUE);
			}
			else
			{
				throw new userException(__('There is no such action'));
			}
		}
		else
		{
			throw new userException(__('Incorrect link'));
		}
	}
	else
	{
		if ($_request->post('save')->show())
		{
			if ($_request->post('full_path')->show() && $_request->post('short_path')->show())
			{
				if (isset($_POST['id']))
				{
					$count = $_pdo->exec('UPDATE [links] SET full_path = :full, short_path = :short, datestamp = '.time().' WHERE id = :id',
						array(
							array(':full', $_request->post('full_path')->show(), PDO::PARAM_STR),
							array(':short', $_request->post('short_path')->show(), PDO::PARAM_STR),
							array(':id', $_request->post('id')->show(), PDO::PARAM_INT)
						)
					);
					
					if ($count)
					{
						$_log->insertSuccess('edit', __('The URL has been edited.'));
						$_request->redirect(FILE_PATH, array('act' => 'edit', 'status' => 'ok'));
					}

					$_log->insertFail('edit', __('Error! The URL has not been edited.'));
					$_request->redirect(FILE_PATH, array('act' => 'edit', 'status' => 'error'));
				}
				else
				{
					$count = $_pdo->exec('INSERT INTO [links] (`full_path`, `short_path`, `datestamp`) VALUES (:full, :short, '.time().')',
						array(
							array(':full', $_request->post('full_path')->show(), PDO::PARAM_STR),
							array(':short', $_request->post('short_path')->show(), PDO::PARAM_STR)
						)
					);
					
					if ($count)
					{
						$_log->insertSuccess('add', __('The URL has been added.'));
						$_request->redirect(FILE_PATH, array('act' => 'add', 'status' => 'ok'));
					}

					$_log->insertFail('add', __('Error! The URL has not been added.'));
					$_request->redirect(FILE_PATH, array('act' => 'add', 'status' => 'error'));
				}
			}
			else
			{
				throw new userException(__('Every field must be filled'));
			}
		}

		$query = $_pdo->getData('SELECT `id`, `full_path`, `short_path`, `datestamp` FROM [links]');

		$i = 0; $link = array();
		foreach($query as $d)
		{
			$link[] = array(
				'row_color' => $i % 2 == 0 ? 'tbl1' : 'tbl2',
				'id'	   => $d['id'],
				'date'     => $d['datestamp'] ? HELP::showDate('shortdate', $d['datestamp']) : __('No data'),
				'full'     => $d['full_path'],
				'short'    => $d['short_path']
			);

			$i++;
		}

		$_tpl->assign('link', $link);
	}

	$_tpl->template('urls');

}
catch(optException $exception)
{
    optErrorHandler($exception);
}
catch(systemException $exception)
{
    systemErrorHandler($exception);
}
catch(userException $exception)
{
    userErrorHandler($exception);
}
catch(PDOException $exception)
{
    PDOErrorHandler($exception);
}