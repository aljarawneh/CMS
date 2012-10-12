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
/*	Notatka dla rozwijających klasę:

	Aby wysłać ukryte kopie wiadomości (Bbc), trzeba skorzystać z funkcji mail() lub protokołu `sendmail`.
	Usuwają one z nagłówka wiadomości adresy wszystkich odbiorców, zostawiając tylko docelowy dla konkretnej osoby.

	Obecna klasa nie obsługuje `sendmail`, jedynie zachowuje się jakby wysyłała Bcc.
	Do użytku zewnetrznego są trzy metody:

	send() - wysyła pojedynczego maila do jednego odbiorcy
	sendCc() - wysyła maila do wielu odbiorców bez ukrycia ich adresów
	sendBcc() - wysyła przez wewnętrzną pętlę maile do wielu odbiorców ukrywając ich adresy

	Nie należy stosować send() w pętli,
	gdyż send() po każdym wykonaniu zrywa połączenie z serwerem, zaś sendBcc() utrzymuje je
	do wysłania wszystkich maili lub pojawienia się błędu.
*/
class Mailer
{
	protected
		$_to,
		$_from,
		$_subject,
		$_message,
		$_headers = array(),
		$_smtp, // Uchwyt
		$_data = array(),
		$_keep_alive = FALSE,
		$_to_is_array,			// Czy wiadomość ma być wysłana do wielu odbiorców? (bool)
		$_exception;

	public function __construct($username = NULL, $password = NULL, $host = NULL, $port = 587, $exception = TRUE)
	{
		// Czy któryś parametr jest NULL lub pusty?
		if (! in_array(NULL, array($username, $password, $host)))
		{
			$this->_data = array(
				'smtp_username' => $username,
				'smtp_password' => $password,
				'smtp_host' => $host,
				'smtp_port' => $port
			);
		}
		
		$this->_exception = $exception;
	}

	// Walidacja
	protected function validate()
	{
		return TRUE;
	}

	protected function addHeaders($headers)
	{
		$this->_headers = array_merge($this->_headers, $headers);
	}

	public function send($to, $from, $subject, $message = NULL, array $headers = array(), $html = TRUE)
	{
		/** Zapis danych do zmiennych klasowych **/
		$this->_to = $to;
		$this->_from = $from;
		$this->_subject = $subject;
		$this->_message = $message;
		$this->_headers = $headers;

		// Czy nie wystąpiły błędy w zapisanych danych?
		if ($this->validate())
		{
			// Nagłówki
			$this->addHeaders(array(
				'To: '.$this->_to.PHP_EOL,
				'From: '.$this->_from.PHP_EOL,
				'Subject: '.$this->_subject.PHP_EOL,
				'Reply-To: '.$this->_from.PHP_EOL,
				'X-Mailer: PHP eXtreme-Fusion 5'.PHP_EOL,
				'Return-Path: '.$this->_from.PHP_EOL
			));

			$this->_to_is_array = FALSE;

			if ($html)
			{
				$this->addHeaders(array(
					'MIME-Version: 1.0'.PHP_EOL,
					'Content-type: text/html; charset=UTF-8'.PHP_EOL
				));
			}

			// Wysyłanie wiadomości przez serwer SMTP
			if ($this->_data)
			{
				return $this->sendBySMTP();
			}

			// Wysyłanie wiadomości przez funkcję mail()
			return mail($this->_to, $this->_subject, $this->_message, implode('', $this->_headers));
		}

		throw new userException('Nieprawidłowe dane do wysyłki maila.');
	}

	public function sendCc(array $to, $from, $subject, $message = NULL, array $headers = array(), $html = TRUE)
	{
		/** Zapis danych do zmiennych klasowych **/
		$this->_to = $to;
		$this->_from = $from;
		$this->_subject = $subject;
		$this->_message = $message;
		$this->_headers = $headers;

		// Czy nie wystąpiły błędy w zapisanych danych?
		if ($this->validate())
		{
			// Nagłówki
			$this->addHeaders(array(
				'From: '.$this->_from.PHP_EOL,
				'Subject: '.$this->_subject.PHP_EOL,
				'Reply-To: '.$this->_from.PHP_EOL,
				'X-Mailer: PHP eXtreme-Fusion 5'.PHP_EOL,
				'Return-Path: '.$this->_from.PHP_EOL
			));


			$this->_to_is_array = TRUE;
			$this->addHeaders(array('Cc: '.implode($this->_to, ', ').PHP_EOL));

			if ($html)
			{
				$this->addHeaders(array(
					'MIME-Version: 1.0'.PHP_EOL,
					'Content-type: text/html; charset=UTF-8'.PHP_EOL
				));
			}

			// Wysyłanie wiadomości przez serwer SMTP
			if ($this->_data)
			{
				return $this->sendBySMTP();
			}

			// Wysyłanie wiadomości przez funkcję mail()
			return mail($this->_to, $this->_subject, $this->_message, implode('', $this->_headers));
		}

		throw new userException('Nieprawidłowe dane do wysyłki maila.');
	}

