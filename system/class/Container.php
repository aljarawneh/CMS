<?php

class Container extends ServiceContainerBuilder
{
	protected function getCommentService()
	{
		return new Comment(new Basic, $this->getService('Pdo'), $this->getService('User'), $this->getService('Sett'), $this->getService('Sbb'), $this->getService('Header'));
	}

	protected function getUserService()
	{
		return new User($this->getService('Sett'), $this->getService('Pdo'));
	}

	protected function getSettService()
	{
		return new Sett($this->getService('System'), $this->getService('Pdo'));
	}

	protected function getSystemService()
	{
		return new System;
	}

	protected function getPDOService()
	{
		$_dbconfig = $this['pdo.config'];

		$pdo = new Data('mysql:host='.$_dbconfig['host'].';dbname='.$_dbconfig['database'].';port='.$_dbconfig['port'], $_dbconfig['user'], $_dbconfig['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$_dbconfig['charset']));
		$pdo->config($_dbconfig['prefix']);

		return $pdo;
	}

	protected function getHeaderService()
	{
		return new Header;
	}

	protected function getSBBService()
	{
		include_once DIR_CLASS.'Sbb.php';

		return SmileyBBcode::getInstance($this->getService('Sett'), $this->getService('Pdo'), $this->getService('Locale'), $this->getService('Header'), $this->getService('User'), $this->getService('System'));
	}

	protected function getLocaleService()
	{
		return new Locales($this->getService('Sett')->get('locale'), DIR_LOCALE);
	}
}