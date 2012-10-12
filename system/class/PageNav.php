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

# THIS CLASS IS A VIEW

interface PageNavIntf
{
	// Wy�wietla stronicowanie
	public function create($show_go_to_first = TRUE, $show_go_to_last = TRUE);

	public function get(array $paging, $filename, $dir = NULL);

	// Zwraca tablic� z numerami podstron do wy�wietlenia
	public function getPagesNums();

	// Zwraca ilo�� numer�w podstron, jakie zostan� wy�wietlone w nawigacji
	// z uwzgl�dnieniem aktualnej pozycji i wyj�ciowej ilo�ci
	public function getLinksCount();
}

class PageNav implements PageNavIntf
{
	private
		$_paging,
		$_tpl;

	private
		$_links_count,					// Ilo�� podstron, jakie b�d� wy�wietlane w nawigacji
		$_pages_nums = array(), 		// Tablica z numerami podstron z uwzgl�dnieniem $_links_count
		$_route,						// Tablica z formatem link�w stronicowania
		$_default_ext = 'html';

	// Nalezy pami�ta�, aby parametr $links_count przes�any do konstruktora by� parsowany przez funkcj� intval()!!
	public function __construct(Paging $paging, $tpl, $links_count = 5, $route)
	{
		if ($links_count >= 1)
		{
			$this->_links_count = $links_count;
		}
		else
		{
			throw new systemException('B��d! Parametr czwarty nie mo�e przyjmowa� warto�ci mniejszej od <span class="italic">1</span>.');
		}

		$this->_route = $route;

		$this->_paging = $paging;
		$this->_tpl = $tpl;

		$this->createListToDisplay();
	}

	private function createListToDisplay()
	{
		// Sprawdza, czy liczba numer�w stron do wy�wietlenia jest parzysta
		if ($this->_links_count % 2 == 0)
		{
			/* Wyliczanie pierwszego i ostatniego numeru podstrony */
			$begin = $this->_paging->getCurrentPage()-($this->_links_count/2-1);
			$end = $this->_paging->getCurrentPage()+$this->_links_count/2;
		}
		else
		{
			/* Wyliczanie pierwszego i ostatniego numeru podstrony */
			$begin = $this->_paging->getCurrentPage()-floor($this->_links_count/2);
			$end = $this->_paging->getCurrentPage()+floor($this->_links_count/2);
		}

		// Sprawdzanie, czy ostatni numer reprezentuje istniej�c� podstron�
		if ($end > $this->_paging->getLastPage())
		{
			// Skoro wyliczony numer  wykracza poza numery reprezentuj�ce istniej�ce podstrony,
			// to trzeba cofn�� do ty�u numer pierwszej, aby wy�wietli�o si� tyle numer�w, ile chciano.
			$begin -= $end - $this->_paging->getLastPage();

			// Ostatni wy�wietlany numer podstrony b�dzie r�wny ilorazowi wszystkich materia��w a ich ilo�ci na podstron�.
			$end = $this->_paging->getLastPage();
		}

		// Sprawdzanie, czy pierwszy numer nie jest mniejszy od 1
		if ($begin < 1)
		{
			// Skoro wyliczony numer wykracza poza numery reprezentuj�ce istniej�ce podstrony (jest mniejszy od 1, a taka podstrona przecie� nie istnieje),
			// to trzeba przesun�� do przodu numer ostatniej, aby wy�wietli�o si� tyle numer�w, ile chciano.
			$end += 1 - $begin;

			// Pierwszy wy�wietlany numer podstrony b�dzie r�wny 1
			$begin = 1;

			// Sprawdzanie, czy w wyniku przesuwania do przodu ostatniej podstrony nie przekroczono zakresu istniej�cych podstron
			if ($end > $this->_paging->getLastPage())
			{
				// Skoro zakres przekroczono, to ostatni wy�wietlany numer podstrony b�dzie r�wny ilorazowi wszystkich materia��w a ich ilo�ci na podstron�.
				// Niestety, ilo�� wy�wietlanych numer�w podstron b�dzie mniejsza ni� chciano, gdy� ich tyle nie istnieje.
				$end = $this->_paging->getLastPage();
			}
		}

		// Skoro znamy pocz�tkowy i ko�cowy numer podstron, trzeba je wyodr�bni�.
		for($i = $begin; $i <= $end; $i++)
		{
			$this->_pages_nums[] = $i;
		}
	}

	public function create($show_go_to_first = TRUE, $show_go_to_last = TRUE)
	{
		$page_nav['nums'] = $this->getPagesNums();

		// Nadawanie domy�lnej warto�ci
		$page_nav['first'] = NULL;
		$page_nav['last'] = NULL;

		if ($show_go_to_first && !in_array($this->_paging->getFirstPage(),  $this->getPagesNums()))
		{
			$page_nav['first'] = 1;
		}

		if ($show_go_to_last && !in_array($this->_paging->getLastPage(), $this->getPagesNums()))
		{
			$page_nav['last'] = $this->_paging->getLastPage();
		}

		$page_nav['current'] = $this->_paging->getCurrentPage();
		$page_nav['route'] = $this->_route;

		$page_nav['prev'] = $this->_paging->getPrevPage();
		$page_nav['next'] = $this->_paging->getNextPage();

		return $page_nav;
	}

	// W odr�bnym stosie OPT tworzy szablon, kt�ry przechwycony przez bufor danych jest zwracany przez metod�
	public function get(array $paging, $filename, $dir = NULL)
	{
		if ($paging)
		{

			/* TO DO
				Notice: Undefined index: route in C:\ef5gif\system\class\StaticContainer.php on line 14
				Call Stack
				#	Time	Memory	Function	Location
				1	0.0020	965336	{main}( )	..\news.php:0
				2	0.0447	4520680	PageNav->get( )	..\news.php:275
				3	0.0451	4543680	StaticContainer->get( )	..\PageNav.php:147
			*/
			$_tpl = new pageNavParser(StaticContainer::has('route') ? StaticContainer::get('route') : NULL);

			$_tpl->assignGroup(array(
				'nums' => count($paging['nums']) > 1 ? $paging['nums'] : NULL,
				'first' => $paging['first'],
				'last' => $paging['last'],
				'prev' => $paging['prev'],
				'next' => $paging['next'],
				'current' => $paging['current'],
				'page' => isset($paging['route'][0]) ? $paging['route'][0] : NULL,
				'id' => isset($paging['route'][1]) ? $paging['route'][1] : NULL,
				'ext' => isset($paging['route'][2]) ? $this->_route === FALSE ? '.'.$paging['route'][2] : '' : $this->_route === FALSE ? '.'.$this->_default_ext : ''
			));

			ob_start();

			$_tpl->template($filename, $dir);

			$out = ob_get_contents();

			ob_end_clean();

			$this->_tpl->assign('page_nav', $out);
		}
	}

	public function getPagesNums()
	{
		return $this->_pages_nums;
	}

	public function getLinksCount()
	{
		return count($this->_pages_nums);
	}
}