	public function sendBcc(array $to, $from, $subject, $message = NULL, array $headers = array(), $html = TRUE)
	{
		/** Zapis danych do zmiennych klasowych **/
		$this->_from = $from;
		$this->_subject = $subject;
		$this->_message = $message;
		$this->_headers = $headers;

		// Czy nie wystąpiły błędy w zapisanych danych?
		if ($this->validate())
		{
			$this->_to_is_array = FALSE;

			// Nagłówki
			$this->addHeaders(array(
				'From: '.$this->_from.PHP_EOL,
				'Subject: '.$this->_subject.PHP_EOL,
				'Reply-To: '.$this->_from.PHP_EOL,
				'X-Mailer: PHP eXtreme-Fusion 5'.PHP_EOL,
				'Return-Path: '.$this->_from.PHP_EOL
			));

			if ($html)
			{
				$this->addHeaders(array(
					'MIME-Version: 1.0'.PHP_EOL,
					'Content-type: text/html; charset=UTF-8'.PHP_EOL
				));
			}

			// Wysyłanie wiadomości przez serwer SMTP
			if ($this->_data)
			{
				// Połączenie z serwerem ma być utrzymywane aż do wysłania wszystkich maili lub wystąpienia błędu
				$this->_keep_alive = TRUE;

				// Wysyłanie maili wg odbiorców
				foreach($to as $rcpt)
				{
					$this->_to = $rcpt;
					$this->addHeaders(array('To: '.$rcpt.PHP_EOL));
					if ( ! $this->sendBySMTP())
					{
						return FALSE;
					}

					// Usuwanie odbiorcy wysłanego maila z tablicy nagłówka
					array_pop($this->_headers);
				}
			}
			else
			{
				foreach($to as $rcpt)
				{
					// Wysyłanie wiadomości przez funkcję mail()
					if ( ! mail($rcpt, $this->_subject, $this->_message, implode('', $this->_headers)))
					{
						return FALSE;
					}
				}
			}

			// Zamyka połączenie z serwerem
			$this->quit();

			return TRUE;
		}

		throw new userException('Nieprawidłowe dane do przesłania maila.');
	}

	// Wysyłanie wiadomości przez zewnętrzny serwer SMTP dla pojedynczego odbiorcy lub jawnej kopii (CC)
	protected function sendBySMTP()
	{
		if ($this->connect())
		{
			// Wysyłanie do serwera SMTP informacji od kogo jest mail
			$this->sendFrom($this->_from);

			// Wysyłanie do serwera SMTP informacji do kogo jest mail
			if ($status = $this->sendRecipient($this->_to))
			{
				// Czy wysłano wiadomość?
				if ($this->sendMessage(implode('', $this->_headers), $this->_message))
				{
					// Czy połączenie ma zostać utrzymane?
					if ($this->_keep_alive)
					{
						$this->reset();
					}
					else
					{
						$this->close();
					}

					return TRUE;
				}
			}
		}

		return FALSE;
	}

	// Nawiązywanie połączenia z serwerem SMTP
	protected function connect()
	{
		// Jeśli połączenie nie jest nawiązane
		if (! $this->isConnected())
		{
			$this->_smtp = fsockopen($this->_data['smtp_host'], $this->_data['smtp_port'], $errno, $errstr, 30);

			// Jeśli wystąpiły błędy
			if (! $this->_smtp)
			{
				throw new systemException('Błąd! Nie można połączyć się z serwerem SMTP.');
			}

			// Odbieranie odpowiedzi z serwera
			$this->getReply();

			// Wysyłanie zapytania Hello - na przywitanie ;)
			if (! $this->sendHello('EHLO', $this->_data['smtp_host']))
			{
				if (! $this->sendHello('HELO', $this->_data['smtp_host']))
				{
					throw new systemException('Helo/Ehlo zakończone niepowodzeniem.');
				}
			}

			// Autoryzacja - zwróci TRUE, albo rzuci wyjątkiem
			return $this->auth();
		}

		return TRUE; // Połączenie nawiązane ;)
	}

	// Zrywa połączenie z SMTP
	protected function close($quit = TRUE)
	{
		if ($this->_smtp)
		{
			if ($quit)
			{
				$this->quit();
			}

			fclose($this->_smtp);
			$this->_smtp = NULL;
		}
	}

	// Zwraca kod odpowiedzi serwera SMTP
	public function getCode()
	{
		return substr($this->getReply(), 0, 3);
	}

