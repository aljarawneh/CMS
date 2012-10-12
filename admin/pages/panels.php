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

	$_locale->load('panels');

    if ( ! $_user->hasPermission('admin.panels'))
    {
        throw new userException(__('Access denied'));
    }

	$_tpl = new Iframe;

    $_panels = new Panels($_pdo);
	$_panels->adminMakeListPanels($_user);

	$_tpl->assign('noact_panels', $_panels->adminGetInactivePanels());
	$_tpl->assign('panel', $_panels->adminGetPanel());
	$_tpl->template('panels');
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