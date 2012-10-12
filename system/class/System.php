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
class System {

	private $_rewrite_available;
	private $_furl = FALSE;
	private $_rewrite = FALSE;

	/**
	 * Tworzenie środowiska pracy systemu
	 *
	 * @return  void
	 * @throws  systemException
	 */
	public function __construct($cleaning = TRUE)
	{
		// Zabezpieczenie przed atakami XSS.
		if ($cleaning && HELP::stripget($_GET))
		{
			throw new systemException(die('Podejrzewany atak XSS po zmiennej $_GET!'));
		}

		if (file_exists(DIR_SITE.'config.php'))
		{
			require DIR_SITE.'config.php';
			$this->_furl = isset($_route['custom_furl']) && $_route['custom_furl'] === TRUE;
			$this->_rewrite = isset($_route['custom_rewrite']) && $_route['custom_rewrite'] === TRUE;
		}
		
		$_SERVER['SERVER_SOFTWARE'] = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
		$_SERVER['SERVER_SIGNATURE'] = isset($_SERVER['SERVER_SIGNATURE']) ? $_SERVER['SERVER_SIGNATURE'] : '';
	}

	/**
	 * Tworzy podstawową obsługę pamięci podręcznej.
	 *
	 *     // Tworzy pamięć podręczną o nazwie `foo`, wartości `bar`, w podkatalogu `dir`.
	 *     $_system->cache('foo', 'bar');
	 *
	 *     // Pobiera pamięć podręczną o nazwie `foo` z podkatalogu `dir`.
	 *     $_system->cache('foo', NULL, 'dir');
	 *
	 * @param   string   nazwa pamięci podręcznej
	 * @param   mixed    zawartość pamięci podręcznej
	 * @param   mixed    nazwa podkatalogu z cache
	 * @param   integer  żywot pamięci podręcznej
	 * @return  mixed
	 */
	public function cache($name, $data = NULL, $dir = NULL, $lifetime = 3600)
	{
		// Wyłączenie szyfrowania cache wersja jedynie dla DEV
		// Pamiętaj o ręcznym usunięciu cache po zmianie parametru FALSE/TRUE
		$code = FALSE;

		// Koduje nazwę pliku pamięci podręcznej
		if ( ! $code)
		{
			$file = CACHE_PREFIX.$name.'.txt';
		}
		else
		{
			$file = sha1(CACHE_PREFIX.$name).'.txt';
		}

		// Tworzy ścieżkę dostępu do katalogu z pamięcią podręczną
		$dir = DIR_SITE.'cache'.DS.$dir.DS;
		if ( ! file_exists($dir))
		{
			mkdir($dir);
			chmod($dir, 0777);
		}

		if ($code)
		{
			// TODO:: czy wartość $key/$string nie powinna być brana z zakodowanej cześci $data zamiast z nazwy pliku?
			// TODO:: bo wydaje mi się że w obecnej formie może się zdarzyć tak, że w zawartości
			// TODO:: cache'owanego pliku nie znajdzie wyrażenia spod tych zmiennych (które pochodzi z nazwy pliku a nie zawartości)
			// TODO:: i wtedy łatwo rozkodować taki plik.

			// Tworzy klucz który jest mieszany z base64_decode/base64_encode,
			// zabezpiecza przed bezpośrednim odczytaniem danych z przez base64_decode
			$key = substr(sha1($file), 1, 7);
			$string = substr(sha1($file), 5, 1);
		}

		if ($data === NULL)
		{
			if (is_file($dir.$file))
			{
				if ((time() - filemtime($dir.$file)) < $lifetime || $lifetime === NULL)
				{
					if ( ! $code)
					{
						return unserialize(file_get_contents($dir.$file));
					}
					else
					{
						return unserialize(base64_decode(str_replace($key, $string, file_get_contents($dir.$file))));
					}
				}
				else
				{
					if (file_exists($dir.$file))
					{
						unlink($dir.$file);
					}
				}
			}

			// Nie znaleziono pliku
			return NULL;
		}

		if ( ! is_dir($dir))
		{
			// Tworzy nowy katalog z pamięcią
			mkdir($dir, 0777, FALSE);

			// Nadaje prawa zapisu
			chmod($dir, 0777);
		}

		if ( ! $code)
		{
			return (bool) file_put_contents($dir.$file, serialize($data), LOCK_EX);
		}
		else
		{
			return (bool) file_put_contents($dir.$file, str_replace($string, $key, base64_encode(serialize($data))), LOCK_EX);
		}
	}