	// Logowanie do SMTP
	public function auth()
	{
		fwrite($this->_smtp, 'AUTH LOGIN'.PHP_EOL);
		if ($this->getCode() != 334)
		{
			throw new systemException('Błąd: Serwer SMTP nie zaakceptował próby autoryzacji.');
		}

		fwrite($this->_smtp, base64_encode($this->_data['smtp_username']).PHP_EOL);
		if ($this->getCode() != 334)
		{
			throw new systemException('Błąd: Nazwa użytkownika nie została zaakceptowana przez serwer SMTP.');
		}

		fwrite($this->_smtp, base64_encode($this->_data['smtp_password']).PHP_EOL);
		if ($this->getCode() != 235)
		{
			throw new systemException('Błąd: Hasło nie zostało zaakceptowana przez serwer SMTP.');
		}

		return TRUE;
	}

	// Pobiera odpowiedź od serwera SMTP.
	public function getReply()
	{
		$data = '';
		while($str = fgets($this->_smtp, 515))
		{
			$data .= $str;
			if (substr($str, 3, 1) === ' ')
			{
				break;
			}
		}

		return $data;
	}

	// Sprawdza stan połączenia z SMTP.
	public function isConnected()
	{
		if ($this->_smtp)
		{
			$status = stream_get_meta_data($this->_smtp);
			if ($status['eof'])
			{
				// End of file status.
				$this->close();
				return FALSE;
			}

			return TRUE;
		}

		return FALSE;
	}

	## KOMENDY ##

	public function reset()
	{
		fwrite($this->_smtp, 'RSET'.PHP_EOL);
		if ($this->getCode() != 250)
		{
			throw new systemException('Błąd: komenda RSET nie została zaakceptowana przez serwer SMTP.');
		}

		return TRUE;
	}

	public function sendFrom($from)
	{
		if ($this->isConnected())
		{
			fwrite($this->_smtp, 'MAIL FROM:<'.$from.'>'.PHP_EOL);
			if ($this->getCode() != 250)
			{
				throw new systemException('Błąd: Nadawca wiadomości został odrzucony przez serwer SMTP.');
			}

			return TRUE;
		}

		return FALSE;
	}

	public function sendRecipient($to)
	{
		if ($this->isConnected())
		{
			if ($this->_to_is_array)
			{
				foreach ($to as $rcpt)
				{
					fwrite($this->_smtp, 'RCPT TO:<'.$rcpt.'>'.PHP_EOL);
					if ($this->getCode() != 250 && $this->getCode() != 251)
					{
						if ($this->_exception)
						{
							throw new systemException('Błąd: Odbiorca niezaakceptowany przez serwer SMTP.');
						}
						
						return FALSE;
					}
				}
			}
			else
			{
				fwrite($this->_smtp, 'RCPT TO:<'.$to.'>'.PHP_EOL);
				if ($this->getCode() != 250 && $this->getCode() != 251)
				{
					if ($this->_exception)
					{
						throw new systemException('Błąd: Odbiorca niezaakceptowany przez serwer SMTP.');
					}
					
					return FALSE;
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	public function sendMessage($headers, $message)
	{
		if ($this->isConnected())
		{
			fwrite($this->_smtp, 'DATA'.PHP_EOL);
			if ($this->getCode() != 354)
			{
				throw new systemException('Błąd: Komenda DATA niezaakceptowana przez serwer SMTP.');
			}

			/** server ready for work ;) **/

			// Wysyłanie wiadomości
			fwrite($this->_smtp,$headers.PHP_EOL.$message.PHP_EOL);

			// Informowanie serwera SMTP, że wiadomość przesłana już w całości
			fwrite($this->_smtp, PHP_EOL. "." . PHP_EOL);
			if ($this->getCode() != 250)
			{
				throw new systemException('Błąd: Komenda kończąca wymianę treści nie została zaakceptowana przez serwer SMTP.');
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Wysyłanie pierwszego żądania po nawiązaniu połączenia.
	 * Nie rzuca wyjątkiem, gdyż jest on wywoływany poza tą metodą
	 * dopiero po dwukrotnym zwróceniu FALSE.
	 */
	protected function sendHello($hello, $host)
	{
		if ($this->isConnected())
		{
			fwrite($this->_smtp, $hello.' '.$host.PHP_EOL);

			if ($this->getCode() != 250)
			{
				return FALSE;
			}

			return TRUE;
		}

		return FALSE;
	}

	// Zamyka połączenie z SMTP
	protected function quit()
	{
		if ($this->isConnected())
		{
			fwrite($this->_smtp, 'quit'.PHP_EOL);
			if ($this->getCode() != 221)
			{
				throw new systemException('Błąd! Połączenie z serwerem nie może zostać zamknięte.');
			}

			return TRUE;
		}

		return FALSE;
	}
}