	/**
	 * Opróżnia katalog pamięci podręcznej szablonów.
	 *
	 *     // Usuwa całą pamięć podręczną szablonów.
	 *     $_system->clearCache();
	 *
	 *     // Usuwa pliki `foo.tpl` oraz `bar.tpl` z pamięci podręcznej.
	 *     $_system->clearCache(NULL, array('foo.tpl', 'bar.tpl'));
	 *
	 *     // Usuwa pamięć podręczną z podkatalogu `dir`.
	 *     $_system->clearCache('dir');
	 *
	 * @param   mixed    nazwa podkatalogu z cache
	 * @param   mixed    pliki pamięci podręcznej do usunięcia
	 * @param   string   ścieżka do głównego katalogu z pamięcią podręczną
	 * @return  boolean  zawsze zwróci TRUE
	 */
	public function clearCache($dir = NULL, array $cache = array(), $path = DIR_CACHE)
	{

		if (file_exists($path.$dir))
		{
			// Przeszukuje katalog pamięci podręcznej
			$files = new DirectoryIterator($path.$dir);

			foreach ($files as $file)
			{
				// Sprawdza rozszerzenie pliku pamięci podręcznej
				$extension = pathinfo($file->getPathname(), PATHINFO_EXTENSION);

				if ( ! $file->isDot() && $file->isFile() && ($extension === 'tpl' || $extension === 'txt'))
				{
					if (in_array(preg_replace('/'.CACHE_PREFIX.'/', '', pathinfo($file->getPathname(), PATHINFO_FILENAME)), $cache) || empty($cache))
					{
						if (file_exists($file->getPathname()))
						{
							// Usuwa plik pamięci podręcznej
							unlink($file->getPathname());
						}
					}
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Wykrywa język z jakiego korzysta przeglądarka
	 *
	 * @return  string
	 */
	 //todo::przenieśc do helpera lub osobnej klasy statycznej
	public static function detectBrowserLanguage($full = FALSE)
	{
		$langs = array(
			'pl' => 'Polish',
			'en' => 'English',
			'cz' => 'Czech'
		);

		$var = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$var = explode(',', $var[0]);

		$current = null;
		foreach($var as $data)
		{

			if(isset($langs[$data]))
			{
				$current = $data;
				break;
			}
		}

		if(is_null($current))
		{
			$current = 'en';
		}

		if ($full === FALSE)
		{
			return $current;
		}
		
		return $langs[$current];
	}

	public function getModuleBootstrap($cache_expire = 43200)
	{
		$row = $this->cache('__autoloadModulesList', NULL, 'system', $cache_expire);
		if ($row === NULL)
		{
			$files = new DirectoryIterator(DIR_MODULES);
			foreach ($files as $file)
			{
				if ( ! in_array($file->getFilename(), array('..', '.', '.svn', '.gitignore')))
				{
					if (is_dir($file->getPathname()) && file_exists($file->getPathname().DS.'autoload'.DS.'__autoload.php'))
					{
						$row[] = $file->getFilename();
					}
				}
			}
			sort($row);
			$this->cache('__autoloadModulesList', $row, 'system');
		}

		return $row;
	}

	// Zwraca informację, czy uzyskanie listy załadowanych modułów Apache jest możliwe
	public function apacheModulesListingAvailable()
	{
		return function_exists('apache_get_modules');
	}

	/**
	 * Sprawdza, czy moduł Apache podany parametrem istnieje
	 * lub zwraca tablicę załądowanych modułów Apache
	 */
	public function apacheModuleLoaded($name = NULL)
	{
		if ($this->apacheModulesListingAvailable())
		{
			if ($name !== NULL)
			{
				return in_array($name, apache_get_modules());
			}

			return apache_get_modules();
		}

		if ($name !== NULL)
		{
			/**
			 * Funkcja do sprawdzania załadowanych modułów Apache jest niedostępna.
			 * Zakładamy więc, że mod_rewrite nie jest załadowany.
			 */
			return FALSE;
		}

		throw new systemException('Błąd: Funkcja <span class="bold">apache_get_modules()</span> jest niedostępna!');
	}

	// Przed wywołaniem tej funkcji należy sprawdzić System::apacheModulesListingAvailable()
	public function rewriteAvailable()
	{
		if ($this->_rewrite_available !== NULL)
		{
			return $this->_rewrite_available;
		}

		return $this->_rewrite_available = $this->apacheModuleLoaded('mod_rewrite') || $this->_rewrite;
	}

	/**
	 * Zwraca informację, czy serwer obsługuje ścieżki w adresie URL podane po nazwie pliku.
	 * Przykład: index.php/ctrl/act/
	 *
	 * PATH_INFO nie występuje na stronie głównej, dlatego sprawdzane jest, czy serwerem jest Apache,
	 * który ścieżki ma zazwyczaj skonfigurowane prawidłowo.
	 *
	 * W przypadku IIS (test na 7.5), na stronie głównej występuje ORIG_PATH_INFO, a na podstronach także PATH_INFO.
	 * ORIG_PATH_INFO jest stosowane również na niektórych serwerach Apache.
	 *
	 * Jeżeli korzystasz z innego serwera, który jest skonfigurowany do PATH_INFO,
	 * wystarczy w pliku config.php zmienić wartość z FALSE na TRUE przy $_route['custom_furl'].
	 *
	 * Dla użytkowników systemu, którzy nie mają możliwości edycji pliku config.php, zaimplementowano system cache, który
	 * wychwytuje sytuację, gdy PATH_INFO wystąpi na którejś z podstron. Zapisywany jest o tym raport, dzięki czemu przyjazne
	 * linki zostają włączone.
	 *
	 * Przy standardowej konfiguracji, linki wyglądają następująco:
		Apache + rewrite: 	/ctrl/act/param-value/
		IIS: 				/index.php/ctrl/act/param-value/
		nginx:				/index.php?q=ctrl/act/param-value/
	 */
	public function pathInfoExists()
	{
		$apache = (isset($_SERVER['SERVER_SOFTWARE']) && preg_match('/Apache/i', $_SERVER['SERVER_SOFTWARE'])) || (isset($_SERVER['SERVER_SIGNATURE']) && preg_match('/Apache/i', $_SERVER['SERVER_SIGNATURE']));

		return $result = (bool) ($this->rewriteAvailable() || $this->serverPathInfoExists() || $apache || $this->_furl);

		// Serwer to nie Apache
		if ($result === FALSE)
		{
			$data = $this->cache('path_exists', NULL, 'system', 86400);
			if ($data[0] === FALSE)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}

		if (! $apache)
		{
			$this->cache('path_exists', array(TRUE), 'system');
		}
		return TRUE;
	}
	
	public function serverPathInfoExists()
	{
		return isset($_SERVER['PATH_INFO']) || isset($_SERVER['ORIG_PATH_INFO']);
	}
	
	public function httpServerIs($name)
	{
		return preg_match('/'.$name.'/i', $_SERVER['SERVER_SOFTWARE']) || preg_match('/'.$name.'/i', $_SERVER['SERVER_SIGNATURE']);
	}
}